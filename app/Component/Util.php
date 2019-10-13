<?php


namespace App\Component;


class Util
{
    public static function aesEncode(string $string, string $key, string $iv)
    {
        $encryptString = openssl_encrypt($string, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return $encryptString;
    }

    public static function aesDecode(string $encodeString, string $key, string $iv)
    {
        $decodeString = openssl_decrypt($encodeString, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return $decodeString;
    }
}
