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

        if (Auth::attempt($request->only('email' , 'password'))) {

            $user = Auth::user();

            $tokenResult = $user->createToken('web.auth', ['normal.access']); // Assign scopes here

            $message = 'Welcome back '. $user->full_name . '!';

            return ApiResponse::successResponse([
                'access_token' => $tokenResult->accessToken,
                'refresh_token' => $tokenResult->refreshToken,
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult->token->expires_at,
                'scopes' => $tokenResult->token->scopes, // Include scopes in the response
            ],$message,202);

        } else {
            return ApiResponse::failureResponse([
                'access_token' => null,
                'refresh_token' => null,
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
