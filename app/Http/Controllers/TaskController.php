<?php


namespace App\Http\Controllers;


use App\Component\ErrorCode;
use App\Service\Category;
use App\Service\Task as TaskSvc;

class TaskController extends Controller
{
    public function allTask(Category $categorySvc)
    {
        $uid = $this->getUid();
        if (empty($uid)) {
            return $this->json(ErrorCode::SUCCESS, 'No login');
        }
        $allTask = $categorySvc->allWithTask($uid);
        if (empty($allTask)) {
            return $this->json(ErrorCode::DATA_NULL, 'Empty');
        }
        $category = [];
        foreach ($allTask as $task) {
            if (!isset($category[$task->category_id])) {
                $category[$task->category_id] = [
                    'categoryId' => $task->category_id,
                    'categoryName' => $task->category_name,
                    'categoryImg' => $task->category_img
                ];
            }
            if (isset($task->task_id)) {
                $category[$task->category_id]['task'][] = [
                    'taskId' => $task->task_id,
                    'taskName' => $task->task_name,
                    'taskImg' => $task->task_img
                ];
            }
        }
        $result = [];
        $i = 0;
        $category = array_values($category);
        foreach ($category as $key => $item) {
            $i ++;
            $tmp[] = $item;
            if ($i %5 === 0 || $key == count($category) - 1) {
                $result[] = $tmp;
                $tmp = [];
            }
        }
        $rertunData = [
            'cateFormat' => $result,
            'all' => $category
        ];
        return $this->json(ErrorCode::SUCCESS, 'Success', $rertunData);
    }
}
