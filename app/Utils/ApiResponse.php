<?php 


namespace App\Utils;

use Illuminate\Support\Facades\Validator;

abstract  class ApiResponse {


    public static function response($data , $message , $status , $httpStatusCode = 200 , $headers = []) {

        return response()->json([
            "status"=> $status,
            "message"=> $message,
            "data"=> $data
        ],$httpStatusCode ,$headers);

    }


    public static function successResponse($data , $message , $httpStatusCode = 201  ,$headers = []) {
        return self::response($data , $message , 'SUCCESS' , $httpStatusCode , $headers);
    }
    public static function failureResponse($data , $message , $httpStatusCode = 400  ,$headers = []) {
        return self::response($data , $message , 'FAILED' , $httpStatusCode , $headers);
    }

    public static function errorResponse($errorCode , $message , $httpStatusCode = 500 , $headers = []) {
        return response()->json([
            "status"=> 'ERROR',
            'errorCode' => $errorCode,
            "message"=> $message,
        ],$httpStatusCode ,$headers);
    }


    public static function validationErrorResponse($validator ,  $httpStatusCode = 422 , $headers = []){

        return response()->json([
            "status"=> 'ERROR',
            'errorCode' => 'BAD_DATA',
            "errors"=> $validator->errors(),
        ],$httpStatusCode ,$headers);

    }

    public static function dataResponse($data ,$httpStatusCode = 200 ,$headers=[]) { 
        return response()->json([
            "data" => $data
        ],$httpStatusCode ,$headers);
    }

    

}