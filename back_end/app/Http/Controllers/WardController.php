<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ApiResponse::dataResponse(Ward::with([
            'lastModifiedBy:first_name,last_name,id,email,image',
        ])
        ->query()->select('id','name' , 'updated_at')->get());

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
            'name' => 'required|unique:wards,name',
            'branch_id' => 'required|integer|exists:branches,id',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $ward = new Ward();
        $ward->name = $request->name;
        $ward->branch_id = $request->branch_id;
        $ward->created_by = $ward->last_modified_by = auth()->user()->id;

        if($ward->save()) {
            return ApiResponse::
            successResponse($ward->only('id','name' , 'updated_at'),'Operation was successful!');
        }
        return ApiResponse::failureResponse($request->all(),'Operation wasnot successful');


    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $ward = Ward::with([
            'lastModifiedBy:first_name,last_name,id,email,image',
            'createdBy:first_name,last_name,id,email,image',
        ])
        ->find($request->ward_id);

        if (!$ward) {
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }
        
        return ApiResponse::dataResponse($ward->only('id','name' , 'updated_at'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ward $ward)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        $validator = Validator::make(request()->all(), [
            'name' => 'required|unique:wards,name,'.$request->ward_id,
            'branch_id' => 'required|integer|exists:branches,id',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $ward = Ward::find($request->ward_id);

        if (!$ward) {
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }

        $ward->name = $request->name;
        $ward->branch_id = $request->branch_id;
        $ward->last_modified_by = auth()->user()->id;

        if($ward->update()) {
            return ApiResponse::
            successResponse($ward->only('id','name' , 'updated_at'),'Operation was successful!' , 202);
        }
        return ApiResponse::failureResponse($request->all(),'Operation wasnot successful');



    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ward $ward)
    {
        if($ward->delete()){
            return ApiResponse::successResponse($ward,'Operation was successful!',202);
        }
        return ApiResponse::failureResponse(request()->all(),'Operation was not successful');
    }
}
