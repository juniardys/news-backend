<?php
namespace App\Traits;

/**
 * Api Response
 */
trait ApiResponse {

    public function responseSuccess($data, $message = '', $status = 200, $headers = []) {
        return response()->json([
            'success'   => true,
            'message'   => $message,
            'data'      => $data
        ], $status, $headers);
    }

    public function responseError($message = '', $errorCode = 40300, $data = null, $status = 400, $headers = []) {
        return response()->json([
            'success'   => false,
            'error_code' => $errorCode,
            'message'   => $message,
            'data'      => $data
        ], $status, $headers);
    }
}
