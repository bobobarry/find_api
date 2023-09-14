<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected function successResponse($data, $message = null, $code = 200)
    {
        return response([
            'status' => 'success',
            'error' => false,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function errorResponse($message = null, $code = 200)
    {
        return response([
            'status' => 'error',
            'error' => true,
            'message' => $message,
            'data' => null
        ], $code);
    }
}
