<?php


namespace App\Console\Commands;


use App\Models\CheckInLog;
use App\Models\Flag;
use App\Models\FlagCheckStat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class StatCheckNum extends Command
{
    protected $signature = 'stat:check_num {date?}';

    protected $description = '统计打卡次数';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle($date = '')
    {
        $statDate = empty($date) ? date('Y-m-d', strtotime('-1 day')) : $date;
        $statNextDay = strtotime('Y-m-d', strtotime($statDate.' +a day'));
        $statCheckLog = CheckInLog::where('check_time', '>=', $statDate)
            ->where('check_time', '<', $statNextDay)
            ->get();
        $checkNum = [];
        if (!empty($statCheckLog)) {
            foreach ($statCheckLog as $log) {
                $checkNum[$log->uid][] = $log;
            }
        }
        $newFlag = Flag::where('create_time', '>=', $statDate)
            ->where('create_time', '<', $statNextDay)
            ->get();
        $newFlagNum = [];
        if (!empty($newFlag)) {
            foreach ($newFlag as $flag) {
                $newFlagNum[$flag->uid] = $flag;
            }
        }

        $result = FlagCheckStat::insert([

        ]);
    }
}
