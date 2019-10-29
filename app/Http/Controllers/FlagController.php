<?php

namespace App\Http\Controllers;


use App\Component\ErrorCode;
use App\Models\CheckInLog;
use App\Models\Flag;
use App\Service\Category;
use App\Service\Flag as FlagSvc;
use App\Service\Task as TaskSvc;
use Illuminate\Http\Request;

class FlagController extends Controller
{
    public function add(Request $request, Flag $flag)
    {
        $uid = $this->getUid();
        if (empty($uid)) {
            return $this->json(ErrorCode::ERROR_NO_LOGIN, 'No login yet!');
        }
        $category = $request->post('categoryId', '');
        $task = $request->post('taskId', '');
        $period = $request->post('period', 0);
        $taskSize = $request->post('taskSize', 0);
        $taskSizeUnit = $request->post('taskSizeUnit', '');
        if (empty($category) ||
            empty($task) ||
            empty($taskSize) ||
            empty($taskSizeUnit) ||
            empty($period)
        ) {
            return $this->json(ErrorCode::ERROR_PARAM_EMPTY, 'Params require is empty');
        }
        $flag->uid = $uid;
        $flag->category_id = $category;
        $flag->task_id = $task;
        $flag->task_size = $taskSize;
        $flag->task_unit = $taskSizeUnit;
        $flag->period = $period;
        $result = $flag->save();
        if (!$result) {
            return $this->json(ErrorCode::ERROR_SQL, 'Add Fail');
        }
        return $this->json(ErrorCode::SUCCESS, 'Success');

    }

    public function delete(Request $request)
    {
        $uid = $this->getUid();
        if (empty($uid)) {
            return $this->json(ErrorCode::ERROR_NO_LOGIN, 'No login');
        }
        $flagId = $request->post('flagId', 0);
        if (empty($flagId)) {
            return $this->json(ErrorCode::ERROR_PARAM_EMPTY, 'Params required is empty');
        }
        $flag = Flag::find($flagId);
        if ($flag->uid != $uid) {
            return $this->json(ErrorCode::SUCCESS, 'Not owner');
        }
        $result = $flag->delete();
        if (empty($result)) {
            return $this->json(ErrorCode::ERROR_SQL, 'Delete error');
        }
        return $this->json(ErrorCode::SUCCESS, 'Success');
    }

    /* --------------------------------------
     *  修改flag信息
     * --------------------------------------
     */
    public function modify(Request $request)
    {
        $uid = $this->getUid();
        if (empty($uid)) {
            return $this->json(ErrorCode::ERROR_NO_LOGIN, 'No login');
        }
        $flagId = $request->input('flagId', 0);
        if (empty($flagId)) {
            return $this->json(ErrorCode::ERROR_PARAM_EMPTY, 'Task not found');
        }
        $flag = Flag::find($flagId);
        if (empty($flag)) {
            return $this->json(ErrorCode::DATA_NULL, 'Task not found');
        }
        $hasChange = false;
        foreach (FlagSvc::$canModifyColumn as $column) {
            $updateData = $request->input($column, null);
            if ($updateData !== null) {
                $hasChange = true;
                $flag->$column = $updateData;
            }
        }
        if (!$hasChange) {
            return $this->json(ErrorCode::SUCCESS, 'Success');
        }
        $result = $flag->save();
        if (empty($result)) {
            return $this->json(ErrorCode::ERROR_SQL, 'Update error');
        }
        return $this->json(ErrorCode::SUCCESS, 'Success');
    }

    public function list(Request $request, Flag $flag, FlagSvc $flagSvc, Category $categorySvc, TaskSvc $taskSvc)
    {
        $uid = $this->getUid();
        if (empty($uid)) {
            return $this->json(ErrorCode::ERROR_NO_LOGIN, 'No login');
        }
        $type = $request->get('type', 0);
        if (!in_array($type, [
            FlagSvc::STATUS_ON_DOING,
            FlagSvc::STATUS_DONE,
            FlagSvc::STATUS_FAIL,
            FlagSvc::STATUS_CANCEL
        ])) {
            return $this->json(ErrorCode::ERROR_PARAM_ILLEGAL, 'Illegal type');
        }
        $allFlags = $flag->getFlagsByUid($uid, $type);
        if (empty($allFlags)) {
            return $this->json(ErrorCode::DATA_NULL, 'empty');
        }
        $allFlagsWithNext = $flagSvc->calCheckInTime($allFlags);
        $extraInfos = $flagSvc->getAllInfo($allFlagsWithNext, $categorySvc, $taskSvc);
        $result = [];
        $today = date('Y-m-d');
        $todayCheckIn = [];
        foreach ($allFlagsWithNext as $item) {
            $cate = $extraInfos['cate'][$item->category_id] ?? null;
            $catName = empty($cate) ? '' : $cate->category_name;
            $task = $extraInfos['task'][$item->task_id] ?? null;
            $taskName = empty($task) ? '' : $task->task_name;
            $taskSizeUnitName = FlagSvc::UNIT_MAP_NAME[$item->task_unit] ?? '';
            $periodUnitName = FlagSvc::UNIT_MAP_NAME[$item->period_unit] ?? '';
            $flagItem = [
                'id' => $item->id,
                'img' => empty($task) ?
                    'http://a.hiphotos.baidu.com/image/pic/item/838ba61ea8d3fd1fc9c7b6853a4e251f94ca5f46.jpg' :
                    $task->cover_url,
                'uid' => $item->uid,
                'cateId' => $item->category_id,
                'cateName' => $catName,
                'taskId' => $item->task_id,
                'taskName' => $taskName,
                'taskSize' => $item->task_size,
                'taskSizeName' => $item->task_size . $taskSizeUnitName,
                'lastCheckIn' => $item->last_check_in_time ?: $item->create_time,
                'nextCheckIn' => $item->nextCheckInTime,
                'finalCheckIn' => $item->nextNextCheckInTime,
                'period' => $item->period,
                'periodName' => $item->period . $periodUnitName,
                'checkNum' => $item->check_num ?? 0,
                'destCheckNum' => $item->dest_check_num ?? 0
            ];
            $result[] = $flagItem;
            if ($flagItem['nextCheckIn'] == $today) {
                $todayCheckIn[] = $flagItem;
            }

        }
        $checkToday = $request->input('checkToday', 0);
        if ($checkToday) {
            return $this->json(ErrorCode::SUCCESS, 'Success', $todayCheckIn);
        }
        return $this->json(ErrorCode::SUCCESS, 'Success', $result);
    }

    public function checkIn(
        Request $request,
        Flag $flag,
        FlagSvc $flagSvc,
        CheckInLog $checkInLog
    )
    {
        $uid = $this->getUid();
        if (empty($uid)) {
            return $this->json(ErrorCode::ERROR_NO_LOGIN, 'No login');
        }
        $flagId = $request->input('flagId', 0);
        if (empty($flagId)) {
            return $this->json(ErrorCode::ERROR_PARAM_EMPTY, 'Flag id is required');
        }
        $flagInfo = $flag->find($flagId);
        if (empty($flagInfo)) {
            return $this->json(ErrorCode::DATA_NULL, 'Flag not found');
        }
        if ($flagInfo->uid != $uid) {
            return $this->json(ErrorCode::ERROR_NOT_OWNER, 'Not owner');
        }
        $today = date('Y-m-d');
        if ($flagSvc->calNextTime($flagInfo) !== $today) {
            return $this->json(ErrorCode::ERROR_NOT_CHECK_IN_TIME, 'Can not check in now');
        }
        $result = $flagSvc->checkIn($flagInfo, $uid, $checkInLog);
        if (empty($result)) {
            return $this->json(ErrorCode::ERROR_SQL, 'Check in fail');
        }
        return $this->json(ErrorCode::SUCCESS, 'Success');
    }
}
