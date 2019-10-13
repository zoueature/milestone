<?php


namespace App\Service;

use App\Models\Task as TaskModel;

class Task extends Service
{
    private $taskModel;

    public function __construct(TaskModel $task)
    {
        $this->taskModel = $task;
    }

    public function getTaskInfosByIds(array $ids)
    {
        if (empty($ids)) {
            return null;
        }
        $infos = $this->taskModel->getInfosByPrimary($ids);
        return $infos;
    }

    public function allWithCate(int $uid)
    {
        $allTask = $this->taskModel->allWithCate($uid);
        return $allTask;
    }
}
