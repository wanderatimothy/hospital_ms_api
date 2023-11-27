<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Utils\ApiResponse;
use App\Utils\RoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
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
            'occupancy' => 'sometimes|in:' . implode(',', RoomService::ROOM_OCCUPANCY_STATUSES)
        ]);
        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }
        $limit = request()->has("limit") ? request("limit") :10;
        $rooms =Room::with('lastModifiedBy:full_name')
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
        ->orderBy('id' , 'desc')
        ->paginate($limit);
        return ApiResponse::dataResponse([
            'rooms' => $rooms,
            'filterableStatus' => RoomService::ROOM_OCCUPANCY_STATUSES,
            'searchableColumns' => RoomService::ROOM_SEARCHABLE_COLUMNS
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
            'label' => 'required|unique:rooms,label',
            'purpose' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $room = new Room();

        $room->label = $request->label;
        $room->purpose = $request->purpose;
        $room->unique_number = "_R".Room::query()->count() + 1;
        $room->created_by = $room->last_modified_by = auth()->user()->id;

        if($room->save()) {
        return ApiResponse::
        successResponse($room->only('id','label','purpose' , 'occupancy' , 'unique_number'),'Operation was successful!');
        }
        return ApiResponse::failureResponse($request->all(),'Operation wasnot successful');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $room = Room::with([
            'lastModifiedBy:first_name,last_name,id,email,image',
            'createdBy:first_name,last_name,id,email,image'
        ])->find($request->room_id);

        if (!$room) {
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }

        return ApiResponse::dataResponse($room->makeHidden(['last_modified_by' , 'created_by']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
              
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'label' => 'required|unique:rooms,label',
            'purpose' => 'sometimes|string',
            'room_id' => 'required|exists:rooms,id',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $room = Room::find($request->room_id);

        if (!$room) {
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }

        $room->label = $request->label;
        $room->purpose = $request->purpose;
        $room->last_modified_by = auth()->user()->id;

        if($room->update()) {
        return ApiResponse::
        successResponse($room
        ->only('label','purpose' , 'occupancy' , 'unique_number'),
        'Operation was successful!' , 202);
        }
        return ApiResponse::failureResponse($request->all(),'Operation wasnot successful');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        if($room->delete()){
            return ApiResponse::successResponse($room,'Operation was successful!',202);
        }
        return ApiResponse::failureResponse(request()->all(),'Operation was not successful');
    }
}
