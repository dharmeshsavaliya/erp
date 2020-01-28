<?php

namespace App\Http\Controllers\Github;

use App\Github\GithubBranchState;
use App\Github\GithubRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Artisan;
use Carbon\Carbon;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Input;
use Twilio\TwiML\Messaging\Body;

class RepositoryController extends Controller
{
    private $client;

    function __construct()
    {
        $this->client = new Client([
            'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')]
        ]);
    }

    private function refreshGithubRepos()
    {
        $url = "https://api.github.com/orgs/" . getenv('GITHUB_ORG_ID') . "/repos";
        $response = $this->client->get($url);

        $repositories = json_decode($response->getBody()->getContents());

        $dbRepositories = [];

        foreach ($repositories as $repository) {

            $data = [
                'id' => $repository->id,
                'name' => $repository->name,
                'html' => $repository->html_url,
                'webhook' => $repository->hooks_url,
                'created_at' => Carbon::createFromFormat(DateTime::ISO8601, $repository->created_at),
                'updated_at' => Carbon::createFromFormat(DateTime::ISO8601, $repository->updated_at)
            ];

            GithubRepository::updateOrCreate(
                [
                    'id' => $repository->id
                ],
                $data
            );
            $dbRepositories[] = $data;
        }
        return $dbRepositories;
    }

    //
    public function listRepositories()
    {
        $repositories = $this->refreshGithubRepos();
        return view('github.repositories', [
            'repositories' => $repositories
        ]);
    }

    public function getRepositoryDetails($repositoryId)
    {
        $repository = GithubRepository::find($repositoryId);
        $branches = $repository->branches;

        $currentBranch = exec('sh ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . $repository->name . '/get_current_deployment.sh');

        //exec('sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').'erp/deploy_branch.sh master');

        //exit;
        return view('github.repository_settings', [
            'repository' => $repository,
            'branches' => $branches,
            'current_branch' => $currentBranch
        ]);



        //print_r($repository);
    }

    public function deployBranch($repoId)
    {
        $repository = GithubRepository::find($repoId);

        $branch = Input::get('branch');
        //echo 'sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').'erp/deploy_branch.sh '.$branch;
        exec('sh ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . $repository->name . '/deploy_branch.sh ' . $branch);
        return redirect(url('/github/repos/' . $repoId . '/branches'));
    }

    public function mergeBranch($id)
    {

        $source = Input::get('source');
        $destination = Input::get('destination');

        $url = "https://api.github.com/repositories/" . $id . "/merges";

        try {
            $this->client->post(
                $url,
                [
                    RequestOptions::BODY => json_encode([
                        'base' => $destination,
                        'head' => $source,
                    ])
                ]
            );
            echo 'done';
            Artisan::call('github:load_branch_state');
        } catch (Exception $e) {
            print_r($e->getMessage());
            return redirect(url('/github/repos/' . $id . '/branches'))->with(
                [
                    'message' => 'Failed to Merge!',
                    'alert-type' => 'error'
                ]
            );
        }
        return redirect(url('/github/repos/' . $id . '/branches'))->with([
            'message' => 'Branch merged successfully',
            'alert-type' => 'success'
        ]);
    }

    private function getPullRequests($repoId)
    {
        $url = "https://api.github.com/repositories/" . $repoId . "/pulls";
        $response = $this->client->get($url);

        $decodedJson = json_decode($response->getBody()->getContents());

        $pullRequests = array();
        foreach ($decodedJson as $pullRequest) {
            $pullRequests[] = array(
                'id' => $pullRequest->number,
                'title' => $pullRequest->title,
                'number' => $pullRequest->number,
                'source' => $pullRequest->head->ref,
                'destination' => $pullRequest->base->ref
            );
        }

        return $pullRequests;
    }

    public function listPullRequests($repoId)
    {

        $repository = GithubRepository::find($repoId);

        $pullRequests = $this->getPullRequests($repoId);

        $branchNames = array_map(
            function($pullRequest){
                return $pullRequest['source'];
            },
            $pullRequests
        );

        $branchStates = GithubBranchState::whereIn('branch_name', $branchNames)->get();

        foreach($pullRequests as $pullRequest){
            $pullRequest['branchState'] = $branchStates->first(
                function($value, $key) use ($pullRequest){
                    return $value->branch_name == $pullRequest['source'];
                }
            );
        }

        return view('github.repository_pull_requests', [
            'pullRequests' => $pullRequests,
            'repository' => $repository
        ]);
    }
}
