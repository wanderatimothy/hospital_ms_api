<?php

namespace Tests\Feature;

use App\Models\Medication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestUtils\UserManager;
use Tests\TestCase;

class MedicationTest extends TestCase
{
    use RefreshDatabase,WithFaker;
    /**
     * A basic feature test example.
     */
    public function test_getting_pagginated_list_of_medications(): void
    {
        $user =  UserManager::createTestUser();
        Medication::factory()->count(40)->create();
        $this->actingAs($user);
        $response =  $this->get(route("medications.all"));
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
    public function test_creating_a_medication_record(){

     $user =  UserManager::createTestUser();

     $this->actingAs($user);

     $form_data  = [
        'code' => 'T_'.$this->faker->unique()->numberBetween(150 , 638),
        'title' => $this->faker->text(50),
        'description' => $this->faker->paragraph,
        'price' => $this->faker->randomFloat(2, 1, 1000),
  
     ];

     $response =  $this->post(route("medications.create",$form_data));

     $response->assertStatus(201);

     $response->assertJsonStructure([
        'data' =>['id','title','code','description','price'],
        'message',
        'status'
     ]);

    }

    public function test_updating_a_medication_record(){

        $user =  UserManager::createTestUser();

        $medication = Medication::factory()->count(1)->create()->first();

        $this->actingAs($user);

        $form_data  = [
            'code' => 'T_'.$this->faker->unique()->numberBetween(150 , 638),
            'title' => $this->faker->text(50),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'medication_id' => $medication->id,
    
        ];

        $response =  $this->post(route("medications.update",$form_data));

        $response->assertStatus(202);

        $response->assertJsonStructure([
            'data' =>['id','title','code','description','price'],
            'message',
            'status'
        ]);


    }


    public function test_delete_medication_record(){
        
        $user =  UserManager::createTestUser();

        $this->actingAs($user);

        $user_type = Medication::factory()->count(1)->create();

        

        $response = $this->post('/api/medications/'.$user_type->first()->id . '/delete' );

        $response->assertStatus(202);
    }


    public  function test_show_single_medication_record(){
        $user =  UserManager::createTestUser();
        $this->actingAs($user);
        $medication = Medication::factory()->count(1)->create([
            'created_by' => $user->id,
            'last_modified_by' => $user->id
        ])->first();

        $response = $this->post('/api/medications/show', [
            'medication_id'=> $medication->id,
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'code',
                'last_modified_by',
                'created_by',
                'description',
                'updated_at',
                'created_at',
                'price'
            ],
        ]);
    }

}
