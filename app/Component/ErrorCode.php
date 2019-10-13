<?php


namespace App\Component;


class ErrorCode
{
    const SUCCESS = 0;

    const ERROR_PARAM_EMPTY = 100;
    const ERROR_NO_LOGIN = 101;
    const ERROR_PARAM_ILLEGAL = 102;

    const ERROR_SQL = 200;
    const DATA_NULL = 201;
}
