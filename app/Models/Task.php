<?php


namespace App\Models;



class Task extends Model
{
    protected $table = 'task';

    public function allWithCate(int $uid)
    {
        $all = $this->select([
                'task.id as task_id',
                'category.id as category_id',
                'category_name',
                'category.cover_url as category_img',
                'task_name',
                'task.cover_url as task_img'
            ])
            ->join('category', 'category.id', '=', 'task.category_id')
            ->where('task.uid', '=', $uid)
            ->get();
        return $all;
    }
}
