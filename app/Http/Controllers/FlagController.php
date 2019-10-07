<?php

namespace App\Http\Controllers;


use App\ErrorCode;
use App\Models\Flag;
use Illuminate\Http\Request;

class FlagController extends Controller
{
    public function add(Request $request, Flag $flag)
    {
        $category = $request->post('category', '');
        $task = $request->post('task', '');
        $taskSize = $request->post('taskSize', 0);
        if (empty($category) || empty($task) || empty($taskSize)) {
            return $this->json(ErrorCode::ERROR_PARAM_EMPTY, 'Params require is empty');
        }
        $flag->category = $category;
        $flag->task = $task;
        $flag->task_size = $taskSize;
        $result = $flag->save();

    }

    public function delete(Request $request)
    {
        $flagId = $request->post('taskId', 0);
        if (empty($flagId)) {
            return $this->json(ErrorCode::ERROR_PARAM_EMPTY, 'Params required is empty');
        }
    }

    public function modify()
    {

    }

    public function list(Request $request)
    {
        $type = $request->get('type', 0);
    }

    public function checkIn()
    {

    }


}
