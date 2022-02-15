<?php

namespace App\Http\Controllers;

use App\BusinessComment;
use App\BusinessPost;
use App\Social\SocialConfig;
use App\SocialContact;
use App\SocialContactThread;
use App\SocialWebhookLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SocialWebhookController extends Controller
{
    /**
     * Verify Facebook and Instagram Webhook URL
     * 
     * @method GET
     * 
     * @param Request $request
     */
    public function verifyWebhook(Request $request)
    {
        $hub = $request->all();
        $verifyToken = $hub['hub_verify_token'];
        $challange = $hub['hub_challenge'];

        SocialWebhookLog::log(SocialWebhookLog::INFO, "Verify Webhook => Webhook Verifying.....", ['token' => $verifyToken, 'challange' => $challange]);

        $countAccount = SocialConfig::where('webhook_token', $verifyToken)->count();

        if ($countAccount == 1) {
            SocialWebhookLog::log(SocialWebhookLog::SUCCESS, "Verify Webhook => Webhook Verified", ['token' => $verifyToken, 'challange' => $challange]);
            echo $challange;
        } else {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, "Verify Webhook => Webhook not Verified", ['token' => $verifyToken, 'challange' => $challange]);
        }
    }

    /**
     * When Subscirbe Event fire on Facebook and Instagram
     * 
     * @method POST
     * 
     * @param Request $request
     */
    public function webhook(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        SocialWebhookLog::log(SocialWebhookLog::INFO, "Webhook => Request Body", ['data' => $data]);

        foreach ($data['entry'] as $entry) {
            if (isset($entry['messaging'])) {
                $this->receiveMessage($entry, $data);
            } else if (isset($entry['changes'])) {
                $this->changes($entry, $data);
            } else {
                SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook => Request Body entry type not found", ['data' => $data]);
            }
        }
    }

    /**
     * Entry type = messaging
     * 
     * @param array $entry
     * @param array $data
     */
    private function receiveMessage($entry, $data)
    {
        foreach ($entry['messaging'] as $message) {

            if (!isset($message['message']['text'])) continue;

            $senderId = $message['sender']['id'];
            $recipientId = $message['recipient']['id'];
            $type = SocialContactThread::RECEIVE;
            $senderAccount = SocialConfig::where('account_id', $recipientId)->first();


            if (!$senderAccount) {
                $temp = $senderId;
                $senderId = $recipientId;
                $recipientId = $temp;
                $type = SocialContactThread::SEND;
            }

            $messageId = $message['message']['mid'];
            $text = $message['message']['text'];
            $socialAccountId = $entry['id'];
            $sendingAt = Carbon::createFromTimestampMs($message['timestamp'])->toDateTimeString();
            $account = SocialConfig::where('account_id', $recipientId)->first();

            if ($account) {
                $object = null;
                if ($data['object'] == SocialContact::TEXT_INSTA) {
                    $object = SocialContact::INSTAGRAM;
                } else if ($data['object'] == SocialContact::TEXT_FB) {
                    $object = SocialContact::FACEBOOK;
                }
                if ($object) {
                    $user = SocialContact::where('account_id', $senderId)->where('platform', $object)->first();
                    if (!$user) {
                        $result = $this->getUserInfo($senderId, $account->page_token);
                        $response = $result['response'];
                        $httpcode = $result['http_code'];

                        SocialWebhookLog::log(SocialWebhookLog::INFO, "Webhook (Receive Message) => Fetched user details using Page access Token", ['response' => $response, 'object' => $data['object'], 'data' => $data]);

                        if ($httpcode == 200) {
                            $user = SocialContact::create([
                                'account_id' => $senderId,
                                'social_config_id' => $account->id,
                                'name' => $response['name'],
                                'platform' => $object
                            ]);

                            SocialWebhookLog::log(SocialWebhookLog::INFO, "Webhook (Receive Message) => New user create", ['id' => $senderId, 'object' => $data['object'], 'data' => $data]);
                        }
                    }

                    if ($user) {
                        SocialContactThread::create([
                            'social_contact_id' => $user->id,
                            'message_id' => $messageId,
                            'sender_id' => $message['sender']['id'],
                            'recipient_id' => $message['recipient']['id'],
                            'text' => $text,
                            'type' => $type,
                            'sending_at' => $sendingAt
                        ]);

                        SocialWebhookLog::log(SocialWebhookLog::SUCCESS, "Webhook (Receive Message) => Message Received", ['mid' => $messageId, 'object' => $data['object'], 'data' => $data]);
                    } else {
                        SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (Receive Message) => User not found", ['id' => $senderId, 'object' => $data['object'], 'data' => $data]);
                    }
                } else {
                    SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (Receive Message) => Object Type not found", ['object' => $data['object'], 'data' => $data, 'object' => $data['object'], 'data' => $data]);
                }
            } else {
                SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (Receive Message) => Account not found", ['id' => $socialAccountId, 'object' => $data['object'], 'data' => $data]);
            }
        }
    }

    /**
     * When Status, Photo, Video or Comment Post on Page Feed
     * 
     * @param array $entry
     * @param array $data
     */
    private function changes($entry, $data)
    {
        foreach ($entry['changes'] as $change) {
            if ($change['field'] == BusinessPost::FEED && $change['value']['item'] == BusinessPost::STATUS) {
                $this->statusUploaded($change, $entry, $data);
            } else if ($change['field'] == BusinessPost::FEED && $change['value']['item'] == BusinessPost::PHOTO) {
                $this->photoUploaded($change, $entry, $data);
            } else if ($change['field'] == BusinessPost::FEED && $change['value']['item'] == BusinessPost::VIDEO) {
                $this->videoUploaded($change, $entry, $data);
            } else if ($change['field'] == BusinessPost::FEED && $change['value']['item'] == BusinessPost::COMMENT) {
                $this->commentOnPostFB($change, $entry, $data);
            } else if ($change['field'] == BusinessPost::COMMENTS && $data['object'] == SocialContact::TEXT_INSTA) {
                $this->commentOnPostIG($change, $entry, $data);
            } else {
                SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook => No changes requested found", ['data' => $data]);
            }
        }
    }

    /**
     * Status Upload
     * 
     * @param array $changes
     * @param array $entry
     * @param array $data
     * 
     * @throws Exception
     */
    private function statusUploaded($changes, $entry, $data)
    {
        try {
            $socialConfig = SocialConfig::where('account_id', $entry['id'])->firstOrFail();

            $post = BusinessPost::updateOrCreate(
                [
                    'post_id' => $changes['value']['post_id']
                ],
                [
                    'social_config_id' => $socialConfig->id,
                    'message' => $changes['value']['message'] ?? NULL,
                    'item' => BusinessPost::STATUS,
                    'verb' => $changes['value']['verb'],
                    'time' => Carbon::createFromTimestamp($changes['value']['created_time'])->toDateTimeString()
                ]
            );

            SocialWebhookLog::log(SocialWebhookLog::SUCCESS, "Webhook (Status Upload) => Status upload Successfully", ['data' => $data, 'post' => $post->toArray()]);
        } catch (\Exception $e) {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (Status Upload) => {$e->getMessage()}", ['data' => $data]);
        }
    }

    /**
     * Photo Upload
     *
     * @param array $changes
     * @param array $entry
     * @param array $data
     * 
     * @throws Exception
     */
    private function photoUploaded($changes, $entry, $data)
    {
        try {
            $socialConfig = SocialConfig::where('account_id', $entry['id'])->firstOrFail();

            $post = BusinessPost::updateOrCreate(
                [
                    'post_id' => $changes['value']['post_id']
                ],
                [
                    'social_config_id' => $socialConfig->id,
                    'message' => $changes['value']['message'] ?? NULL,
                    'item' => BusinessPost::PHOTO,
                    'verb' => $changes['value']['verb'],
                    'time' => Carbon::createFromTimestamp($changes['value']['created_time'])->toDateTimeString()
                ]
            );

            SocialWebhookLog::log(SocialWebhookLog::SUCCESS, "Webhook (Photo Upload) => Photo upload Successfully", ['data' => $data, 'post' => $post->toArray()]);
        } catch (\Exception $e) {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (Photo Upload) => {$e->getMessage()}", ['data' => $data]);
        }
    }

    /**
     * Video Upload
     * 
     * @param array $changes
     * @param array $entry
     * @param array $data
     * 
     * @throws Exception
     */
    private function videoUploaded($changes, $entry, $data)
    {
        try {
            $socialConfig = SocialConfig::where('account_id', $entry['id'])->firstOrFail();

            $post = BusinessPost::updateOrCreate(
                [
                    'post_id' => $changes['value']['post_id']
                ],
                [
                    'social_config_id' => $socialConfig->id,
                    'message' => $changes['value']['message'] ?? NULL,
                    'item' => BusinessPost::VIDEO,
                    'verb' => $changes['value']['verb'],
                    'time' => Carbon::createFromTimestamp($changes['value']['created_time'])->toDateTimeString()
                ]
            );

            SocialWebhookLog::log(SocialWebhookLog::SUCCESS, "Webhook (Video Upload) => Video upload Successfully", ['data' => $data, 'post' => $post->toArray()]);
        } catch (\Exception $e) {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (Video Upload) => {$e->getMessage()}", ['data' => $data]);
        }
    }

    /**
     * Comment on Post (FB)
     *  
     * @param array $changes
     * @param array $entry
     * @param array $data
     * 
     * @throws Exception
     */
    private function commentOnPostFB($changes, $entry, $data)
    {
        try {
            $isAdminComment = 1;
            $isParent = 0;
            $fromId = $changes['value']['from']['id'];
            $pageId = $entry['id'];
            $socialConfig = SocialConfig::where('account_id', $pageId)->firstOrFail();

            if ($pageId !== $fromId) {
                $socialContact = SocialContact::where('account_id', $fromId)->first();
                if (!$socialContact) {
                    $socialContact = SocialContact::create([
                        'account_id' => $fromId,
                        'social_config_id' => $socialConfig->id,
                        'name' => $changes['value']['from']['name'],
                        'platform' => SocialContact::FACEBOOK
                    ]);
                }
                $socialConfig = $socialContact;
                $isAdminComment = 0;
            }

            if ($changes['value']['parent_id'] !== $changes['value']['post_id']) $isParent = 1;

            BusinessComment::updateOrCreate(
                [
                    'comment_id' => $changes['value']['comment_id']
                ],
                [
                    'post_id' => $changes['value']['post_id'],
                    'is_admin_comment' => $isAdminComment,
                    'social_contact_id' => $socialConfig->id,
                    'message' => $changes['value']['message'] ?? NULL,
                    'photo' => $changes['value']['photo'] ?? NULL,
                    'is_parent' => $isParent,
                    'parent_comment_id' => $changes['value']['parent_id'] ?? NULL,
                    'verb' => $changes['value']['verb'],
                    'time' => Carbon::createFromTimestamp($changes['value']['created_time'])->toDateTimeString()
                ]
            );

            SocialWebhookLog::log(SocialWebhookLog::INFO, "Webhook (FB - Post Comment) => Comment Added Successfully", ['data' => $data]);
        } catch (\Exception $e) {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (IG - Post Comment) => {$e->getMessage()}", ['data' => $data]);
        }
    }

    /**
     * Comment on Post (IG)
     *  
     * @param array $changes
     * @param array $entry
     * @param array $data
     * 
     * @throws Exception
     */
    private function commentOnPostIG($changes, $entry, $data)
    {
        try {
            $isAdminComment = 1;
            $isParent = 0;
            $fromId = $changes['value']['from']['id'];
            $pageId = $entry['id'];
            $socialConfig = SocialConfig::where('account_id', $pageId)->firstOrFail();
            $mediaId = $changes['value']['media']['id'];
            $igMedia = BusinessPost::find($mediaId);
            if (!$igMedia) {
                $response = $this->getIGMedia($mediaId, $socialConfig->page_token);
                
                if ($response['http_code'] == 200) {
                    BusinessPost::create([
                        'post_id' => $mediaId,
                        'social_config_id' => $socialConfig->id,
                        'message' => $response['response']['caption'] ?? NULL,
                        'item' => $response['response']['media_type'],
                        'verb' => "add",
                        'time' => $response['response']['timestamp']
                    ]);
                } else {
                    throw new \Exception('Media not found');
                }
            }

            if ($pageId !== $fromId) {
                $socialContact = SocialContact::where('account_id', $fromId)->first();
                if (!$socialContact) {
                    $socialContact = SocialContact::create([
                        'account_id' => $fromId,
                        'social_config_id' => $socialConfig->id,
                        'name' => $changes['value']['from']['username'],
                        'platform' => SocialContact::INSTAGRAM
                    ]);
                }
                $socialConfig = $socialContact;
                $isAdminComment = 0;
            }

            if (isset($changes['value']['parent_id']) && ($changes['value']['parent_id'] !== $mediaId)) $isParent = 1;

            BusinessComment::updateOrCreate(
                [
                    'comment_id' => $changes['value']['id']
                ],
                [
                    'post_id' => $mediaId,
                    'is_admin_comment' => $isAdminComment,
                    'social_contact_id' => $socialConfig->id,
                    'message' => $changes['value']['text'] ?? NULL,
                    'is_parent' => $isParent,
                    'parent_comment_id' => $changes['value']['parent_id'] ?? NULL,
                    'verb' => "add",
                    'time' => Carbon::createFromTimestamp($entry['time'])->toDateTimeString()
                ]
            );

            SocialWebhookLog::log(SocialWebhookLog::INFO, "Webhook (IG - Media Comment) => Comment Added Successfully", ['data' => $data]);
        } catch (\Exception $e) {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (IG - Media Comment) => {$e->getMessage()}", ['data' => $data]);
        }
    }

    /**
     * Get Facebook or Instagram User Details
     * 
     * @param int $userId
     * @param string $pageAccessToken
     */
    private function getUserInfo($userId, $pageAccessToken)
    {
        $curl = curl_init();

        $url = sprintf('https://graph.facebook.com/v12.0/%s?fields=%s&access_token=%s', $userId, 'id,name', $pageAccessToken);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = json_decode(curl_exec($curl), true);

        SocialWebhookLog::log(SocialWebhookLog::INFO, "Webhook (Getting User) => Fetched user details using Page access Token", ['response' => $response, 'user_id' => $userId]);

        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        return [
            'response' => $response,
            'http_code' => $httpcode
        ];
    }

    /**
     * Get Instagram Media Details
     * 
     * @param int $userId
     * @param string $pageAccessToken
     */
    private function getIGMedia($mediaId, $pageAccessToken)
    {
        $curl = curl_init();

        $url = sprintf('https://graph.facebook.com/v12.0/%s?fields=%s&access_token=%s', $mediaId, 'caption,media_type,timestamp', $pageAccessToken);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = json_decode(curl_exec($curl), true);

        SocialWebhookLog::log(SocialWebhookLog::INFO, "Webhook (Getting ID Media) => Fetched IG Media using Page access Token", ['response' => $response, 'media_id' => $mediaId]);

        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        return [
            'response' => $response,
            'http_code' => $httpcode
        ];
    }
}