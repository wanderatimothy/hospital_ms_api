<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase ,WithFaker;
    /**
     * A basic feature test example.
     */
    public function test_login_action(): void
    {
        $user =  User::factory()->count(1)->create();

        $response = $this->post('/api/auth/login',[
            'email'=> $user->first()->email,
            'password' => 'password'
        ]);

        $response->assertStatus(202);

        $response->assertJsonStructure([
            'data' => [
                'access_token',
                'refresh_token',
                'token_type',
                'expires_at',
                'scopes'
            ],
            'message',
            'status'
        ]);

    }
}
