<?php


namespace App\Component;


class ErrorCode
{
    const SUCCESS = 0;

    /** @var int 参数相关 */
    const ERROR_PARAM_EMPTY = 100;
    const ERROR_NO_LOGIN = 101;
    const ERROR_PARAM_ILLEGAL = 102;

    /** @var int 数据库操作等相关 */
    const ERROR_SQL = 200;
    const DATA_NULL = 201;

    /** @var int 权限相关 */
    const ERROR_NOT_OWNER = 301;

    /** @var int 操作条件相关 */
    const ERROR_CONDITION_LIMIT = 400;
    const ERROR_NOT_CHECK_IN_TIME = 401;

    /** @var int 分类相关错误吗 */
    const ERROR_CATE_INVALID = 500;
}
