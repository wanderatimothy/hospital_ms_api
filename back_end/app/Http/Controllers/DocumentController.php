<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Patient;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $validator = Validator::make(request()->all(), [
            'search_word' => 'sometimes|string',
            'document_type' => 'sometimes|integer|exists:document_types,id',
            'limit' => 'sometimes|integer',
            'entity_id' => 'sometimes|integer',
            'entity' => 'sometimes|string|in:staff,patient,administration',
            'order' => 'sometimes|string|in:asc,desc',
            'sortBy' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        $limit = request()->has("limit") ? request("limit") :10;
        $documents = Document::with(['lastModifiedBy:full_name'])
        ->select(["id","name","updated_at"])
        ->when(request()->has("search_word"), function ($query) {
           return $query->where('name','like', '%' . request()->search_word . '%' );
        })
        ->when(request()->has("document_type"), function ($query) {
            return $query->where('document_type_id',request()->document_type);
        })
        ->when((request()->has("entity")  && request()->has("entity_id")) , function ($query) {
            return $query->where('entity_id',request()->entity_id)->where('entity',request()->entity);
        })
        ->when(request()->has("order") && request()->has("sortBy"), function ($query) {
            return $query->orderBy(request()->sortBy,request()->order);
        })
        ->paginate($limit);
        return ApiResponse::dataResponse($documents);
        

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
        $validator = validator(request()->all(), [
            'entity' => 'required|string|in:staff,patient,administration',
            'entity_id' => 'required|integer|min:1',
            'document_type_id' => 'required|integer|exists:document_types,id',
            'is_text' => 'required|boolean',
            'file' => 'required_if:is_text,false|file|mimes:pdf,jpg,jpeg,png|max:4048',
            'name' => 'required|string',
            'content' => 'sometimes|string|max:65535',
            
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationErrorResponse($validator);
        }

        if($request->entity === 'patient'){
            if(!Patient::find($request->entity_id)){
                return ApiResponse::failureResponse($request->all(),'Patient not found' , 404);
            }
        }

        $document = new Document();
        $document->name = $request->name;
        $document->is_text = $request->is_text;
        $document->document_type_id = $request->document_type_id;
        $document->entity_id = $request->entity_id;
        $document->last_modified_by = $document->created_by = auth()->user()->id;
        if($request->is_text){
            $document->content = $request->content;
        }else{
            $document->store_path = $request->file->store('documents');
        }
        
        if($document->save()){
            return ApiResponse::successResponse($document->only('name' , 'id' ),'Document saved successfuly');
        }

        return ApiResponse::failureResponse(request()->all(),'Operation was not successful');


    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        if($document->delete()){
            return ApiResponse::successResponse($document->only('name' , 'id' ),'Operation was successful!',202);
        }
        return ApiResponse::failureResponse(request()->all(),'Operation was not successful');
    }
}
