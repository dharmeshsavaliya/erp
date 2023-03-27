<?php
/**
 * Created by PhpStorm.
 * User: mustafaflexwala
 * Date: 13/10/18
 * Time: 5:52 PM
 */

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use App\Loggers\TranslateLog;
use Exception;
use Google\Cloud\Translate\V2\TranslateClient;

class GoogleTranslate
{
    protected $path;

    public function __construct()
    {
        $this->path = public_path().'/google/translation_key.json';
    }

    public function translate($target, $text, $throwException = false)
    {
        $lastFileId = '';
        someLine:
        try {
            $file = googleTraslationSettings::select('id', 'account_json')
            ->where('status', '1')
            ->orderBy('id')
            ->first();

            // on production site it will return the original text
            // if(env("IS_SITE","local") != "production") {
            //     return $text;
            // }
            if (! empty($file)) {
                $jsonArray = (array) json_decode($file->account_json);
                $lastFileId = $file->id;
                $keyFileArray = [
                    'keyFile' => $jsonArray,
                ];

                $translate = new TranslateClient($keyFileArray);

                // echo $target." ".$text;
                $result = $translate->translate($text, [
                    'target' => $target,
                ]);
                // \Log::info(print_r(['Result of google', $result], true));

                return $result['text'];
            } else {
                // $translate = new TranslateClient([
                //     'keyFile' => json_decode(file_get_contents($this->path), true)
                // ]);
                $translateLog = TranslateLog::log([
                    'google_traslation_settings_id' => 0,
                    'messages' => 'Not any account found',
                    'code' => 404,
                    'domain' => ' ',
                    'reason' => ' ',
                ]);

                if ($throwException) {
                    throw new Exception("You have no google translation account enabled.", 500);
                }
            }
        } catch (\Google\Cloud\Core\Exception\ServiceException $e) {
            // \Log::info("-----------------");
            // \Log::info(json_decode($e));
            // \Log::info($e->getServiceException());
            \Log::error($e);
            $message = json_decode($e->getMessage());
            $errorMessage = "";

            if (isset($message) && isset($message->error)) {
                $errorMessage = $message->error->message;
                $translateLog = TranslateLog::log([
                    'google_traslation_settings_id' => (! empty($lastFileId)) ? $lastFileId : 0,
                    'messages' => $message->error->message,
                    'code' => $message->error->code,
                    'domain' => $message->error->errors[0]->domain,
                    'reason' => $message->error->errors[0]->reason,
                ]);
                // $translateLog = TranslateLog::log(["google_traslation_settings_id" => (!empty($lastFileId)), "messages" => $flow["name"] . " has found total Action  : " . $flowActions->count()]);
            } else {
                // Sensitive error message
                $errorMessage = "Something went wrong while translating.";
                $translateLog = TranslateLog::log([
                    'google_traslation_settings_id' => (! empty($lastFileId)) ? $lastFileId : 0,
                    'messages' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'domain' => " ",
                    'reason' => " ",
                ]);
            }
            if (! empty($lastFileId)) {
                $googleTraslationSettings = new googleTraslationSettings;
                $googleTraslationSettings->where('id', $lastFileId)
                ->limit(1)
                ->update([
                    'status' => 0,
                ]);
            }
            $file = googleTraslationSettings::select('id', 'account_json')
            ->where('status', '1')
            ->orderBy('id')
            ->first();
            if (! empty($file)) {
                goto someLine;
            }

            if ($throwException) {
                throw new Exception($errorMessage ?? "");
            }
        } catch (Exception $e) {
            \Log::error($e);
            $code = $e->getCode();

            if(isset($lastFileId)) {
                $translateLog = TranslateLog::log([
                    'google_traslation_settings_id' => (! empty($lastFileId)) ? $lastFileId : 0,
                    'messages' => $e->getMessage(),
                    'code' => 404,
                    'domain' => " ",
                    'reason' => " ",
                ]);
                $googleTraslationSettings = new googleTraslationSettings;
                $googleTraslationSettings->where('id', $lastFileId)
                ->limit(1)
                ->update([
                    'status' => 0,
                ]);
            }
            if ($throwException) {
                if($code == 500) {
                    throw new Exception($e->getMessage());
                } else {
                    throw new Exception("Something went wrong while translating.");
                }
            }
        }
    }

    public function detectLanguage($text)
    {
        try {
            $file = googleTraslationSettings::select('id', 'account_json')
            ->where('status', '1')
            ->orderBy('id')
            ->first();

            if (! empty($file)) {
                $jsonArray = (array) json_decode($file->account_json);
                $lastFileId = $file->id;
                $keyFileArray = [
                    'keyFile' => $jsonArray,
                ];

                $translate = new TranslateClient($keyFileArray);
                $result = $translate->detectLanguage($text);

                return $result;
            }
        } catch (\Google\Cloud\Core\Exception\ServiceException $e) {
            \Log::error($e);
            $message = json_decode($e->getMessage());
            return $message->error;
        }
    }
}
