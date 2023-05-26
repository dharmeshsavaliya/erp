<?php

namespace App\Http\Controllers\Github;

use Artisan;
use DateTime;
use Exception;
use Carbon\Carbon;
use App\DeveloperTask;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Helpers\GithubTrait;
use Illuminate\Http\Request;
use App\GitMigrationErrorLog;
use App\Helpers\MessageHelper;
use GuzzleHttp\RequestOptions;
use App\Github\GithubRepository;
use App\Github\GithubOrganization;
use App\Github\GithubBranchState;
use App\Http\Controllers\Controller;
use App\DeveoperTaskPullRequestMerge;
use App\Http\Requests\DeleteBranchRequest;
use App\Jobs\DeleteBranches;
use App\Models\DeletedGithubBranchLog;

class RepositoryController extends Controller
{
    use GithubTrait;

    private $client;

    public function __construct()
    {
        $this->client = new Client([
            // 'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')]
            'auth' => [config('env.GITHUB_USERNAME'), config('env.GITHUB_TOKEN')],
        ]);
    }

    private function connectGithubClient($userName, $token)
    {
        $githubClient = new Client([
                // 'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')],
                'auth' => [$userName, $token],
            ]);

        return $githubClient;
    }

    private function refreshGithubRepos($organizationId)
    {
        if(strlen($organizationId) > 0){
            $organization = GithubOrganization::find($organizationId);
        }else{
            $organization = GithubOrganization::where('name', 'MMMagento')->first();
        }

        $dbRepositories = [];

        try{
            if(!empty($organization)){
                // $url = "https://api.github.com/orgs/" . getenv('GITHUB_ORG_ID') . "/repos?per_page=100";
                $url = 'https://api.github.com/orgs/'.$organization->name.'/repos?per_page=100';

                $githubClient = $this->connectGithubClient($organization->username, $organization->token);

                $response = $githubClient->get($url);

                $repositories = json_decode($response->getBody()->getContents());

                foreach ($repositories as $repository) {
                    $data = [
                        'id' => $repository->id,
                        'github_organization_id' => $organization->id,
                        'name' => $repository->name,
                        'html' => $repository->html_url,
                        'webhook' => $repository->hooks_url,
                        'created_at' => Carbon::createFromFormat(DateTime::ISO8601, $repository->created_at),
                        'updated_at' => Carbon::createFromFormat(DateTime::ISO8601, $repository->updated_at),
                    ];

                    GithubRepository::updateOrCreate(
                        [
                            'id' => $repository->id,
                        ],
                        $data
                    );
                    $dbRepositories[] = $data;
                }
            }
        }
        catch(\Exception $e){

        }

        return $dbRepositories;
    }

    //
    public function listRepositories(Request $request, $organizationId = '')
    {
        $repositories = $this->refreshGithubRepos($organizationId);

        $githubOrganizations = GithubOrganization::get();

        if($request->ajax()){
            return response()->json([
                'tbody' => view('github.include.repository-list', compact('repositories'))->render(),
                'count' => count($repositories)
            ], 200);
        }

        return view('github.repositories', [
            'repositories' => $repositories,
            'githubOrganizations' => $githubOrganizations,
            'organizationId' => $organizationId
        ]);
    }

    public function getRepositoryDetails($repositoryId)
    {
        $repository = GithubRepository::find($repositoryId);
        $branches = $repository->branches;

        $currentBranch = exec('/usr/bin/sh ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . $repository->name . '/get_current_deployment.sh');

        //exec('sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').'erp/deploy_branch.sh master');

        //exit;
        return view('github.repository_settings', [
            'repository' => $repository,
            'branches' => $branches,
            'current_branch' => $currentBranch,
        ]);
    }

    public function deployBranch($repoId, Request $request)
    {
        $repository = GithubRepository::find($repoId);
        $organization = $repository->organization;

        $githubClient = $this->connectGithubClient($organization->username, $organization->token);

        //dd($repoId);
        $source = 'master';
        $destination = $request->branch;
        $pullOnly = request('pull_only', 0);

        $url = 'https://api.github.com/repositories/' . $repoId . '/merges';

        try {
            // Merge master into branch
            if (empty($pullOnly) || $pullOnly != 1) {
                $githubClient->post(
                    $url,
                    [
                        RequestOptions::BODY => json_encode([
                            'base' => $destination,
                            'head' => $source,
                        ]),
                    ]
                );
                //Artisan::call('github:load_branch_state');
                if ($source == 'master') {
                    $this->updateBranchState($repoId, $destination);
                } elseif ($destination == 'master') {
                    $this->updateBranchState($repoId, $source);
                }
            }

            // Deploy branch
            $repository = GithubRepository::find($repoId);

            $branch = $request->branch;
            $composerupdate = request('composer', false);

            $cmd = 'sh ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . '/' . $repository->name . '/deploy_branch.sh ' . $branch . ' ' . $composerupdate . ' 2>&1';
            //echo 'sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').'erp/deploy_branch.sh '.$branch;

            $allOutput = [];
            $allOutput[] = $cmd;
            $result = exec($cmd, $allOutput);

            $migrationError = is_array($result) ? json_encode($result) : $result;
            if (Str::contains($migrationError, 'database/migrations') || Str::contains($migrationError, 'migrations') || Str::contains($migrationError, 'Database/Migrations') || Str::contains($migrationError, 'Migrations')) {
                if ($source == 'master') {
                    $this->createGitMigrationErrorLog($repoId, $destination, $migrationError);
                } elseif ($destination == 'master') {
                    $this->createGitMigrationErrorLog($repoId, $source, $migrationError);
                } else {
                    $this->createGitMigrationErrorLog($repoId, $source, $migrationError);
                }
            }
        } catch (Exception $e) {
            print_r($e->getMessage());
            $errorArr = [];
            $errorArr = $e->getMessage();
            if (! is_array($errorArr)) {
                $arrErr[] = $errorArr;
                $errorArr = implode(' ', $arrErr);
            } else {
                $arrErr = $errorArr;
                $errorArr = $errorArr;
            }
            $migrationError = is_array($result) ? json_encode($errorArr) : $errorArr;
            if (Str::contains($migrationError, 'database/migrations') || Str::contains($migrationError, 'migrations') || Str::contains($migrationError, 'Database/Migrations') || Str::contains($migrationError, 'Migrations')) {
                if ($source == 'master') {
                    $this->createGitMigrationErrorLog($repoId, $destination, $migrationError);
                } elseif ($destination == 'master') {
                    $this->createGitMigrationErrorLog($repoId, $source, $migrationError);
                } else {
                    $this->createGitMigrationErrorLog($repoId, $source, $migrationError);
                }
            }

            return redirect(url('/github/pullRequests'))->with(
                [
                    'message' => $e->getMessage(),
                    'alert-type' => 'error',
                ]
            );
        }

        return redirect(url('/github/pullRequests'))->with([
            'message' => print_r($allOutput, true),
            'alert-type' => 'success',
        ]);
    }

    /**
     * Undocumented function
     *
     * @param [mix] $repoId
     * @param [mix] $branchName
     * @param [array] $errorLog
     * @return void
     */
    public function createGitMigrationErrorLog($repoId, $branchName, $errorLog)
    {
        $repository = GithubRepository::find($repoId);
        $organization = $repository->organization;

        $comparison = $this->compareRepoBranches($organization->username, $organization->token, $repoId, $branchName);
        GitMigrationErrorLog::create([
            'github_organization_id' => $organization->id,
            'repository_id' => $repoId,
            'branch_name' => $branchName,
            'ahead_by' => $comparison['ahead_by'],
            'behind_by' => $comparison['behind_by'],
            'last_commit_author_username' => $comparison['last_commit_author_username'],
            'last_commit_time' => $comparison['last_commit_time'],
            'error' => $errorLog,
        ]);
    }

    public function getGitMigrationErrorLog(Request $request)
    {
        if($request->ajax()) {
            $gitDbError = GitMigrationErrorLog::where('repository_id',  $request->repoId)->orderBy('id', 'desc')->take(100)->get();

            return response()->json([
                'tbody' => view('github.include.migration-error-logs-list', compact('gitDbError'))->render(),
                'count' => count($gitDbError)
            ], 200);
        }

        try {
            $githubOrganizations = GithubOrganization::with('repos')->get();

            return view('github.deploy_branch_error', compact('githubOrganizations'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    private function updateBranchState($repoId, $branchName)
    {
        $repository = GithubRepository::find($repoId);
        $organization = $repository->organization;

        $comparison = $this->compareRepoBranches($organization->username, $organization->token, $repoId, $branchName);
        $filters = [
            'state' => 'all',
            'head' => $organization->name.":".$branchName
        ];
        $pullRequests = $this->pullRequests($organization->username, $organization->token, $repoId, $filters);
        if(!empty($pullRequests) && count($pullRequests) > 0){
            $pullRequest = $pullRequests[0];
        }
        \Log::info('Add entry to GithubBranchState');
        GithubBranchState::updateOrCreate(
            [
                'repository_id' => $repoId,
                'branch_name' => $branchName,
            ],
            [
                'github_organization_id' => $organization->id,
                'repository_id' => $repoId,
                'branch_name' => $branchName,
                'status' => ! empty($pullRequest) ? $pullRequest['state'] : '',
                'ahead_by' => $comparison['ahead_by'],
                'behind_by' => $comparison['behind_by'],
                'last_commit_author_username' => $comparison['last_commit_author_username'],
                'last_commit_time' => $comparison['last_commit_time'],
            ]
        );
    }

    private function findDeveloperTask($branchName)
    {
        $devTaskId = null;
        $usIt = explode('-', $branchName);

        if (count($usIt) > 1) {
            $devTaskId = $usIt[1];
        } else {
            $usIt = explode(' ', $branchName);
            if (count($usIt) > 1) {
                $devTaskId = $usIt[1];
            }
        }

        return  DeveloperTask::find($devTaskId);
    }

    private function updateDevTask($branchName, $pull_request_id)
    {
        $devTask = $this->findDeveloperTask($branchName); //DeveloperTask::find($devTaskId);

        \Log::info('updateDevTask call ' . $branchName);

        if ($devTask) {
            \Log::info('updateDevTask find success ' . $branchName);
            try {
                \Log::info('updateDevTask :: PR merge msg send .' . json_encode($devTask->user));

                $message = $branchName . ':: PR has been merged';

                $requestData = new Request();
                $requestData->setMethod('POST');
                $requestData->request->add(['issue_id' => $devTask->id, 'message' => $message, 'status' => 1]);
                app(\App\Http\Controllers\WhatsAppController::class)->sendMessage($requestData, 'issue');

                MessageHelper::sendEmailOrWebhookNotification([$devTask->assigned_to, $devTask->team_lead_id, $devTask->tester_id], $message . '. kindly test task in live if possible and put test result as comment in task.');
                $devTask->update(['is_pr_merged' => 1]);

                $request = new DeveoperTaskPullRequestMerge;
                $request->task_id = $devTask->id;
                $request->pull_request_id = $pull_request_id;
                $request->user_id = auth()->user()->id;
                $request->save();

                //app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($devTask->user->phone, $devTask->user->whatsapp_number, $branchName.':: PR has been merged', false);
            } catch (Exception $e) {
                \Log::info('updateDevTask ::' . $e->getMessage());
                \Log::error('updateDevTask ::' . $e->getMessage());
            }

            $devTask->status = 'In Review';
            $devTask->save();
        }
    }

    public function mergeBranch($id, Request $request)
    {
        $source = $request->source;
        $destination = $request->destination;
        $pull_request_id = $request->task_id;

        $repository = GithubRepository::find($id);
        $organization = $repository->organization;

        $githubClient = $this->connectGithubClient($organization->username, $organization->token);

        $url = 'https://api.github.com/repositories/'.$id.'/merges';

        try {
            $githubClient->post(
                $url,
                [
                    RequestOptions::BODY => json_encode([
                        'base' => $destination,
                        'head' => $source,
                    ]),
                ]
            );
            //Artisan::call('github:load_branch_state');
            if ($source == 'master') {
                $this->updateBranchState($id, $destination);
            } elseif ($destination == 'master') {
                $this->updateBranchState($id, $source);
            }

            \Log::info('updateDevTask calling...' . $source);
            $this->updateDevTask($source, $pull_request_id);

            // Deploy branch
            $repository = GithubRepository::find($id);

            $branch = 'master';
            //echo 'sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').'erp/deploy_branch.sh '.$branch;

            $cmd = 'sh ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . $repository->name . '/deploy_branch.sh ' . $branch . ' 2>&1';
            $allOutput = [];
            $allOutput[] = $cmd;
            $result = exec($cmd, $allOutput);
            \Log::info(print_r($allOutput, true));

            // Used to reset php cache after merge.
            // opcache_reset();

            $sqlIssue = false;
            if (! empty($allOutput) && is_array($allOutput)) {
                foreach ($allOutput as $output) {
                    if (strpos(strtolower($output), 'sqlstate') !== false) {
                        $sqlIssue = true;
                    }
                }
            }
            if ($sqlIssue) {
                $devTask = $this->findDeveloperTask($source);
                if ($devTask) {
                    $message = $source . ':: there is some issue while running migration please check migration or contact administrator';
                    $requestData = new Request();
                    $requestData->setMethod('POST');
                    $requestData->request->add(['issue_id' => $devTask->id, 'message' => $message, 'status' => 1]);
                    app(\App\Http\Controllers\WhatsAppController::class)->sendMessage($requestData, 'issue');
                }

                //Merged to master get migration error
                $migrationError = is_array($allOutput) ? json_encode($allOutput) : $allOutput;
                $this->createGitMigrationErrorLog($id, $source, $migrationError);

                return redirect(url('/github/pullRequests'))->with([
                    'message' => 'Branch merged successfully but migration failed',
                    'alert-type' => 'error',
                ]);
            }
        } catch (Exception $e) {
            \Log::error($e);
            print_r($e->getMessage());
            $devTask = $this->findDeveloperTask($source);
            if ($devTask) {
                $message = $source . ':: Failed to Merge please check branch has not any conflict or contact administrator';
                $requestData = new Request();
                $requestData->setMethod('POST');
                $requestData->request->add(['issue_id' => $devTask->id, 'message' => $message, 'status' => 1]);
                app(\App\Http\Controllers\WhatsAppController::class)->sendMessage($requestData, 'issue');
            }

            return redirect(url('/github/pullRequests'))->with(
                [
                    'message' => 'Failed to Merge please check branch has not any conflict !',
                    'alert-type' => 'error',
                ]
            );
        }

        return redirect(url('/github/pullRequests'))->with([
            'message' => 'Branch merged successfully',
            'alert-type' => 'success',
        ]);
    }

    private function getPullRequests($userName, $token, $repoId, $filters = [])
    {
        $addedFilters = ! empty($filters) ? Arr::query($filters) : '';
        $pullRequests = [];
        $url = 'https://api.github.com/repositories/' . $repoId . '/pulls?per_page=200';
        if (! empty($addedFilters)) {
            $url .= '&' . $addedFilters;
        }
        try {
            $githubClient = $this->connectGithubClient($userName, $token);

            $response = $githubClient->get($url);

            $decodedJson = json_decode($response->getBody()->getContents());
            foreach ($decodedJson as $pullRequest) {
                $pullRequests[] = [
                    'id' => $pullRequest->number,
                    'title' => $pullRequest->title,
                    'number' => $pullRequest->number,
                    'state' => $pullRequest->state,
                    'username' => $pullRequest->user->login,
                    'userId' => $pullRequest->user->id,
                    'updated_at' => $pullRequest->updated_at,
                    'source' => $pullRequest->head->ref,
                    'destination' => $pullRequest->base->ref,
                ];
            }
        } catch (Exception $e) {
        }

        return $pullRequests;
    }

    public function listPullRequests($repoId)
    {
        $repository = GithubRepository::find($repoId);
        $organization = $repository->organization;

        $pullRequests = $this->getPullRequests($organization->username, $organization->token, $repoId);

        $branchNames = array_map(
            function ($pullRequest) {
                return $pullRequest['source'];
            },
            $pullRequests
        );

        $branchStates = GithubBranchState::whereIn('branch_name', $branchNames)->get();

        foreach ($pullRequests as $pullRequest) {
            $pullRequest['branchState'] = $branchStates->first(
                function ($value, $key) use ($pullRequest) {
                    return $value->branch_name == $pullRequest['source'];
                }
            );
        }

        return view('github.repository_pull_requests', [
            'pullRequests' => $pullRequests,
            'repository' => $repository,
        ]);
    }

    public function closePullRequestFromRepo($repositoryId, $pullRequestNumber)
    {
        return $this->closePullRequest($repositoryId, $pullRequestNumber);
    }

    public function deleteBranchFromRepo($repositoryId, DeleteBranchRequest $request)
    {
        $response = $this->deleteBranch($repositoryId, $request->branch_name);
        $githubBranchState = GithubBranchState::where('repository_id', $repositoryId)->where('branch_name', $request->branch_name)->first();
        if (! empty($githubBranchState) && $response['status']) {

            DeletedGithubBranchLog::create([
                'branch_name' => $request->branch_name,
                'repository_id' => $repositoryId,
                'deleted_by'    => \Auth::id(),
                'status'    => 'success'
            ]);

            $githubBranchState->delete();

        }else{
            DeletedGithubBranchLog::create([
                'branch_name' => $request->branch_name,
                'repository_id' => $repositoryId,
                'deleted_by'    => \Auth::id(),
                'status'    => 'failed',
                'error_message' => $response['error']
            ]);
        }

        return $response;
    }

    // devtask - 23311
    public function deleteNumberOfBranchesFromRepo($repositoryId,Request $request){
        $branches = $this->getGithubBranches($repositoryId,[]);
        $numberOfBranchesToDelete = $request->number_of_branches;
        $branchArr = [];

        for($i = 0; $i < $numberOfBranchesToDelete; $i++ ){
            if(isset($branches[$i]->name)){
                $branchArr[$i] = $branches[$i]->name;
            }
        }

        $response = DeleteBranches::dispatch($branchArr,$repositoryId)->onQueue('delete_github_branches');

        return response()->json(['type' => 'success'],200);

    }

    public function actionWorkflows(Request $request, $repositoryId)
    {
        $githubActionRuns = $this->githubActionResult($repositoryId, $request->page);

        return view('github.action_workflows', [
            'githubActionRuns' => $githubActionRuns,
            'repositoryId' => $repositoryId,
        ]);
    }

    public function ajaxActionWorkflows(Request $request, $repositoryId)
    {
        return $this->githubActionResult($repositoryId, $request->page);
    }

    public function githubActionResult($repositoryId, $page, $date = null){
        ini_set('max_execution_time', -1);

        $githubActionRuns = $this->getGithubActionRuns($repositoryId, $page, $date);
        foreach ($githubActionRuns->workflow_runs as $key => $runs) {
            $githubActionRuns->workflow_runs[$key]->failure_reason = '';
            if ($runs->conclusion == 'failure') {
                $githubActionRunJobs = $this->getGithubActionRunJobs($repositoryId, $runs->id);
                foreach ($githubActionRunJobs->jobs as $job) {
                    foreach ($job->steps as $step) {
                        if ($step->conclusion == 'failure') {
                            $githubActionRuns->workflow_runs[$key]->failure_reason = $step->name;
                        }
                    }
                }
            }
        }

        return $githubActionRuns;
    }

    public function listAllPullRequests(Request $request)
    {
        if($request->ajax()) {
            ini_set('max_execution_time', -1);

            $repositories = GithubRepository::where('id',  $request->repoId)->get();
            $allPullRequests = [];

            foreach ($repositories as $repository) {
                $organization = $repository->organization;
                $pullRequests = $this->getPullRequests($organization->username, $organization->token, $repository->id);

                foreach($pullRequests as $key =>  $pullRequest){
                    //Need to execute the detail API as we require the mergeable_state which is only return in the PR detail API.
                    $pr = $this->getPullRequestDetail($organization->username, $organization->token, $repository->id, $pullRequest['id']);
                    $pullRequests[$key]['mergeable_state'] = $pr['mergeable_state'];
                    $pullRequests[$key]['conflict_exist'] = $pr['mergeable_state'] == "dirty" ? true : false;
                }
                $pullRequests = array_map(
                    function ($pullRequest) use ($repository) {
                        $pullRequest['repository'] = $repository;

                        return $pullRequest;
                    },
                    $pullRequests
                );

                $allPullRequests = array_merge($allPullRequests, $pullRequests);
            }

            return response()->json([
                'tbody' => view('github.include.pull-request-list', compact('pullRequests'))->render(),
                'count' => count($pullRequests)
            ], 200);
        }

        $githubOrganizations = GithubOrganization::with('repos')->get();

        return view('github.all_pull_requests', compact('githubOrganizations'));
    }

    public function deployNodeScrapers()
    {
        return $this->getRepositoryDetails(231924853);
    }

    /**
     * Githjub Branch Page
     */
    public function branchIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->getAjaxBranches($request);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }
        $githubOrganizations = GithubOrganization::with('repos')->get();

        return view('github.branches', compact('githubOrganizations'));
    }

    public function getAjaxBranches(Request $request)
    {
        $branches = GithubBranchState::where('repository_id', $request->repoId)->orderBy('created_at', 'desc');
        if ($request->status) {
            $branches = $branches->where('status', $request->status);
        }

        return $branches->get();
    }

    /**
     * Github Actions Page
     */
    public function actionIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->getAjaxActions($request);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        $githubOrganizations = GithubOrganization::with('repos')->get();

        return view('github.actions', compact('githubOrganizations'));
    }

    public function getAjaxActions(Request $request)
    {
        $data = $this->githubActionResult($request->repoId, $request->page, $request->date);

        return $data;
    }

    public function rerunGithubAction($repoId, $jobId)
    {
        $data = $this->rerunAction($repoId, $jobId);

        return $data;
    }
}
