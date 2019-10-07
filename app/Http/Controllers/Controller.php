<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function json($code, $msg = '', $data = [], $status = 200, array $headers = [], $options = 0)
    {
        $responseData = [
            'code' => $code,
            'data' => $data,
            'msg' => $msg
        ];
        $response = response()->json($responseData, $status, $headers, $options);
        return $response;
    }
}
