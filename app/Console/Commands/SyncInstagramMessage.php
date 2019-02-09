<?php

namespace App\Console\Commands;

use App\Customer;
use App\InstagramThread;
use Illuminate\Console\Command;
//use App\Services\Instagram\DirectMessage;

class SyncInstagramMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:instagram-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Instagram Direct Messaging With Customers Page';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private $messages;

    public function __construct()
//    public function __construct(DirectMessage $messages)
    {
        parent::__construct();
//        $this->messages = $messages;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $inbox = $this->messages->getInbox()->asArray();
        if (isset($inbox['inbox']['threads'])) {
            $threads = $inbox['inbox']['threads'];
            foreach ($threads as $thread)
            {
                $user = $thread['users'];
                if (count($user) !== 1) {
                    continue;
                }
                $customer = $this->createCustomer($user[0]);

                if ($customer) {
                    $this->createThread($customer, $thread);
                }
            }
        }
    }

    /**
     * @param $user
     * @return Customer|void
     */
    private function createCustomer($user)
    {
        $customer = Customer::where('instahandler', $user['pk'])->first();
        if ($customer)
        {
            return;
        }

        $customer = Customer::where('ig_username', $user['username'])->first();


        if (!$customer) {
            $customer = new Customer();
            $customer->name = $user['full_name'];
        }

        $customer->instahandler = $user['pk'];
        $customer->save();

        return $customer;
    }

    private function createThread($customer, $t) {
        $thread = new InstagramThread();
        $thread->customer_id = $customer->id;
        $thread->thread_id = $t['thread_id'];
        $thread->thread_v2_id = $t['thread_v2_id'];
        $thread->save();
    }
}
