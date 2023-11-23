<?php

namespace App\Http\Controllers;

use App\Utils\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'string|required|min:8'
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $credentials = $request->only('email' , 'password');

        if (Auth::attempt($credentials)) {

            $user = Auth::user();

            $expiry =  now()->addMinutes(1440);
            $scopes = ['normal.access'];

            $tokenResult = $user->createToken('web.auth', $scopes, $expiry); // Assign scopes here

            $message = 'Welcome back '. $user->full_name . '!';

            return ApiResponse::successResponse([
                'access_token' => $tokenResult->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $expiry->toDateString(),
                'scopes' =>   $scopes// Include scopes in the response
            ],$message,202);

        } else {
            return ApiResponse::failureResponse([
                'access_token' => null,
                'scopes' => [],
                'expires_at'=> null,
                'token_type' => 'Bearer',
            ],"Invalid Credentials",401);
        }


    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return ApiResponse::dataResponse(
            [
                "redirect_route"  => '/login',
                'alert_message'  => 'See you againg soon!'
            ],
            200
        );
    }
}
