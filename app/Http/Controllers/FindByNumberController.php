<?php

namespace App\Http\Controllers;

use Twilio\Jwt\ClientToken;
use Twilio\Twiml;
use Twilio\Rest\Client;
use App\Category;
use App\Notification;
use App\Leads;
use App\Order;
use App\Status;
use App\Setting;
use App\User;
use App\Brand;
use App\Product;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers;
use App\ChatMessage;


class FindByNumberController extends Controller
{
	protected function findLeadByNumber($number)
	{
		return Leads::where('contactno', '=', $number)->first();
	}
    protected function findOrderByNumber($number)
	{
		return Order::where('contact_detail', '=', $number)->first();
	}
    protected function findLeadOrOrderByNumber($number)
    {
        $lead = $this->findLeadByNumber($number);
        if($lead) {
            return array("leads", $lead);
        }
        $order = $this->findOrderByNumber($number);
        if ($order) {
            return array("orders", $order);
        }
        return array(FALSE, FALSE);
    }
}
