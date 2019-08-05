<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],

        'Illuminate\Auth\Events\Login' => [
	        'App\Listeners\LogSuccessfulLoginListener',
        ],

        'Illuminate\Auth\Events\Logout' => [
	        'App\Listeners\LogSuccessfulLogoutListener',
        ],

        'App\Events\OrderCreated' => [
	        'App\Listeners\CreateOrderCashFlow',
        ],

        'App\Events\OrderUpdated' => [
	        'App\Listeners\UpdateOrderCashFlow',
        ],

        'App\Events\RefundCreated' => [
	        'App\Listeners\CreateRefundCashFlow',
        ],

        'App\Events\RefundDispatched' => [
	        'App\Listeners\UpdateRefundCashFlow',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
