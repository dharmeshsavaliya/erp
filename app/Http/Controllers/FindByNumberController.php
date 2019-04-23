<?php

namespace App\Http\Controllers;

use Twilio\Jwt\ClientToken;
use Twilio\Twiml;
use Twilio\Rest\Client;
use App\Category;
use App\Notification;
use App\Leads;
use App\Customer;
use App\Order;
use App\Status;
use App\Setting;
use App\User;
use App\Brand;
use App\Product;
use App\Message;
use App\Purchase;
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
	protected function findCustomerByNumber($number)
	{
		return Customer::where('phone', '=', $number)->first();
	}
    protected function findOrderByNumber($number)
	{
		return Order::where('contact_detail', '=', $number)->first();
	}

	protected function findPurchaseByNumber($number)
	{
		if ($agent = Agent::where('phone', $number)->first()) {
			if ($agent->purchase) {
				return $agent->purchase;
			}
		}

		return Purchase::where('supplier_phone', '=', $number)->first();
	}

	protected function findUserByNumber($number)
	{
		return User::where('phone', '=', $number)->first();
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

	protected function findCustomerOrLeadOrOrderByNumber($number)
  {
		$customer = $this->findCustomerByNumber($number);
		if($customer) {
				return array("customers", $customer);
		}
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
