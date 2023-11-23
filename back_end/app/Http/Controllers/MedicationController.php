<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MedicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $limit = request()->has("limit") ? request("limit") :10;
        $medications =Medication::with('lastModifiedBy:full_name')
        ->select("id","title","code","description","price","updated_at")
        ->when(request()->has("search_word"), function ($query) {
           return $query->where('title','like', '%' . request()->search_word . '%' )
           ->orWhere('code','like', '%' . request()->search_word . '%' )
           ->orWhere('description','like', '%' . request()->search_word . '%' )
           ->orWhere('updated_at','like', '%' . request()->search_word . '%' ); 
        })
        ->orderBy('id' , 'desc')
        ->paginate($limit);
        return ApiResponse::dataResponse($medications);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'title'=> 'required|max:255|min:2',
            'code' => 'required|unique:medications,code',
            'description' => 'required|string',
            'price' => 'sometimes|numeric'
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $medication = new Medication();

        $medication->title = $request->title;
        $medication->code = $request->code;
        $medication->description = $request->description;
        $medication->price = $request->price?$request->price:null;
        $medication->created_by = $medication->last_modified_by = auth()->user()->id;

        if($medication->save()) {
         return ApiResponse::
         successResponse($medication->only('id','title','code','description','price'),'Operation was successful!');
        }
        return ApiResponse::failureResponse($request->all(),'Operation wasnot successful');
    }
    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $medication = Medication::with([
            'lastModifiedBy:first_name,last_name,id,email,image',
            'createdBy:first_name,last_name,id,email,image'
        ])
        ->find($request->medication_id);

        if (!$medication) {
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }
        return ApiResponse::dataResponse($medication->makeHidden(['last_modified_by' , 'created_by']));

    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $medication = Medication::find($request->medication_id);

        if (!$medication) {
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }

        $validator = Validator::make(request()->all(), [
            'title'=> 'required|max:255|min:2',
            'code' => ['required',Rule::unique('user_types','name')->ignore($medication->id)],
            'description' => 'required|string',
            'price' => 'sometimes|numeric'
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $medication->title = $request->title;
        $medication->code = $request->code;
        $medication->description = $request->description;
        $medication->price = $request->price?$request->price:null;
        $medication->last_modified_by = auth()->user()->id;

        if($medication->update()) {
         return ApiResponse::
         successResponse($medication->only('id','title','code','description','price'),'Operation was successful!',202);
        }
        return ApiResponse::failureResponse($request->all(),'Operation wasnot successful');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medication $medication)
    {
        if($medication->delete()){
            return ApiResponse::successResponse($medication,'Operation was successful!',202);
        }
        return ApiResponse::failureResponse(request()->all(),'Operation was not successful');
    }
}
