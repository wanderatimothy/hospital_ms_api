<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Passport::loadKeysFrom(__DIR__.'/../secrets/oauth');
        Passport::hashClientSecrets();
        Passport::tokensExpireIn(now()->addMinutes(120));
        Passport::refreshTokensExpireIn(now()->addDays(300));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
