<?php 

namespace App\Utils;

use Illuminate\Routing\Route;

abstract class PermissionsGenerator {


    public static function generatePermissions() : array {

        $routes = collect(Route::getRoutes());
        $permissions = $routes->map(function (Route $route) {
            return $route->getName();
        });
        return $permissions->toArray();


    }

}
