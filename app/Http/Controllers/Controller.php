<?php

namespace App\Http\Controllers;

use App\Component\ErrorCode;
use App\Service\Common;
use App\Service\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function json($code, $msg = '', $data = [], $status = 200, array $headers = [], $options = 0)
    {
        $responseData = [
            'code' => $code,
            'data' => $data,
            'msg' => $msg
        ];
        $response = response()->json($responseData, $status, $headers, $options);
        return $response;
    }

    protected function getUid() :int
    {
        $request = App::make(Request::class);
        $user = App::make(User::class);
        $token = $request->input('token', '');
        $token = '6+ELAhl0ACX0xTEmM45lN7saO3UtKy86zc3sWFnZBzw5Ldd4RdohjSnuuFFkuWBDZrYiFD7ROGrEwTgD6yvSeiknoDLsIHQL9YCyIIx0KvSs3+kt0Uoaz9T0JrycS9ISkHtImwu/fK8G9vE9yGGn36uoGgwgr9jALYjYnqA4DZfywQlaHBnqbvKi2g01qs+0s12S0Ge3KyCR3a7mvzackdbt/ao4rLdji9LN2l7SBqp0PZB7gQ16qAS0gYQ5z/lu+RKFIoioGfRn6eICd3rtb3E4VTk5Tk7PZy3kHrT0J9VNQJXpcpoW0dcE/j3LMlBv';
        if (empty($token)) {
            return 0;
        }
        $userInfo = $user->getUerInfoIgnoreSession($token);
        if (empty($userInfo)) {
            return 0;
        }
        return intval($userInfo['uid']);
    }

    protected function checkSign(Request $request) :bool
    {
        $isDebug = env('APP_DEBUG');
        if ($isDebug) {
            return true;
        }
        $allParams = $request->input();
        $now = time();
        $timestamp = $allParams['timestamp'] ?? 0;
        if (empty($timestamp) ||
            ($now - $timestamp) > 10 ||
            $timestamp > $now
        ) {
            return false;
        }
        $sign = $allParams['sign'] ?? '';
        if (empty($sign)) {
            return false;
        }
        $util = App::make(Common::class);
        unset($allParams['sign']);
        $trueSign = $util->generateRequestSign($allParams);
        if ($sign != $trueSign) {
            return false;
        }
        return true;
    }
}
