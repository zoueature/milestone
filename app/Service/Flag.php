<?php


namespace App\Service;


use Illuminate\Database\Eloquent\Collection;

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
        $now = time();
        foreach ($collection as $flag) {
            $lastCheckInTime = $flag->last_check_in_time;
            if (empty($lastCheckInTime)) {
                $lastCheckInTime = $flag->create_time;
            }
            $periodUnit = self::PERIOD_UNIT_MAP_TIME_UNIT[$flag->period_unit] ?? '';
            if ($periodUnit === 'w') {
                $diffDay = $flag->period - date('w');
                $diffPeriod = $diffDay >= 0 ? $diffDay : $diffDay + 7;
                $nextCheckInTime =  strtotime($lastCheckInTime."+{$diffPeriod} day");
            } else {
                for ($i =1; $i < 10000; $i ++) {
                    $diffPeriod = $flag->period * $i;
                    $nextCheckInTime = strtotime($lastCheckInTime."+{$diffPeriod} {$periodUnit}");
                    if ($nextCheckInTime > $now) {
                        break;
                    }
                }
            }
            $flag->nextCheckInTime = date('Y-m-d H:i:s', $nextCheckInTime);
        }
        return $collection;
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

    public function calDiffTimeToCheckIn(int $period, string $periodUnit) :int
    {

    }
}
