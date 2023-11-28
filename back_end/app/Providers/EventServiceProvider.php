<?php

namespace App\Providers;

use App\Events\BranchRemoval;
use App\Events\BranchSwitch;
use App\Events\PatientUpdate;
use App\Events\VisitUpdate;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        VisitUpdate::class => [
            \App\Listeners\VisitUpdateListener::class
        ],
        PatientUpdate::class => [
            \App\Listeners\PatientUpdateListener::class
        ],
        BranchSwitch::class => [
            \App\Listeners\BranchSwitchListener::class
        ],
        BranchRemoval::class => [
            \App\Listeners\BranchRemovalListener::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
