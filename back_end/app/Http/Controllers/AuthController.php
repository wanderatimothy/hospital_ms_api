<?php

namespace App\Http\Controllers;

use App\Events\BranchSwitch;
use App\Models\User;
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


    public function switch_active_branch(Request $request){

        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|integer|exists:branches,id',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }


        $user_id = $request->has('user_id') ? $request->user_id : auth()->user()->id;

        $user = User::find($user_id);

        if(!$user){

            return ApiResponse::failureResponse($request->all(),'User not found',404);
        }
        $current_active_branch = $user->branch_id;

        $user->branch_id = $request->branch_id;

        if($user->update()){

            BranchSwitch::dispatchIf(($current_active_branch !== $request->branch_id), $user, $request->branch_id,$current_active_branch);

            return ApiResponse::successResponse(['branch_id' => $request->branch_id],'Operation was successful!',202);
        }

        return ApiResponse::failureResponse($request->all(),'Operation was not successful');

    }
}
