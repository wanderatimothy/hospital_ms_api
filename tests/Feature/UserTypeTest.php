<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\TestUtils\UserManager;

class UserTypeTest extends TestCase
{
    use RefreshDatabase , WithFaker;
    /**
     * A basic feature test example.
     */
    public function test_getting_list_of_user_types(): void
    {
        $user =  UserManager::createTestUser();

        $this->actingAs($user);

        $response = $this->get('/api/user_types');

        $response->assertStatus(200);
    }
     /**
     * Test creating a new user type.
     */
    public function test_create_new_user_type(): void {

        $user =  UserManager::createTestUser();

        $this->actingAs($user);

        $response = $this->post('/api/user_types', [
            'name'=>  $word = fake()->word(),
            'created_by' => 1
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'data' => ['id' => 1 , 'name'=> $word],
            'status' => 'SUCCESS',
            'message' => 'Operation was successful!'
        ]);
    }

    public function test_validation_failure_when_creating_user_type():void{
        $user =  UserManager::createTestUser();

        $this->actingAs($user);

        $response = $this->post('/api/user_types', [
            'name'=> '',
        ]);

        $response->assertStatus(422);

        $response->assertJson([
            'status'=> 'ERROR',
            'errorCode' => 'BAD_DATA',
            'errors' => [
                'name' => ['The name field is required.']
            ]

        ]);

    }

    public function  test_update_user_type(): void {

        $user =  UserManager::createTestUser();

        $this->actingAs($user);

        $user_type = UserType::factory()->count(1)->create();


        $response = $this->post("/api/user_types/update", [
            'type_id' => $user_type->first()->id,
            "name"=> $word = fake()->word(),
            "updated_by" =>  1
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'data' => ['id' => $user_type->first()->id  , 'name'=> $word],
            'status' => 'SUCCESS',
            'message' => 'Operation was successful!'
        ]);

    }

    public function test_show_single_user_type(): void {

    

        $user_type = UserType::factory()->count(1)->create();

        $user =  UserManager::createTestUser();

        $this->actingAs($user);

        $response = $this->post('/api/user_types/show', [
            'type_id'=> $user_type->first()->id,
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'data' => ['id' => $user_type->first()->id  , 'name'=> $user_type->first()->name],
        ]);
    }


    public function test_delete_user_type():void{
        $user =  UserManager::createTestUser();

        $this->actingAs($user);

        $user_type = UserType::factory()->count(1)->create();

        

        $response = $this->post('/api/user_types/'.$user_type->first()->id . '/delete' );

        $response->assertStatus(201);

    }
}
