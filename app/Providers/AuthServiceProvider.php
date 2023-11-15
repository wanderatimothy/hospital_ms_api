<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Passport\AuthCode;
use App\Models\Passport\Client;
use App\Models\Passport\PersonalAccessClient;
use App\Models\Passport\RefreshToken;
use App\Models\Passport\Token;
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
        Passport::useTokenModel(Token::class);
        Passport::useRefreshTokenModel(RefreshToken::class);
        Passport::useAuthCodeModel(AuthCode::class);
        Passport::useClientModel(Client::class);
        Passport::usePersonalAccessClientModel(PersonalAccessClient::class);
        Passport::hashClientSecrets();
        Passport::tokensExpireIn(now()->addMinutes(120));
        Passport::refreshTokensExpireIn(now()->addDays(300));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
