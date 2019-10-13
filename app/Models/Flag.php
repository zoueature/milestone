<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flag extends Model
{
    protected $table = 'flag';

    public $timestamps = false;

    private $columnDbMapCode = [
        'uid' => 'uid',
        'category_id' => 'categoryId',
        'task_id' => 'taskId',
        'period' => 'period',
        'task_size' => 'taskSize',
        'last_check_in_time' => 'lastCheckInTime',
        'status' => 'status',
        'create_time' => 'createTime',
        'update_time' => 'updateTime'
    ];

    /* -----------------------------
     * 获取用户所属flag
     * -----------------------------
     */
    public function getFlagsByUid(int $uid, int $type = -1)
    {
        $tmp = $this->where(['uid' => $uid]);
        if ($type >= 0) {
            $tmp = $tmp->where(['status' => $type]);
        }
        $flags = $tmp->orderBy('create_time', 'desc')->get();
        return $flags;
    }
}
