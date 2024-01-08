<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $limit = request()->has("limit") ? request("limit") :10;
        $documentTypes = DocumentType::with(['lastModifiedBy:full_name' , 'branch'])
        ->select(["id","name","description","updated_at"])
        ->when(request()->has("search_word"), function ($query) {
           return $query->where('name','like', '%' . request()->search_word . '%' )
           ->orWhere('description','like', '%' . request()->search_word . '%' )
           ->orWhere('updated_at','like', '%' . request()->search_word . '%' ); 
        })
        ->orderBy('id' , 'desc')
        ->paginate($limit);
        return ApiResponse::dataResponse($documentTypes);
        

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
            'name' => 'required|unique:documentTypes,name',
            'description' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $documentType = new DocumentType();
        $documentType->name = $request->name;
        $documentType->description = $request->description;
        $documentType->created_by = $documentType->last_modified_by = auth()->user()->id;

        if($documentType->save()){
        return ApiResponse::
        successResponse($documentType
        ->only('id','name','description'),
        'Operation was successful!');
        }
        return ApiResponse::failureResponse($request->all(),'Operation was not successful');

    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        
        $validator = Validator::make(request()->all(), [
            'document_type_id' => 'required|integer|exists:documentTypes,id'
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $documentType = DocumentType::with([
            'lastModifiedBy:first_name,last_name,id,email,image',
            'createdBy:first_name,last_name,id,email,image',
            'branch'
        ])
        ->find(request()->document_type_id);

        if (!$documentType) {
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }
        return ApiResponse::dataResponse($documentType->makeHidden(['last_modified_by' , 'created_by']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentType $documentType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        
        $validator = Validator::make(request()->all(), [
            'name' => 'required|unique:document_types,name,'.$request->document_type_id,
            'description' => 'sometimes|string',
            'document_type_id' => 'required|integer|exists:document_types,id',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $documentType = DocumentType::find($request->document_type_id);

        if(!$documentType){
            return ApiResponse::errorResponse('RESOURCE_NOT_FOUND','record not found in the system',404);
        }

        $documentType->name = $request->name;
        $documentType->description = $request->description;
        $documentType->last_modified_by = auth()->user()->id;

        if($documentType->update()){
            return ApiResponse::
            successResponse($documentType
            ->only('id','name','description'),
            'Operation was successful!' ,202);
            }
            
        return ApiResponse::failureResponse($request->all(),'Operation was not successful');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentType $document_type)
    {
        if($document_type->delete()){
            return ApiResponse::successResponse($document_type,'Operation was successful!',202);
        }
        return ApiResponse::failureResponse(request()->all(),'Operation was not successful');
    }
}
