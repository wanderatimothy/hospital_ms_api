<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $validator = Validator::make(request()->all(), [
            'limit'=> 'sometimes|integer|max:1000|min:5',
            'search_word' => 'sometimes|string',
            'columns'=> 'required|string',
            'occupancy' => 'sometimes|in:vacant,occupied',
            'room_id' => 'sometimes|integer|exists:rooms,id',
        ]);
        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }
        $limit = request()->has("limit") ? request("limit") :10;
        $beds =Bed::with('lastModifiedBy:full_name','room:id,label')
        ->select(explode(',', request()->columns))
        ->when(request()->has("search_word"), function ($query) {
            foreach(explode(',', request()->columns) as $column){
             $query->orWhere($column,'like', '%' . request()->search_word . '%' );
            }
            return $query;
        })
        ->when(request()->has("occupancy"), function ($query) {
            return $query->where('occupancy', request()->occupancy);
        })
        ->when(request()->has("room_id"), function ($query) {
            return $query->where('room_id', request()->room_id);
        })
        ->orderBy('id' , 'desc')
        ->paginate($limit);
        return ApiResponse::dataResponse([
            'beds' => $beds,
            'availableColumns' => ['id', 'tag_name' , 'bed_status']
        ]);
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
            'tag_name' => 'required|unique:beds,tag_name',
            'bed_status' => 'required|in:vacant,occupied',
            'room_id' => 'required|integer|exists:rooms,id',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $bed = new Bed();
        $bed->tag_name = $request->tag_name;
        $bed->bed_status = $request->bed_status;
        $bed->room_id = $request->room_id;
        $bed->created_by = $bed->last_modified_by = auth()->user()->id;

        if($bed->save()) {
        return ApiResponse::
        successResponse($bed->only('id','tag_name','bed_status'),'Operation was successful!');
        }
        return ApiResponse::failureResponse($request->all(),'Operation wasnot successful');

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {

        $bed = Bed::with([
            'lastModifiedBy:first_name,last_name,id,email,image',
            'createdBy:first_name,last_name,id,email,image',
            'room:id,label'
        ])
        ->find($request->branch_id);

        if (!$bed) {
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }
        return ApiResponse::dataResponse($bed->makeHidden(['last_modified_by' , 'created_by']));

        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bed $bed)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        
        $validator = Validator::make(request()->all(), [
            'tag_name' => 'required|unique:beds,tag_name,'.$request->bed_id,
            'bed_status' => 'required|in:vacant,occupied',
            'room_id' => 'required|integer|exists:rooms,id',
            'bed_id' => 'required|integer|exists:beds,id',
        ]);
        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }
        $bed = Bed::find($request->bed_id);
        if (!$bed) {
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }
        $bed->tag_name = $request->tag_name;
        $bed->bed_status = $request->bed_status;
        $bed->room_id = $request->room_id;
        $bed->last_modified_by = auth()->user()->id;
        if($bed->update()) {
        return ApiResponse::
        successResponse($bed
        ->only('id','tag_name','bed_status'),
        'Operation was successful!',202);
        }
        return ApiResponse::failureResponse($request->all(),'Operation was not successful');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bed $bed)
    {
        if($bed->delete()){
            return ApiResponse::successResponse($bed,'Operation was successful!',202);
        }
        return ApiResponse::failureResponse(request()->all(),'Operation was not successful');
    }
}
