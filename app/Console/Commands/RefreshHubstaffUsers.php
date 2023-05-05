<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Hubstaff\HubstaffMember;
use App\Library\Hubstaff\Src\Hubstaff;
use App\Helpers\LogHelper;

class RefreshHubstaffUsers extends Command
{
    public $HUBSTAFF_TOKEN_FILE_NAME;

    public $SEED_REFRESH_TOKEN;

    public $hubstaff;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubstaff:refresh_users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the Hubstaff users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->HUBSTAFF_TOKEN_FILE_NAME = 'hubstaff_tokens.json';
        // $this->SEED_REFRESH_TOKEN       = getenv('HUBSTAFF_SEED_PERSONAL_TOKEN');
        $this->SEED_REFRESH_TOKEN = config('env.HUBSTAFF_SEED_PERSONAL_TOKEN');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $report = \App\CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            $this->refreshUserList();
            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function refreshUserList()
    {
        // 1. try to get the list of users
        // 2. If users recieved update in database
        // 3. if users not recieved due to token failure refresh the token and retry

        // start hubstaff section from here
        $hubstaff = Hubstaff::getInstance();
        try {
            $this->hubstaff = $hubstaff->authenticate(false);
            $this->startGetUser();
        } catch (\Exception $e) {
            $this->hubstaff = $hubstaff->authenticate(true);
            $this->startGetUser();
        }
        // $organizationUsers = $hubstaff->getRepository('organization')->getOrgUsers(env("HUBSTAFF_ORG_ID"),0,665240);
    }

    private function startGetUser($startId = 665240)
    {
        $organizationUsers = $this->hubstaff->getRepository('organization')->getOrgUsers(config('env.HUBSTAFF_ORG_ID'), 0, $startId);

        if (! empty($organizationUsers->members)) {
            $record = count($organizationUsers->members);
            echo 'Total Record :' . $record;
            foreach ($organizationUsers->members as $member) {
                echo $member->user_id . ' Record started';
                echo PHP_EOL;
                $memeberExist = HubstaffMember::where('hubstaff_user_id', $member->user_id)->first();
                if (! $memeberExist) {
                    $userDetails = $this->hubstaff->getRepository('user')->getUserDetail($member->user_id);
                    if (! empty($userDetails)) {
                        $member->email = $userDetails->user->email;
                    }

                    if (! empty($member->email)) {
                        $userExist = \App\User::where('email', $member->email)->first();
                        HubstaffMember::create([
                            'hubstaff_user_id' => $member->user_id,
                            'email' => $member->email,
                            'user_id' => ($userExist) ? $userExist->id : null,
                        ]);
                    }
                }

                echo $member->user_id . ' Record eneded';
                echo PHP_EOL;
                echo 'Total Record Left :' . $record--;
                echo PHP_EOL;
            }

            if (! empty($organizationUsers->pagination) && ! empty($organizationUsers->pagination->next_page_start_id)) {
                $this->startGetUser($organizationUsers->pagination->next_page_start_id);
            }
        }
    }
}
