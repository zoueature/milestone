<?php


namespace App\Service;


class Common extends Service
{
    const SIGN_SALT = 'alhd^*4535A#@$#WDBJOuqejiqw09';

    public function generateRequestSign(array $requestParams) :string
    {
        if (empty($requestParams)) {
            return '';
        }
        ksort($requestParams);
        $str = http_build_query($requestParams).self::SIGN_SALT;
        $sign = sha1($str);
        return $sign;
    }
}
