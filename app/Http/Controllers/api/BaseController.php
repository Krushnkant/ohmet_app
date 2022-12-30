<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function sendResponseWithData($data, $message)
    {
        return response()->json(array('success'=>true,'status_code' => 1, 'message' => $message, 'data' => $data));
    }

    public function sendResponseSuccess($message)
    {
        return response()->json(array('success'=>true,'status_code' => 1, 'message' => $message));
    }

    public function sendError($error = [], $message, $errorMessages = [])
    {
        return response()->json(array('success'=>false,'status_code' => 0, 'error' => $error, 'message' => $message, 'data' => $errorMessages));
    }
}
