<?php

namespace App\Http\Controllers;

use App\Events\PatientUpdate;
use App\Models\Patient;
use App\Utils\ApiResponse;
use App\Utils\PatientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $validator = Validator::make(request()->all(), [
            'limit'=> 'sometimes|integer|max:1000|min:5',
            'search_word' => 'sometimes|string',
            'columns'=> 'sometimes|string',
            'ignore_in_search' => 'sometimes|string',
            'last_visit_date' => 'sometimes|array',
            'blood_group' => 'sometimes|string',
        ]);
        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }
        
        $limit = request()->has("limit") ? request("limit") :10;
        $patients =Patient::with(['lastModifiedBy:full_name'])
        ->select(explode(',', request()->columns))
        ->when(request()->has("search_word"), function ($query) {
            foreach(explode(',', request()->columns) as $column){
                if(request()->has("ignore_in_search") && !in_array($column,explode(',', request()->ignore_in_search))){
                    $query->orWhere($column,'like', '%' . request()->search_word . '%' );
                }else{
                    $query->orWhere($column,'like', '%' . request()->search_word . '%' );
                }
                
            }
            return $query;
        })
        ->when(request()->has("is_insured"), function ($query) {
            return $query->where('is_insured',request()->is_insured);
        })
        ->when(request()->has("blood_group"), function ($query) {
            return $query->where('blood_group',request()->blood_group);
        })
        ->when(request()->has("last_visit_date"), function ($query) {
            return $query->whereBetween('last_visit_date',request()->last_visit_date);
        })
        ->when(request()->has("date_of_birth"), function ($query) {
            return $query->whereBetween('date_of_birth',request()->date_of_birth);
        })
        ->orderBy('id' , 'desc')
        ->paginate($limit);
        return ApiResponse::dataResponse([
            'patients' => $patients,
            'availableFields' => PatientService::PATIENT_COLUMNS 
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
            'first_name'=> 'required|string|max:255|min:4',
            'last_name'=> 'required|string|max:255|min:4',
            'other_names'=> 'sometimes|string|max:255|min:4',
            'date_of_birth' => 'required|date_format:Y-m-d H:i:s',
            'blood_group' => 'required|string|min:1|max:3',
            'last_visit_date' => 'sometimes|date|date_format:Y-m-d H:i:s',
            'is_insured' => 'required|boolean',
            'gender' => 'required|string',
            'email' => 'sometimes|max:255|min:2|email',
            'phone_number' => 'required|string|max:13',
            'address' => 'sometimes|string',
            'emergency_contact_first_name'=> 'sometimes|string|max:255|min:4',
            'emergency_contact_last_name'=> 'sometimes|string|max:255|min:4',
            'emergency_contact_relationship'=>
            'sometimes|string|in_array:["father","mother","brother","sister","wife","husband" , "guardian", "son", "daughter"]',
            'emergency_contact_phone_number'=> 'sometimes|string|max:13',
            'nationality'=> 'sometimes|string|max:255|min:4',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'national_number'=> 'sometimes|string|max:255|min:4',
            'ethinicity'=> 'sometimes|string|max:255|min:4',

        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $patient = new Patient();
        $data = $request->all();
        $data['created_by'] =  $data['last_modified_by']= auth()->user()->id;
        $data['unique_no'] = "_P".Patient::query()->count() + 1;
        if(request()->hasFile('image')){
            $data['image'] = $request->file('image')->store('patients','public');
        }
        $patient->fill($data);

        if($patient->save()){
            return ApiResponse::successResponse($patient->only([
                'id',
                'unique_no',
                'first_name',
                'last_name',
                'phone_number',
                'emergency_contact_phone_number',
                'emergency_contact_relationship',
                'blood_group',
                'image',
                'gender',
                'ethinicity',
                'is_insured',
                'last_visit_date',
                'date_of_birth',
            ]),'Operation was successful');
        }
       
        return ApiResponse::failureResponse($request->all(),'Operation was not successful');

    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

       $validator = Validator::make(request()->all(), [
           'patient_id' => 'required|integer|exists:patients,id',
           'columns' => 'sometimes|string|min:3|max:255',
           'data'  => 'required_if:columns|string',
           'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
       ]);
       if ($validator->fails()) {
        return ApiResponse::validationErrorResponse($validator);
      }
      $patient = Patient::find($request->patient_id);

      if (!$patient) {
          return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
      }


      if(request()->hasFile('image')){
        $patient->image = $request->file('image')->store('patients','public');
      }
      
      if($request->has('columns')){

        $patient->update([$request->columns => $request->data]);
        $columns = explode(',', $request->columns);
        $data = json_decode($request->data, true);
        $updates = [];
        foreach($columns as $column){
            if(array_key_exists($column, $data)){
                $updates[$column] = $data[$column];
            }
        }
        $patient->fill($updates);

      }else{
        $columns =  [
            'id', 'unique_no',
            'first_name', 'last_name',
            'phone_number',
            'emergency_contact_phone_number',
            'emergency_contact_relationship','blood_group',
            'image','gender',
            'ethinicity','is_insured',
            'last_visit_date','date_of_birth',
        ];
    
    }
      if($patient->update()){
       if($request->has('columns')){
            PatientUpdate::dispatch($patient, $columns, $request->data);

        }
        
        return ApiResponse::successResponse($patient->only($columns),'Operation was successful');
    }
   
    return ApiResponse::failureResponse($request->all(),'Operation was not successful');
      
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        if($patient->delete()){
            return ApiResponse::successResponse($patient,'Operation was successful!',202);
        }
        return ApiResponse::failureResponse(request()->all(),'Operation was not successful');
    }
}
