<?php


namespace App\Service;


use App\Component\Util;

class User extends Service
{
    const AES_KEY = '234029nwifuwf76q';
    const AES_IV = '0947832740327042';
    const GENERATE_SIGN_SALT = '4u9fhvnigf71edvc82%*^&(*U)hbdsh';

    public function getUerInfoIgnoreSession(string $token) :array
    {
        $decodeString = Util::aesDecode(base64_decode($token), self::AES_KEY, self::AES_IV);
        if (empty($decodeString)) {
            return [];
        }
        $loginInfo = unserialize($decodeString);
        $sign = $loginInfo['sign'];
        unset($loginInfo['sign']);
        ksort($loginInfo);
        $string = http_build_query($loginInfo).self::GENERATE_SIGN_SALT;
        $trueSign = sha1($string);
        if ($sign != $trueSign) {
            return [];
        }
        $userInfo = [
            'uid' => $loginInfo['userId'] ?? 0,
            'name' => $loginInfo['userName'] ?? '',
            'type' => $loginInfo['userType'] ?? 0,
            'registerTime' => $loginInfo['registerTime'] ?? 0
        ];
        return $userInfo;
    }

}
