<?php

namespace App\Listeners;

use App\Events\BranchSwitch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BranchSwitchListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BranchSwitch $event): void
    {
        //call all method operations that should happen on branch switch
    }
}
