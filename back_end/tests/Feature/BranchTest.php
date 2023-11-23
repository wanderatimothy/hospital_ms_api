<?php

namespace Tests\Feature;

use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestUtils\UserManager;
use Tests\TestCase;

use function PHPUnit\Framework\assertCount;

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

    public function test_creating_a_branch_record(){

        $user =  UserManager::createTestUser();
   
        $this->actingAs($user);
   
        $form_data  = [
           'name' => $this->faker->word(),
           'address' => $this->faker->address(),
           'phone_number' => $this->faker->phoneNumber(),
           'email' => $this->faker->companyEmail(),
     
        ];
   
        $response =  $this->post(route("branches.create",$form_data));
   
        $response->assertStatus(201);
   
        $response->assertJsonStructure([
           'data' =>['id','name','address','phone_number','email'],
           'message',
           'status'
        ]);
   
       }

       public function test_updating_a_branch_record(){

        $user =  UserManager::createTestUser();

        $branch = Branch::factory()->count(1)->create()->first();

        $this->actingAs($user);

        $form_data  = [
            'name' => $this->faker->word(),
            'address' => $this->faker->address(),
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'branch_id' => $branch->id,
    
        ];

        $response =  $this->post(route("branches.update",$form_data));

        $response->assertStatus(202);

        $response->assertJsonStructure([
            'data' =>['id','name','address','phone_number','email'],
            'message',
            'status'
        ]);


    }


    public function test_delete_branch_record(){
        
        $user =  UserManager::createTestUser();

        $this->actingAs($user);

        $branches = Branch::factory()->count(2)->create();

        $response = $this->post('/api/branches/'.$branches->first()->id . '/delete' );

        $response->assertStatus(202);

        assertCount(1, Branch::all()->toArray());
    }


    public  function test_show_single_branch_record(){
        $user =  UserManager::createTestUser();
        $this->actingAs($user);
        $branch = Branch::factory()->count(1)->create([
            'created_by' => $user->id,
            'last_modified_by' => $user->id
        ])->first();

        $response = $this->post('/api/branches/show', [
            'branch_id'=> $branch->id,
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'address',
                'phone_number',
                'created_by',
                'updated_at',
                'created_at',
                'email'
            ],
        ]);
    }
}
