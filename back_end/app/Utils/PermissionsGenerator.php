<?php 

namespace App\Utils;

use Illuminate\Routing\Route;

abstract class PermissionsGenerator {


    /**
     * Generates an array of permissions based on the registered routes.
     *
     * @return array The array of permissions.
     */
    public static function generatePermissions() : array {

        $routes = collect(Route::getRoutes());
        $permissions = $routes->map(function (Route $route) {
            return $route->getName();
        });
        return $permissions->toArray();


    }

}
