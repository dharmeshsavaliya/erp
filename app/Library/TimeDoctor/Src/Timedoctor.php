<?php

namespace App\Library\TimeDoctor\Src;
use App\TimeDoctor\TimeDoctorAccount;
use App\TimeDoctor\TimeDoctorProject;
use App\TimeDoctor\TimeDoctorTask;
use App\TimeDoctor\TimeDoctorMember;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;

class Timedoctor
{
    protected static $instance = null;

    private $accessToken;
    

    public function __construct()
    {
        // $this->SEED_REFRESH_TOKEN = getenv('HUBSTAFF_SEED_PERSONAL_TOKEN');        
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Timedoctor();
        }

        return self::$instance;
    }

    public function generateAuthToken($account_id){
        $getTimeDoctorAccount = TimeDoctorAccount::find( $account_id );        
        $timedoctor = Timedoctor::getInstance();
        $url = 'https://api2.timedoctor.com/api/1.0/authorization/login';
        try{
            $httpClient = new Client();
            $response = $httpClient->post(
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Content-Type' => 'application/json',
                    ],
                    RequestOptions::BODY => json_encode([
                        'email' => $getTimeDoctorAccount->time_doctor_email,
                        'password' => $getTimeDoctorAccount->time_doctor_password,
                        'permissions' => 'write',
                    ]),
                ]
            );
            $parsedResponse = json_decode($response->getBody()->getContents());
            $getTimeDoctorAccount->auth_token = $parsedResponse->data->token;
            $getTimeDoctorAccount->company_id = $parsedResponse->data->companies[0]->id;
            if ($getTimeDoctorAccount->save()) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function authenticate($generate = true, $access_token = '')
    {
        /*if ($generate) {
            $token = new Token();
            $token->getAuthToken($this->SEED_REFRESH_TOKEN, $this->HUBSTAFF_TOKEN_FILE_NAME);
        }*/

        $this->accessToken = $access_token;

        return $this;
    }

    public function getMemberList($company_id, $access_token){
        $url = 'https://api2.timedoctor.com/api/1.0/users?company='.$company_id.'&token='.$access_token;
        $httpClient = new Client();
        $response = $httpClient->get( $url );
        $parsedResponse = json_decode($response->getBody()->getContents());
        return $parsedResponse;
    }

    public function getProjectList($company_id, $access_token){
        $url = 'https://api2.timedoctor.com/api/1.0/projects?company='.$company_id.'&token='.$access_token;
        $httpClient = new Client();
        $response = $httpClient->get( $url );
        $parsedResponse = json_decode($response->getBody()->getContents());
        return $parsedResponse;
    }

    public function createProject($company_id, $access_token,$project_data){
        try{
            $url = 'https://api2.timedoctor.com/api/1.0/projects?company='.$company_id.'&token='.$access_token;
            $httpClient = new Client();
            $response = $httpClient->post(
                $url,
                [
                    RequestOptions::HEADERS => [                    
                        'Content-Type' => 'application/json',
                    ],

                    RequestOptions::BODY => json_encode([
                        'name' => $project_data['time_doctor_project_name'],
                        'description' => $project_data['time_doctor_project_description'],
                    ]),
                ]
            );
            $parsedResponse = json_decode($response->getBody());
            if($response->getStatusCode() == 200){
                return true;
            } else {
                return false;
            }

        } catch (Exception $e) {
            return false;
        }
    }

    public function getTaskList($company_id, $access_token){        
        $url = 'https://api2.timedoctor.com/api/1.0/tasks?company='.$company_id.'&token='.$access_token;
        $httpClient = new Client();
        $response = $httpClient->get( $url );
        $parsedResponse = json_decode($response->getBody()->getContents());
        return $parsedResponse;
    }

    public function createTask($company_id, $access_token,$project_data){
        try{
            $url = 'https://api2.timedoctor.com/api/1.0/tasks?company='.$company_id.'&token='.$access_token;
            $httpClient = new Client();
            $response = $httpClient->post(
                $url,
                [
                    RequestOptions::HEADERS => [                    
                        'Content-Type' => 'application/json',
                    ],

                    RequestOptions::BODY => json_encode([
                        'project' =>  ['id'=> $project_data['time_doctor_project'],"weight"=> 0 ],
                        'name' => $project_data['time_doctor_task_name'],
                        'description' => $project_data['time_doctor_task_description'],
                    ]),
                ]
            );

            $parsedResponse = json_decode($response->getBody());            
            if($response->getStatusCode() == 200){
                return true;
            } else {
                return false;
            }

        } catch (Exception $e) {
            return false;
        }
    }

    public function createGeneralTask($company_id, $access_token,$project_data){
        try{
            $url = 'https://api2.timedoctor.com/api/1.0/tasks?company='.$company_id.'&token='.$access_token;
            $httpClient = new Client();
            $response = $httpClient->post(
                $url,
                [
                    RequestOptions::HEADERS => [                    
                        'Content-Type' => 'application/json',
                    ],

                    RequestOptions::BODY => json_encode([
                        'project' =>  ['id'=> $project_data['time_doctor_project'],"weight"=> 0 ],
                        'name' => $project_data['time_doctor_task_name'],
                        'description' => $project_data['time_doctor_task_description'],
                    ]),
                ]
            );

            $parsedResponse = json_decode($response->getBody());
            if($response->getStatusCode() == 200){
                return $parsedResponse->data->id;                
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateTask($company_id, $access_token,$project_data){
        try{
            $url = 'https://api2.timedoctor.com/api/1.0/tasks/'.$project_data['taskId'].'?company='.$company_id.'&token='.$access_token;
            $httpClient = new Client();
            $response = $httpClient->put(
                $url,
                [
                    RequestOptions::HEADERS => [                    
                        'Content-Type' => 'application/json',
                    ],

                    RequestOptions::BODY => json_encode([
                        'project' =>  ['id'=> $project_data['taskProject'],"weight"=> 0 ],
                        'name' => $project_data['taskName'],
                        'description' => $project_data['taskDescription'],
                    ]),
                ]
            );
            
            $parsedResponse = json_decode($response->getBody());            
            if($response->getStatusCode() == 200){
                return true;
            } else {
                return false;
            }

        } catch (Exception $e) {
            return false;
        }
    }

    public function updateProject($company_id, $access_token,$project_data){
        try{
            $url = 'https://api2.timedoctor.com/api/1.0/projects/'.$project_data['projectId'].'?company='.$company_id.'&token='.$access_token;
            $httpClient = new Client();
            $response = $httpClient->put(
                $url,
                [
                    RequestOptions::HEADERS => [                    
                        'Content-Type' => 'application/json',
                    ],

                    RequestOptions::BODY => json_encode([                        
                        'name' => $project_data['projectName'],
                        'description' => $project_data['projectDescription'],
                    ]),
                ]
            );            
            $parsedResponse = json_decode($response->getBody());            
            if($response->getStatusCode() == 200){
                return true;
            } else {
                return false;
            }

        } catch (Exception $e) {
            return false;
        }
    }

    public function getActivityListOld($company_id, $access_token, $user_id, $start='', $end=''){                       
        $members = TimeDoctorMember::where('time_doctor_account_id', $user_id)->select('time_doctor_user_id')->get();                
        $memberId = implode(',', array_column($members->toArray(), 'time_doctor_user_id' ) );                
        $end = date('Y-m-d', strtotime($end . ' +1 day'));
        $url = 'https://api2.timedoctor.com/api/1.0/activity/worklog?company='.$company_id.'&user='.$memberId.'&from='.$start.'&to='.$end.'&token='.$access_token;        
        $httpClient = new Client();
        $response = $httpClient->get( $url );        

        $parsedResponse = json_decode($response->getBody()->getContents());
        $activities = [];

        foreach ($parsedResponse->data as $activity_data) {
            foreach($activity_data as $activity){
                $res = [
                    'user_id' => $activity->userId,
                    'task_id' => $activity->taskId,
                    'starts_at' => $activity->start,
                    'tracked' => $activity->time,
                    'project'  => $activity->projectId,
                ];
                $activities[] = $res;
            }
        }        
        return $activities;
    }

    public function getActivityList($company_id, $access_token, $user_id, $start='', $end=''){
        $members = TimeDoctorMember::where('user_id', $user_id)->get();        
        $activities = [];
        foreach($members as $member){

            $end = date('Y-m-d', strtotime($end . ' +1 day'));
            $url = 'https://api2.timedoctor.com/api/1.0/activity/worklog?company='.$member->account_detail->company_id.'&user='.$member->time_doctor_user_id.'&from='.$start.'&to='.$end.'&token='.$member->account_detail->auth_token;        
            $httpClient = new Client();
            $response = $httpClient->get( $url );        

            $parsedResponse = json_decode($response->getBody()->getContents());            

            foreach ($parsedResponse->data as $activity_data) {
                foreach($activity_data as $activity){
                    $res = [
                        'user_id' => $activity->userId,
                        'task_id' => $activity->taskId,
                        'starts_at' => $activity->start,
                        'tracked' => $activity->time,
                        'project'  => $activity->projectId,
                    ];
                    $activities[] = $res;
                }
            }        
        }
        return $activities;
    }

    public function getActivityListCommand($company_id, $access_token, $user_id){        
        $members = TimeDoctorMember::where('user_id', $user_id)->get();        
        /*$start = date('Y-m-d', strtotime('-7 days'));*/
        $start = date('Y-m-d');
        $end = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
        $activities = [];
        foreach($members as $member){            
            $url = 'https://api2.timedoctor.com/api/1.0/activity/worklog?company='.$member->account_detail->company_id.'&user='.$member->time_doctor_user_id.'&from='.$start.'&to='.$end.'&token='.$member->account_detail->auth_token;        
            $httpClient = new Client();
            $response = $httpClient->get( $url );        

            $parsedResponse = json_decode($response->getBody()->getContents());            

            foreach ($parsedResponse->data as $activity_data) {
                foreach($activity_data as $activity){
                    $res = [
                        'user_id' => $activity->userId,
                        'task_id' => $activity->taskId,
                        'starts_at' => $activity->start,
                        'tracked' => $activity->time,
                        'project'  => $activity->projectId,
                    ];
                    $activities[] = $res;
                }
            }        
        }
        return $activities;
    }
}
