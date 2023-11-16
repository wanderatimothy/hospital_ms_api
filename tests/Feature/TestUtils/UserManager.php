<?php

namespace Tests\Feature\TestUtils;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserManager {

    public static function createTestUser()  {

        $user =  User::factory()->count(1)->create();

        return $user->first();

    }
}


?>