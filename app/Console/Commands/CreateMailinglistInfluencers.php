<?php

namespace App\Console\Commands;

use App\Customer;
use App\EmailEvent;
use Illuminate\Console\Command;
use App\Service;
use App\Mailinglist;
use App\MailinglistTemplate;
use  App\Loggers\MailinglistIinfluencersLogs;
use  App\Loggers\MailinglistIinfluencersDetailLogs;
use App\MaillistCustomerHistory;
use DB;
class CreateMailinglistInfluencers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-mailinglist-influencers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is using for create mailing list from influencers ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public $mailList = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		
        $influencers = \App\ScrapInfluencer::where(function($q) {
            $q->orWhere("read_status","!=",1)->orWhereNull('read_status');
        })->where('email', '!=', "")->limit(1)->get();
		MailinglistIinfluencersLogs::log(count($influencers). " influencers found for CreateMailinglistInfluencers on ->".now());
		
	//	$influencers = \App\ScrapInfluencer::where('email', '!=', "")->limit(10)->get();
//		dd($influencers);
		$send_in_blue_apis=$old_names=[];	
		$send_in_blue_account=[];		
		$services=Service::pluck('name','id');
        $websites = \App\StoreWebsite::select('id', 'title', 'mailing_service_id','send_in_blue_api','send_in_blue_account')->where("website_source", "magento")->whereNotNull('mailing_service_id')->where('mailing_service_id', '>', 0)->orderBy('id', 'desc')->get();
		//$websites = \App\StoreWebsite::select('id', 'title', 'mailing_service_id','send_in_blue_api','send_in_blue_account')->where("website_source", "magento")->whereNotNull('mailing_service_id')->where('mailing_service_id', '>', 0)->where('id',1)->orderBy('id', 'desc')->get();
		MailinglistIinfluencersLogs::log(count($websites). " websites found for CreateMailinglistInfluencers on ->".now());

        /*foreach ($influencers as $influencer) {
        $email_list[] = ['email' => $influencer->email, 'name' => $influencer->name, 'platform' => $influencer->platform];
        }*/
	
        foreach ($websites as $website) { 
			$send_in_blue_apis[$website->id]=$website->send_in_blue_api;
			$send_in_blue_account[$website->id]=$website->send_in_blue_account;
			$old_names[$website->id]=$website->title;
			$service = Service::find($website->mailing_service_id);
			
			
			if($service){
				$name = $website->title;
				if ($name != '') {
					$name = $name . "_" . date("d_m_Y");
					$old_name=$name."_"."old_list";
				} else {
					$name = 'WELCOME_LIST_' . date("d_m_Y");
					$old_name="WELCOME_LIST"."_"."old_list";
				}
				MailinglistIinfluencersLogs::log( " service  for this website is  -->".$service->name);

				$mailingList = \App\Mailinglist::where('name', $name)->where('service_id', $website->mailing_service_id)->where("website_id", $website->id)->where('remote_id', ">", 0)->first();
				
				if (!$mailingList) {
					MailinglistIinfluencersLogs::log( " mailingList  not found for website -->".$website->title);
	
					$mailList = \App\Mailinglist::create([
						'name'       => $name,
						'website_id' => $website->id,
						'service_id' => $website->mailing_service_id,
						'send_in_blue_api'=>$website->send_in_blue_api,
						'send_in_blue_account'=>$website->send_in_blue_account,
					]);

					MailinglistIinfluencersLogs::log( "mailingList  created with name  -->".$name);
					$mailListLogID = MailinglistIinfluencersLogs::log( "mailingList  created with id  -->".$mailList->id);
					
					if (strpos(strtolower($service->name), strtolower('SendInBlue')) !== false) { 
						$mailListLogID = MailinglistIinfluencersLogs::log( "come to SendInBlue");
						$url="https://api.sendinblue.com/v3/contacts/lists";
						$req= [
							"folderId" => 1,
							"name"     => $name,
						];
						$response = $this->callApi("https://api.sendinblue.com/v3/contacts/lists", "POST", $data = [
							"folderId" => 1,
							"name"     => $name,
						],$website->send_in_blue_api);
						
						if (isset($response->id)) {
							$mailList->remote_id = $response->id;
							$mailList->save();
							$this->mailList[] = $mailList;
						}
					} else if(strpos($service->name, 'AcelleMail') !== false) {
						$mailListLogID = MailinglistIinfluencersLogs::log( "come to AcelleMail");
						$curl = curl_init();
						$url="https://acelle.theluxuryunlimited.com/api/v1/lists?api_token=".config('env.ACELLE_MAIL_API_TOKEN');
						$req= array('contact[company]' => '.','contact[state]' => 'afdf','name' =>  $name,'default_subject'=> $name,'from_email' => 'welcome@test.com','from_name' => 'dsfsd','contact[address_1]' => 'af','contact[country_id]' => '219','contact[city]' => 'sdf','contact[zip]' => 'd','contact[phone]' => 'd','contact[email]' => 'welcome@test.com');
						curl_setopt_array($curl, array(
						CURLOPT_URL => "https://acelle.theluxuryunlimited.com/api/v1/lists?api_token=".config('env.ACELLE_MAIL_API_TOKEN'),
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_ENCODING => "",
						  CURLOPT_MAXREDIRS => 10,
						  CURLOPT_TIMEOUT => 0,
						  CURLOPT_FOLLOWLOCATION => true,
						  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						  CURLOPT_CUSTOMREQUEST => "POST",
						  CURLOPT_POSTFIELDS => array('contact[company]' => '.','contact[state]' => 'afdf','name' =>  $name,'default_subject'=> $name,'from_email' => 'welcome@test.com','from_name' => 'dsfsd','contact[address_1]' => 'af','contact[country_id]' => '219','contact[city]' => 'sdf','contact[zip]' => 'd','contact[phone]' => 'd','contact[email]' => 'welcome@test.com'),
						));

						$response = curl_exec($curl);
						 
						curl_close($curl); 
						$res = json_decode($response); 
						if($res->status == 1){
							//getting last id
							$list = Mailinglist::orderBy('id','desc')->first();
							if($list){
								$id = ($list->id + 1);
							}else{
								$id = 1;
							}
							if (isset($res->list_uid)) {
								$mailList->remote_id = $res->list_uid;
								$mailList->save();
								$this->mailList[] = $mailList;
							}
							
						}   
					}
					MailinglistIinfluencersDetailLogs::Create([
						"service"=>$service->name,
						"maillist_id"=>$mailList->id,
						 "url"=>$url,
						"request_data"=>json_encode($req),
						"response_data"=>json_encode($response),
						'message'=>'Mailist created'

					]);
					$this->mailList[] = $mailList;
				}else{
					MailinglistIinfluencersLogs::log( " mailList  for this website is  -->".$mailingList->id);

					$this->mailList[] = $mailingList;
				}
            }
			else{
				MailinglistIinfluencersLogs::log( " service  is not found for  this website");
			}
        }


        if (!empty($influencers) && !empty($this->mailList)) {  
			MailinglistIinfluencersLogs::log( "find influencers and mailList");
            $webListIds=$listIds = [];
            // if (!empty($this->mailList)) {
            //     foreach ($this->mailList as $mllist) {
            //         $listIds[] = $mllist->remote_id;
            //      //   $listIds[] = intval($mllist->remote_id);
			// 		$webListIds[$mllist->website_id][] = $mllist->remote_id;
            //     }
            // }
			
            foreach ($influencers as $list) {
				MailinglistIinfluencersLogs::log( "find influencers".json_encode($list));
				foreach( $this->mailList as $mllist){
					MailinglistIinfluencersLogs::log( "find mllist".json_encode($mllist));
					$serviceName = isset($services[$mllist["service_id"]])?$services[$mllist["service_id"]]:0;
						if (strpos(strtolower($serviceName), strtolower('SendInBlue')) !== false) { 
						
							$api_key=isset($send_in_blue_apis[$mllist->website_id])?$send_in_blue_apis[$mllist->website_id]:'';
							$reqData=[
								"email"      => $list->email,
								"listId"    => $mllist->remote_id,
								"attributes" => ['firstname' => $list->name],
								"updateEnabled" => true
							];
							$url="https://api.sendinblue.com/v3/contacts";
							$response = $this->callApi($url, "POST", $reqData,$api_key );
							MailinglistIinfluencersDetailLogs::Create([
								"service"=>$service->name,
								"maillist_id"=>$mllist->id,
								"email"=>$list->email,
								"name"=> $list->name,
								"url"=>$url,
								"request_data"=>json_encode($reqData),
								"response_data"=>json_encode($response),
								'message'=> 'Added contact sendinblue to mailinglist'
	
							]);
							MailinglistIinfluencersLogs::log( "Added contact sendinblue to mailinglist");
						}else if(strpos($serviceName, 'AcelleMail') !== false) {
						//Assign Customer to list
						
							$curl = curl_init();
	
							$ch = curl_init();
							$url='https://acelle.theluxuryunlimited.com/api/v1/subscribers?list_uid='.$mllist->remote_id;
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_POST, 1);
							curl_setopt($ch, CURLOPT_POSTFIELDS, "api_token=".config('env.ACELLE_MAIL_API_TOKEN')."&EMAIL=".$list->email);
	
							$headers = array();
							$headers[] = 'Accept: application/json';
							$headers[] = 'Content-Type: application/x-www-form-urlencoded';
							curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	
							$response = curl_exec($ch);
							MailinglistIinfluencersDetailLogs::log([
								"service"=>$service->name,
								"maillist_id"=>$mllist->id,
								"email"=>$list->email,
								"name"=> $list->name,
								"url"=>$url,
								"request_data"=>"api_token=".config('env.ACELLE_MAIL_API_TOKEN')."&EMAIL=".$list->email,
								"response_data"=>json_encode($response),
	
							]);
							if (curl_errno($ch)) {
								echo 'Error:' . curl_error($ch);
							
							curl_close($ch);
							
						}
					}
				}
                

                $customer = Customer::where('email', $list->email)->first();
                if (!$customer) {
                    $customer = Customer::create([
                        "name"   => $list->name,
                        "email"  => $list->email,
                        "source" => "scrap_influencer",
                    ]);
					MailinglistIinfluencersLogs::log( $list->email. "Customer created");
                }else{
					MailinglistIinfluencersLogs::log( $list->email. "Customer find");
				}
				
				
				
                $mailing_item = MailinglistTemplate::where('auto_send', 1)->where('duration', 0)->first();
				if (!empty($this->mailList)) {
					MailinglistIinfluencersLogs::log( "Get Mailing Item".json_encode($mailing_item));
                    foreach ($this->mailList as $mllist) {

                        $mllist->listCustomers()->attach($customer->id);
						MailinglistIinfluencersLogs::log( $list->email. "Addded to ".$mllist->name);

						$maillist_customer_history = new MaillistCustomerHistory;
						$maillist_customer_history->customer_id = $customer->id;
						$maillist_customer_history->attribute = 'maillist';
						$maillist_customer_history->new_value =  $mllist->id;
					     $maillist_customer_history->save();
						
						$list_contact_id = \DB::table('list_contacts')->where(['list_id'=>$mllist->id, "customer_id"=>$customer->id])->pluck('id')->first();					
							    
						/*** send welcome email to mailing list customers start**/
						if($mailing_item != null and !empty($mailing_item['static_template'])) { 
							$mllist['email'] = $list->email;
							$mllist['name'] = $list->name;
							MailinglistIinfluencersLogs::log( $list->email. "email send  to ".$mailing_item['static_template']->name);
							(new Mailinglist)->sendAutoEmails($mllist, $mailing_item, $service);
						}	
						/*** send welcome email to mailing list customers end**/						
                    }

					// Added Code for the create maillist with backup
					foreach ($this->mailList as $mllist) {
						if (strpos(strtolower($services[$mllist->service_id]), strtolower('SendInBlue')) !== false) { 
							$old_name = $old_names[$mllist->website_id]."_old_list";
							//dd($old_name);
							$oldmailList = \App\Mailinglist::where('name', $old_name)->where("website_id", $mllist->website_id)->first();
							if (!$oldmailList ) {
								\Log::info("come to create");
								\Log::info($old_name);
								$oldmailList =\App\Mailinglist::create([
									'name'       => $old_name,
									'website_id' =>$mllist->website_id,
									'service_id' => $mllist->service_id,
									'send_in_blue_api'=>$mllist->send_in_blue_api,
									'send_in_blue_account'=>$mllist->send_in_blue_account,
								]);
								\Log::info($oldmailList);
								
								MailinglistIinfluencersLogs::log( "New Mailing listcreated ->".$old_name);
							
								$response = $this->callApi("https://api.sendinblue.com/v3/contacts/lists", "POST", $data = [
									"folderId" => 1,
									"name"     => $old_name,
								],$mllist->send_in_blue_api);
								
								if (isset($response->id)) {
									$oldmailList->remote_id = $response->id;
									$oldmailList->save();
								}
								MailinglistIinfluencersDetailLogs::log([
									"service"=>$service->name,
									"maillist_id"=>$oldmailList->id,
									"email"=>$list->email,
									"name"=> $list->name,
									"url"=>"https://api.sendinblue.com/v3/contacts/lists",
									"request_data"=>json_encode([
										"folderId" => 1,
										"name"     => $name,
									]),
									"response_data"=>json_encode($response),
									"message"=>"Added customer $list->email to Mailist $old_name"
		
								]);
							}
							\Log::info($oldmailList);
									$api_key=isset($send_in_blue_apis[$mllist->website_id])?$send_in_blue_apis[$oldmailList->website_id]:'';
									$reqData=[
										"email"      => $list->email,
										"listIds"    => $mllist->remote_id ,
																		];
									$url="https://api.sendinblue.com/v3/contacts/lists/listId/contacts/remove";
									$response = $this->callApi($url, "POST", $reqData,$api_key );
									MailinglistIinfluencersDetailLogs::log([
										"service"=>$service->name,
										"maillist_id"=>$mllist->id,
										"email"=>$list->email,
										"name"=> $list->name,
										"url"=>$url,
										"request_data"=>json_encode($reqData),
										"response_data"=>json_encode($response),
										"message"=>"Removed customer  from $list->email to Mailist $mllist->name"
			
									]); 	

									$reqData=[
										"email"      => $list->email,
										"listIds"    => $oldmailList->remote_id ,
										"attributes" => ['firstname' => $list->name],
										"updateEnabled" => true
									];
									$url="https://api.sendinblue.com/v3/contacts";
									$response = $this->callApi($url, "POST", $reqData,$api_key );
									MailinglistIinfluencersDetailLogs::log([
										"service"=>$service->name,
										"maillist_id"=>$mllist->id,
										"email"=>$list->email,
										"name"=> $list->name,
										"url"=>$url,
										"request_data"=>json_encode($reqData),
										"response_data"=>json_encode($response),
										"message"=>"added customer   $list->email to Mailist $oldmailList->name"
			
									]);
						//	foreach ($mllist->listCustomers() as $customer_id){
								$oldmailList->listCustomers()->attach($customer->id);
								$mllist->listCustomers()->detach($customer->id);
								$maillist_customer_history = new MaillistCustomerHistory;
								$maillist_customer_history->customer_id = $customer->id;
								$maillist_customer_history->attribute = 'maillist';
								$maillist_customer_history->old_value =  $mllist->id;
								$maillist_customer_history->new_value =  $oldmailList->id;
								 $maillist_customer_history->save();

						//	}
						
											
							
						}
					}
		
                }

                $list->read_status = 1;
                $list->save();
            }
			
        }
    }

    public function callApi($url, $method, $data = [],$send_in_blue_api="")
    {
        $curl = curl_init();
		$api_key=($send_in_blue_api=="")? getenv('SEND_IN_BLUE_API'):$send_in_blue_api;
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => array(
                "api-key: " . $api_key,
                "Content-Type: application/json",
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        \Log::info($response);
        return json_decode($response);
    }
	
	
}
