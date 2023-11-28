<?php

namespace App\Listeners;

use App\Events\BranchRemoval;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BranchRemovalListener implements ShouldQueue
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
    public function handle(BranchRemoval $event): void
    {
        //
    }
}
