<?php

namespace App\Observers;

use Auth;
use App\User;
use App\Brand;
use App\Activity;

class BrandObserver
{
    /**
     * Handle the brand "created" event.
     *
     * @return void
     */
    public function created(Brand $brand)
    {
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::find(6);
        }
        /*Activity::create([
            'subject_type' => 'Brand',
            'subject_id' => $brand->id,
            'causer_id' => $user->id,
            'description' => $user->name.' has created brand '.$brand->name,
        ]);*/
    }

    /**
     * Handle the brand "updated" event.
     *
     * @return void
     */
    public function updated(Brand $brand)
    {
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::find(6);
        }
        /*Activity::create([
            'subject_type' => 'Brand',
            'subject_id' => $brand->id,
            'causer_id' => $user->id,
            'description' => $user->name.' has updated brand '.$brand->name,
        ]);*/
    }

    /**
     * Handle the brand "deleted" event.
     *
     * @return void
     */
    public function deleted(Brand $brand)
    {
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::find(6);
        }
        /*Activity::create([
            'subject_type' => 'Brand',
            'subject_id' => $brand->id,
            'causer_id' => $user->id,
            'description' => $user->name.' has deleted brand '.$brand->name,
        ]);*/
    }

    /**
     * Handle the brand "restored" event.
     *
     * @return void
     */
    public function restored(Brand $brand)
    {
        //
    }

    /**
     * Handle the brand "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Brand $brand)
    {
        //
    }
}
