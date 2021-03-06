<?php


namespace App\Service;


use App\Models\CheckInLog;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Flag as FlagModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Flag extends Service
{
    const STATUS_ON_DOING = 0;
    const STATUS_DONE = 1;
    const STATUS_FAIL = 2;
    const STATUS_CANCEL = 3;

    const PERIOD_UNIT_MINUTE = 'i';
    const PERIOD_UNIT_HOUR = 'h';
    const PERIOD_UNIT_DAY = 'd';
    const PERIOD_UNIT_MONTH = 'm';
    const PERIOD_UNIT_YEAR = 'y';
    const PERIOD_UNIT_WEEK = 'w';
    const TASK_UNIT_GE = 'g';
    const TASK_UNIT_ZU = 'z';
    const TASK_UNIT_CI = 'c';

    const PERIOD_UNIT_MAP_TIME_UNIT = [
        self::PERIOD_UNIT_MINUTE => 'minute',
        self::PERIOD_UNIT_HOUR => 'hour',
        self::PERIOD_UNIT_DAY => 'day',
        self::PERIOD_UNIT_MONTH => 'month',
        self::PERIOD_UNIT_YEAR => 'year'
    ];

    const UNIT_MAP_NAME = [
        self::PERIOD_UNIT_MINUTE => '分钟',
        self::PERIOD_UNIT_HOUR => '小时',
        self::PERIOD_UNIT_DAY => '天',
        self::PERIOD_UNIT_MONTH => '月',
        self::PERIOD_UNIT_YEAR => '年',
        self::TASK_UNIT_GE => '个',
        self::TASK_UNIT_ZU => '组',
        self::TASK_UNIT_CI => '次'
    ];

    public static $canModifyColumn = [
        'task_size',
        'period'
    ];


    /* ------------------------------------------
     * 计算每个flag的下次打卡时间
     * ------------------------------------------
     */
    public function calCheckInTime(Collection $collection)
    {
        foreach ($collection as $flag) {
            $flag->nextCheckInTime = $this->calNextTime($flag, true);
        }
        return $collection;
    }

    public function calNextTime(\App\Models\Flag $flag, bool $needExtraInfo = false)
    {
        $todayWeek = date('w');
        $today = date('Y-m-d');
        $checkTime = date('Y-m-d', strtotime($flag->last_check_in_time));
        $checkDateString = decbin($flag->period);
        if ($needExtraInfo) {
            $flag->checkPeriod = $checkDateString;
        }
        $length = strlen($checkDateString);
        $nextCheckInTime = -1;
        $todayIndex = $todayWeek - (7 - $length);
        $todayIndex = $todayIndex < 0 ? 0 : $todayIndex;
        if ($checkTime !== $today && $checkDateString[$todayIndex] == 1) {
            $nextCheckInTime = 7 - $length + $todayIndex;
        } else {
            $got = false;
            for ($i = $todayIndex + 1; $i < $length; $i ++) {
                if ($checkDateString[$i] == 1) {
                    $stringWeek = 7 - $length + $i;
                    $got = true;
                    $nextCheckInTime = $stringWeek;
                    break;
                }
            }
            if (!$got) {
                $nextCheckInTime = 7 - $length;
            }
        }
        $diffDay = $nextCheckInTime - $todayWeek;
        if ($diffDay == 0 && $checkTime == $today) {
            $diffDay += 7;
        }
        $realDiffDay = $diffDay >= 0 ? $diffDay : ($diffDay + 7);
        $next = date('Y-m-d', strtotime("+{$realDiffDay} day"));
        return $next;
    }

    /* -----------------------
     * 获取集合中的所有分类
     * -----------------------
     */
    public function getAllInfo(Collection $collection, Category $category, Task $task) :array
    {
        if ($collection->isEmpty()) {
            return [];
        }
        $allCateId = [];
        $allTaskId = [];
        $periodResult = [];
        $collection->each(function ($value, $key)
        use (&$allCateId, &$allTaskId, &$periodResult) {
            $allCateId[] = $value['category_id'];
            $allTaskId[] = $value['task_id'];

        });
        $categoryInfos = $category->getCategoryInfos($allCateId);
        $cateResult = [];
        foreach ($categoryInfos as $cate) {
            $cateResult[$cate->id] = $cate;
        }
        $taskInfos = $task->getTaskInfosByIds($allTaskId);
        $taskResult = [];
        foreach ($taskInfos as $task) {
            $taskResult[$task->id] = $task;
        }
        $result = [
            'cate' => $cateResult,
            'task' => $taskResult,
            'period' => $periodResult
        ];
        return $result;
    }

    public function checkIn(FlagModel $flag, int $uid, CheckInLog $checkInLog)
    {
        $now = date('Y-m-d H:i:s');
        try {
            DB::beginTransaction();
            $checkInLog->uid = $uid;
            $checkInLog->flag_id = $flag->id;
            $checkInLog->check_time = $now;
            $result = $checkInLog->save();
            if (empty($result)) {
                DB::rollback();
                return false;
            }
            $flag->last_check_in_time = $now;
            $flag->check_num ++;
            $flagResult = $flag->save();
            if (empty($flagResult)) {
                DB::rollBack();
                return false;
            }
            DB::commit();
        } catch (\Exception $exception) {
            Log::error('check in error : '.$flag->id. 'info: '.$exception->getMessage());
            return false;
        }
        return true;
    }

    /* ---------------------------
     * 获取每个状态的flag数量
     * ---------------------------
     */
    public function getFlagCount(FlagModel $flag, int $uid, int &$total = 0) :array
    {
        $result = [];
        $statusNum = $flag->getUserFlagStatusNum($uid);
        if (!empty($statusNum)) {
            foreach ($statusNum as $item) {
                $result[$item->status] = $item->num;
                $total += $item->num;
            }
        }
        return $result;
    }
}
