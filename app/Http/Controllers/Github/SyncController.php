<?php

namespace App\Http\Controllers\Github;

use App\Github\GithubGroup;
use App\Github\GithubGroupMember;
use App\Github\GithubRepository;
use App\Github\GithubRepositoryGroup;
use App\Github\GithubRepositoryUser;
use App\Github\GithubOrganization;
use App\Github\GithubUser;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class SyncController extends Controller
{
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

    public function index()
    {
        $githubOrganizations = GithubOrganization::with('repos')->get();

        return view('github.sync', compact('githubOrganizations'));
    }

    public function startSync(Request $request)
    {
        if(!isset($request->organizationId) || empty($request->organizationId)){
            return abort(404);
        }

        $githubOrganization = GithubOrganization::find($request->organizationId);
        $organizationId = $githubOrganization->name;
        $userName = $githubOrganization->username;
        $token = $githubOrganization->token;

        $groups = $this->refreshGithubGroups($organizationId, $userName, $token);

        $this->refreshUsersForOrganization($organizationId, $userName, $token);
        $repositories = $this->refreshGithubRepos($organizationId, $userName, $token);

        $updatedUserAccess = [];
        foreach ($repositories as $repository) {
            $accessIds = $this->refreshUserAccessForRepository($organizationId, $userName, $token, $repository->id, $repository->name);
            $updatedUserAccess = array_merge($updatedUserAccess, $accessIds);
        }
        GithubRepositoryUser::whereNotIn('id', $updatedUserAccess)->delete();

        $updatedTeamAccessIds = [];
        foreach ($groups as $group) {
            $updatedIds = $this->refreshUserAccessInTeam($organizationId, $userName, $token, $group->id);
            $updatedTeamAccessIds = array_merge($updatedTeamAccessIds, $updatedIds);
        }
        GithubGroupMember::whereNotIn('id', $updatedTeamAccessIds)->delete();

        $updatedRepositoryAccess = [];
        foreach ($groups as $group) {
            $updatedIds = $this->refreshRepositoryForTeam($organizationId, $userName, $token, $group->id);
            $updatedRepositoryAccess = array_merge($updatedRepositoryAccess, $updatedIds);
        }
        GithubRepositoryGroup::whereNotIn('id', $updatedRepositoryAccess)->delete();

        return redirect('/github/sync');
    }

    private function refreshGithubRepos($organizationId, $userName, $token)
    {
        // $url = "https://api.github.com/orgs/" . getenv('GITHUB_ORG_ID') . "/repos?per_page=100";
        $url = 'https://api.github.com/orgs/'.$organizationId.'/repos?per_page=100';

        // $response = $this->client->get($url);

        $githubClient = $this->connectGithubClient($userName, $token);

        $response = $githubClient->get($url);

        $repositories = json_decode($response->getBody()->getContents());

        $dbRepositories = [];
        $repositoryIds = [];
        foreach ($repositories as $repository) {
            $data = [
                'id' => $repository->id,
                'name' => $repository->name,
                'html' => $repository->html_url,
                'webhook' => $repository->hooks_url,
                'created_at' => Carbon::createFromFormat(DateTime::ISO8601, $repository->created_at),
                'updated_at' => Carbon::createFromFormat(DateTime::ISO8601, $repository->updated_at),
            ];

            $updatedData = GithubRepository::updateOrCreate(
                [
                    'id' => $repository->id,
                ],
                $data
            );
            $dbRepositories[] = $updatedData;
            $repositoryIds[] = $repository->id;
        }

        // delete other repositories
        GithubRepository::whereNotIn('id', $repositoryIds)->delete();

        return $dbRepositories;
    }

    private function refreshUsersForOrganization($organizationId, $userName, $token)
    {
        // $url = "https://api.github.com/orgs/" . getenv('GITHUB_ORG_ID') . "/members";
        $url = 'https://api.github.com/orgs/'.$organizationId.'/members';

        // $response = $this->client->get($url);
        
        $githubClient = $this->connectGithubClient($userName, $token);

        $response = $githubClient->get($url);

        $users = json_decode($response->getBody()->getContents());
        $returnUser = [];
        $userIds = [];
        foreach ($users as $user) {
            $dbUser = [
                'id' => $user->id,
                'username' => $user->login,
            ];

            $updatedUser = GithubUser::updateOrCreate(
                [
                    'id' => $user->id,
                ],
                $dbUser
            );
            $returnUser[] = $updatedUser;
            $userIds[] = $user->id;
        }

        // delete additional users
        GithubUser::whereNotIn('id', $userIds)->delete();

        return $returnUser;
    }

    private function refreshGithubGroups($organizationId, $userName, $token)
    {
        // $url = "https://api.github.com/orgs/" . getenv('GITHUB_ORG_ID') . "/teams";
        $url = 'https://api.github.com/orgs/'.$organizationId.'/teams';

        // $response = $this->client->get($url);

        $githubClient = $this->connectGithubClient($userName, $token);

        $response = $githubClient->get($url);

        $groups = json_decode($response->getBody()->getContents());
        $returnGroup = [];
        $groupIds = [];
        foreach ($groups as $group) {
            $dbGroup = [
                'id' => $group->id,
                'name' => $group->name,
            ];

            $updatedGroup = GithubGroup::updateOrCreate(
                ['id' => $group->id],
                $dbGroup
            );

            $returnGroup[] = $updatedGroup;
            $groupIds[] = $group->id;
        }

        // delete other groups
        GithubGroup::whereNotIn('id', $groupIds)->delete();

        return $returnGroup;
    }

    private function refreshUserAccessForRepository($organizationId, $userName, $token, $repositoryId, $repositoryName)
    {
        // https://api.github.com/repos/:org/:repo/collaborators
        $url = 'https://api.github.com/repos/'.$organizationId.'/'.$repositoryName.'/collaborators';

        // $response = $this->client->get($url);

        $githubClient = $this->connectGithubClient($userName, $token);

        $response = $githubClient->get($url);

        $userRepositoryAccess = json_decode($response->getBody()->getContents());

        $updatedAccess = [];
        foreach ($userRepositoryAccess as $user) {
            $rights = null;
            if ($user->permissions->admin == true) {
                $rights = 'admin';
            } elseif ($user->permissions->push == true) {
                $rights = 'push';
            } elseif ($user->permissions->pull == true) {
                $rights = 'pull';
            }

            $updatedAccess[] = GithubRepositoryUser::updateOrCreate(
                [
                    'github_users_id' => $user->id,
                    'github_repositories_id' => $repositoryId,
                ],
                [
                    'github_users_id' => $user->id,
                    'github_repositories_id' => $repositoryId,
                    'rights' => $rights,
                ]
            );
        }

        $updatedIds = array_map(
            function ($mapping) {
                return $mapping->id;
            },
            $updatedAccess
        );

        return $updatedIds;
        //GithubRepositoryUser::whereNotIn('id', $updatedIds)->delete();
    }

    private function refreshUserAccessInTeam($organizationId, $userName, $token, $teamId)
    {
        // https://api.github.com/teams/:team_id/members?role=all
        $url = 'https://api.github.com/teams/'.$teamId.'/members?role=all';

        // $response = $this->client->get($url);

        $githubClient = $this->connectGithubClient($userName, $token);

        $response = $githubClient->get($url);

        $users = json_decode($response->getBody()->getContents());

        $updates = [];
        foreach ($users as $user) {
            $data = [
                'github_groups_id' => $teamId,
                'github_users_id' => $user->id,
            ];

            $updates[] = GithubGroupMember::updateOrCreate($data, $data);
        }

        // remove additional memberships
        $updatedIds = array_map(
            function ($mapping) {
                return $mapping->id;
            },
            $updates
        );

        return $updatedIds;
    }

    private function refreshRepositoryForTeam($organizationId, $userName, $token, $teamId)
    {
        // https://api.github.com/teams/:team_id/repos
        $url = 'https://api.github.com/teams/'.$teamId.'/repos';

        // $response = $this->client->get($url);

        $githubClient = $this->connectGithubClient($userName, $token);

        $response = $githubClient->get($url);

        $repos = json_decode($response->getBody()->getContents());

        $updates = [];
        foreach ($repos as $repo) {
            $rights = null;
            if ($repo->permissions->admin == true) {
                $rights = 'admin';
            } elseif ($repo->permissions->push == true) {
                $rights = 'push';
            } elseif ($repo->permissions->pull == true) {
                $rights = 'pull';
            }

            $updates[] = GithubRepositoryGroup::updateOrCreate(
                [
                    'github_repositories_id' => $repo->id,
                    'github_groups_id' => $teamId,
                ],
                [
                    'github_repositories_id' => $repo->id,
                    'github_groups_id' => $teamId,
                    'rights' => $rights,
                ]
            );
        }

        $updatedIds = array_map(
            function ($update) {
                return $update->id;
            },
            $updates
        );

        return $updatedIds;
    }
}
