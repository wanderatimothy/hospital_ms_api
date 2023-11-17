<?php

namespace Tests\Feature;

use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestUtils\UserManager;
use Tests\TestCase;

class BranchTest extends TestCase
{
    use RefreshDatabase ,WithFaker;
    /**
     * A basic feature test example.
     */
    public function test_getting_pagginated_list_of_branches(): void
    {
        $user =  UserManager::createTestUser();
        Branch::factory()->count(5)->create();
        $this->actingAs($user);
        $response =  $this->get(route("branches.all"));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'current_page',
                'path',
                'data'=>[],
                'prev_page_url',
                'next_page_url',
                'per_page',
                'total',
                'to'
            ]
        ]);

    }
}
