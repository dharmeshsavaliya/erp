<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use App\PushFcmNotification;
use App\StoreWebsite;
use App\FcmToken;
use Carbon\Carbon;
class SendFcmNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fcm:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command For push notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fromdate = date('Y-m-d H:i');
        $newtimestamp = strtotime($fromdate.' + 4 minute');
        $todate =  date('Y-m-d H:i', $newtimestamp);
        $Notifications = PushFcmNotification::select('sw.push_web_key','sw.push_web_id','ft.token','push_fcm_notifications.*')
                            ->leftJoin('fcm_tokens as ft','ft.store_website_id','=','push_fcm_notifications.store_website_id')
                            ->leftJoin('store_websites as sw','sw.id','=','push_fcm_notifications.store_website_id')
                            ->where('ft.token','!=','')
                            ->where('sw.push_web_key','!=','')
                            ->where('sw.push_web_id','!=','')
                            ->whereBetween('push_fcm_notifications.sent_at',[$fromdate,$todate])
                            ->get();
        if(isset($Notifications) && count($Notifications)!=0 &&  is_array($Notifications)){
            foreach($Notifications as $Notification){
                config(['FCM_SERVER_KEY' => $Notification['push_web_key']]);
                config(['FCM_SENDER_ID' => $Notification['push_web_id']]);
                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60 * 20);
                
                $notificationBuilder = new PayloadNotificationBuilder($Notification->title);
                $notificationBuilder->setBody($Notification->body)
                    ->setSound('default');

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData(['icon' => $Notification->icon,'url',$Notification->url]);

                $option = $optionBuilder->build();
                $notification = $notificationBuilder->build();
                $data = $dataBuilder->build();

                $token = $Notification->token;

                $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

                if($downstreamResponse->numberSuccess()){
                    PushFcmNotification::where('id',$Notification->id)->update(['sent_on'=>date('Y-m-d H:i')]);
                    $this->info('Message Sent Succesfully');
                }elseif($downstreamResponse->numberFailure()){
                    $this->info($downstreamResponse->tokensWithError());
                }

            }
        }else{
            $this->info('No notification available for sending at the moment');
        }
    }
}
