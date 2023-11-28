<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\UserBranch;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $limit = request()->has("limit") ? request("limit") :10;
        $branches =Branch::with(['lastModifiedBy:full_name'])
        ->select(["id","name","email","phone_number","address","updated_at"])
        ->when(request()->has("search_word"), function ($query) {
           return $query->where('name','like', '%' . request()->search_word . '%' )
           ->orWhere('phone_number','like', '%' . request()->search_word . '%' )
           ->orWhere('address','like', '%' . request()->search_word . '%' )
           ->orWhere('email','like', '%' . request()->search_word . '%' )
           ->orWhere('updated_at','like', '%' . request()->search_word . '%' ); 
        })
        ->orderBy('id' , 'desc')
        ->paginate($limit);
        return ApiResponse::dataResponse($branches);
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
            'name' => 'required|unique:branches,name',
            'phone_number' => 'required|string',
            'address' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $branch = new Branch();

        $branch->email = $request->email;
        $branch->name = $request->name;
        $branch->phone_number = $request->phone_number;
        $branch->address = $request->address?$request->address:null;
        $branch->created_by = $branch->last_modified_by = auth()->user()->id;

        if($branch->save()) {
         return ApiResponse::
         successResponse($branch->only('id','name','address','email','phone_number'),'Operation was successful!');
        }
        return ApiResponse::failureResponse($request->all(),'Operation wasnot successful');

        
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {

        $branch = Branch::with([
            'lastModifiedBy:first_name,last_name,id,email,image',
            'createdBy:first_name,last_name,id,email,image'
        ])
        ->find($request->branch_id);

        if (!$branch) {
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }
        return ApiResponse::dataResponse($branch->makeHidden(['last_modified_by' , 'created_by']));

        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $branch = Branch::find($request->branch_id);

        if (!$branch) {
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }
        
        $validator = Validator::make(request()->all(), [
            'email'=> 'required|max:255|min:2|email',
            'name' =>  ['required',Rule::unique('user_types','name')->ignore($request->branch_id)],
            'phone_number' => 'required|string',
            'address' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $branch->email = $request->email;
        $branch->name = $request->name;
        $branch->phone_number = $request->phone_number;
        $branch->address = $request->address?$request->address:null;
        $branch->last_modified_by = auth()->user()->id;

        if($branch->update()) {
         return ApiResponse::
         successResponse($branch->only('id','name','address','email','phone_number'),'Operation was successful!',202);
        }
        return ApiResponse::failureResponse($request->all(),'Operation was not successful');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        if($branch->delete()){
            return ApiResponse::successResponse($branch,'Operation was successful!',202);
        }
        return ApiResponse::failureResponse(request()->all(),'Operation was not successful');
    }



    public function add_user_to_branch(Request $request){

        $validator = Validator::make(request()->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'branch_id' => 'required|integer|exists:branches,id',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        //check if user is already in the branch
        
        $match = UserBranch::query()
        ->where('user_id' , $request->user_id)
        ->where('branch_id' , $request->branch_id)
        ->first();

        if($match){
            return ApiResponse::failureResponse($request->all(),'User is already in the branch');
        }

        $user_branch = new UserBranch();

        $user_branch->user_id = $request->user_id;
        $user_branch->branch_id = $request->branch_id;
        $user_branch->created_by = $user_branch->last_modified_by = auth()->user()->id;

        if($user_branch->delete()){
            return ApiResponse::successResponse(['branch_id' => $request->branch_id],'Operation was successful!',202);
        }
        return ApiResponse::failureResponse(request()->all(),'Operation was not successful');


    }

    public  function remove_user_from_branch(Request $request){
        
        $validator = Validator::make(request()->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'branch_id' => 'required|integer|exists:branches,id',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $match = UserBranch::query()
        ->where('user_id' , $request->user_id)
        ->where('branch_id' , $request->branch_id)
        ->first();

        if(!$match){
            return ApiResponse::failureResponse($request->all(),'User is not in the branch');
        }

        if($match->delete()){
            return ApiResponse::successResponse(['branch_id' => $request->branch_id],'Operation was successful!',202);
        }
        return ApiResponse::failureResponse(request()->all(),'Operation was not successful');

    }


    public function get_branches_user_can_access(Request $request){
        
        $user_id =  request()->has('user_id') ? $request->user_id : auth()->user()->id;

        $branches =  UserBranch::with(['branch'])
        ->query()
        ->where('user_id' , $user_id)
        ->get()
        ->map(function($item){
            return [
                'id' => $item->branch_id,
                'name' => $item->branch->name
            ];
        });
        return ApiResponse::dataResponse($branches);

    }
}
