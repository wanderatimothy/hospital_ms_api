<?php

namespace App\Http\Controllers;

use App\Models\UserType;
use App\Utils\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
         $data = UserType::query()->orderBy('id','desc')->get(['id' , 'name']);
         return ApiResponse::dataResponse($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $validator = Validator::make(request()->all(), [
            'name'=> 'required|max:150|min:2|unique:user_types,name',
            'created_by' => 'sometimes|integer'
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }
        
        $user_type = new UserType();

        $user_type->name = $request->name;
        $user_type->created_by = $request->created_by ? $request->created_by : null;
        $user_type->updated_by = $request->created_by ? $request->created_by : null;

        if($user_type->save()) {

            return ApiResponse::successResponse($user_type->only('id' , 'name'),'Operation was successful!');
        }

        return ApiResponse::failureResponse($request->all(),'Operation wasnot successful');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $validator = Validator::make(request()->all(), [
            'type_id' => 'required|integer|exists:user_types,id'
        ]);
        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }
        return ApiResponse::dataResponse(UserType::find(request()->type_id)->only(['id','name' , 'created_at']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserType $userType)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $userType = UserType::find($request->type_id);

        if (!$userType) {
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }

        $validator = Validator::make(request()->all(), [
            'name'=> ['required','string',Rule::unique('user_types','name')->ignore($userType->id)],
            'updated_by' => 'sometimes|integer'
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }
        
        $userType->name = $request->name;

        $userType->updated_by = $request->updated_by ? $request->updated_by : null;

        if($userType->update()) {

            return ApiResponse::successResponse($userType->only('id' , 'name'),'Operation was successful!');
        }

        return ApiResponse::failureResponse($request->all(),'Operation wasnot successful');
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserType $userType)
    {
        if($userType->delete()){
            return ApiResponse::successResponse($userType->only('id' , 'name'),'Operation was successful!');
        }
        return ApiResponse::failureResponse(request()->all(),'Operation was not successful');
    }



}
