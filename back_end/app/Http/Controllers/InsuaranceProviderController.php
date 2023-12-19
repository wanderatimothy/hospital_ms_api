<?php

namespace App\Http\Controllers;

use App\Models\InsuaranceProvider;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InsuaranceProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $limit = request()->has("limit") ? request("limit") :10;
        $insuaranceProvider = InsuaranceProvider::with(['lastModifiedBy:first_name,last_name,id'])
        ->select(["id","name","email","phone_number","address","logo","updated_at"])
        ->when(request()->has("search_word"), function ($query) {
           return $query->where('name','like', '%' . request()->search_word . '%' )
           ->orWhere('phone_number','like', '%' . request()->search_word . '%' )
           ->orWhere('address','like', '%' . request()->search_word . '%' )
           ->orWhere('email','like', '%' . request()->search_word . '%' )
           ->orWhere('updated_at','like', '%' . request()->search_word . '%' ); 
        })
        ->orderBy('id' , 'desc')
        ->paginate($limit);
        return ApiResponse::dataResponse($insuaranceProvider);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'email'=> 'required|max:255|min:2|email',
            'name' => 'required|unique:insuarance_providers,name',
            'phone_number' => 'required|string',
            'address' => 'sometimes|string',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $insuaranceProvider = new InsuaranceProvider();

        $insuaranceProvider->email = $request->email;
        $insuaranceProvider->name = $request->name;
        $insuaranceProvider->phone_number = $request->phone_number;
        $insuaranceProvider->address = $request->address?$request->address:null;
        $insuaranceProvider->logo = $request->logo?$request->file('logo')->store('insuaranceProviders','public'):null;
        $insuaranceProvider->created_by = $insuaranceProvider->last_modified_by = auth()->user()->id;

        if($insuaranceProvider->save()) {
         return ApiResponse::
         successResponse($insuaranceProvider
         ->only('id','name','address','email','phone_number', 'logo'),
         'Operation was successful!');
        }
        return ApiResponse::failureResponse($request->all(),'Operation wasnot successful');

    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $validator = Validator::make(request()->all(), [
            'insuarance_id' => 'required|integer|exists:insuaranceProviders,id'
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $insuaranceProvider = InsuaranceProvider::with([
            'lastModifiedBy:first_name,last_name,id,email,image',
            'createdBy:first_name,last_name,id,email,image'
        ])
        ->find(request()->insuarance_id);

        if (!$insuaranceProvider) {
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }
        return ApiResponse::dataResponse($insuaranceProvider->makeHidden(['last_modified_by' , 'created_by']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InsuaranceProvider $insuaranceProvider)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $insuaranceProvider = InsuaranceProvider::find($request->insurance_id);

        if (!$insuaranceProvider) {
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }
        
        $validator = Validator::make(request()->all(), [
            'insurance_id' => 'required',
            'email'=> 'required|max:255|min:2|email',
            'name' => 'required|unique:insuaranceProviders,name,'.$request->insurance_id,
            'phone_number' => 'required|string',
            'address' => 'sometimes|string',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        //update insuarance provider
        $data = $request->all();
        if(request()->hasFile('logo')){
            $data['logo'] = $request->file('logo')->store('insuaranceProviders','public');
        }
        $insuaranceProvider->fill($data);
        $insuaranceProvider->last_modified_by = auth()->user()->id;
        if($insuaranceProvider->update()){
            return ApiResponse::
            successResponse($insuaranceProvider
            ->only('id','name','address','email','phone_number', 'logo'),'Operation was successful!',202);
        }
           return ApiResponse::failureResponse($request->all(),'Operation wasnot successful');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InsuaranceProvider $insuaranceProvider)
    {
        if($insuaranceProvider->delete()){
            return ApiResponse::successResponse($insuaranceProvider,'Operation was successful!',202);
        }
        return ApiResponse::failureResponse(request()->all(),'Operation was not successful');
    }
}
