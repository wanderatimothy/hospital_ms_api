<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $validator = Validator::make(request()->all(), [
            'limit'=> 'sometimes|integer|max:1000|min:5',
            'patient_id' => 'sometimes|numeric|exists:patients,id',
            'search_word' => 'required|string',
            'visit_outcome' => 'sometimes|string',
            'departure_time' => 'sometimes|array|size:2',
            'arrival_time' => 'sometimes|array|size:2',
            'is_insured'=>'sometimes|boolean',
            'has_appointment'=>'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $limit = request()->has("limit") ? request("limit") :10;
        $visits =Visit::with(['lastModifiedBy:full_name'])
        ->select('visits.*')
        ->when(request()->has("search_word"), function ($query) {
           return $query->where('first_name','like', '%' . request()->search_word . '%' )
           ->orWhere('last_name','like', '%' . request()->search_word . '%' )
           ->orWhere('gender','like', '%' . request()->search_word . '%' )
           ->orWhere('phone_number','like', '%' . request()->search_word . '%' )
           ->orWhere('visit_date','like', '%' . request()->search_word . '%' ); 
           
        })
        ->when(request()->has("patient_id"), function ($query) {
            return $query->where('patient_id',request()->patient_id);
        })
        ->when(request()->has("has_appointment"), function ($query) {
            return $query->where('has_appointment',request()->has_appointment);
        })
        ->when(request()->has("is_insured"), function ($query) {
            return $query->where('is_insured',request()->is_insured);
        })
        ->when(request()->has("visit_outcome"), function ($query) {
            return $query->where('visit_outcome',request()->visit_outcome);
        })
        ->when(request()->has("departure_time"), function ($query) {
            return $query->whereBetween('departure_time',request()->departure_time);
        })
        ->when(request()->has("arrival_time"), function ($query) {
            return $query->whereBetween('arrival_time',request()->arrival_time);
        })
        ->orderBy('id' , 'desc')
        ->paginate($limit);
        return ApiResponse::dataResponse($visits);
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
            'patient_id' => 'sometimes|integer|exists:patients,id',
            'has_appointment' => 'required|boolean',
            'first_name' => 'required|string|max:150',
            'last_name' => 'required|string|max:150',
            'arrival_time' => 'required|date_format:Y-m-d H:i:s',
            'gender' => 'required|string|max:10',
            'is_insured' => 'required|boolean',
            'health_insuarance_coverage'=> 'required|string|max:150',
            'insuarance_provider_id' => 'sometimes|integer|exists:insuarance_providers,id',
            'phone_number' => 'required|string|max:13',
            'visit_date' => 'required|date_format:Y-m-d',
            'appointment_id' => 'sometimes|integer|exists:appointments,id',
        ]);
        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }
        $visit = new Visit();
        $data = $request->all();
        $data['created_by'] =  $data['last_modified_by']= auth()->user()->id;
        $visit->fill($data);
        if($visit->save()){
            return ApiResponse::
            successResponse($visit->only([
            'id',
            'first_name',
            'last_name',
            'gender',
            'phone_number',
            'is_insured',
            'arrival_time']),'Operation was successful!');
        }
        return ApiResponse::failureResponse($request->all(),'Operation was not successful');

    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $validator = Validator::make(request()->all(), [
            'visit_id' => 'required|integer|exists:visits,id'
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $visit = Visit::with([
            'lastModifiedBy:first_name,last_name,id,email,image',
            'createdBy:first_name,last_name,id,email,image'
        ])
        ->find(request()->visit_id);

        if (!$visit) {
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }
        return ApiResponse::dataResponse($visit->makeHidden(['last_modified_by' , 'created_by']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visit $visit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $visit = Visit::find($request->visit_id);

        if (!$visit) {
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }
        $validator = Validator::make(request()->all(), [
            'patient_id' => 'sometimes|integer|exists:patients,id',
            'first_name' => 'sometimes|string|max:150',
            'last_name' => 'sometimes|string|max:150',
            'phone_number' => 'sometimes|string|max:13',
            'appointment_id' => 'sometimes|integer|exists:appointments,id',
            'departure_time'=> 'sometimes|date_format:Y-m-d H:i:s',
            'visit_outcome' => 'sometimes|string|max:150',
        ]);
        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }
        $visit->fill($request->all());
        $visit->last_modified_by = auth()->user()->id;
        if($visit->update()){
            return ApiResponse::
            successResponse($visit->only([
            'id',
            'first_name',
            'last_name',
            'gender',
            'phone_number',
            'is_insured',
            'arrival_time']),
            'Operation was successful!',202);
        }
        return ApiResponse::failureResponse($request->all(),'Operation was not successful');


        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visit $visit)
    {
        if($visit->delete()){
            return ApiResponse::successResponse($visit,'Operation was successful!',202);
        }
        return ApiResponse::failureResponse(request()->all(),'Operation was not successful');
    }
}
