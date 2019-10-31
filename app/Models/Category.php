<?php


namespace App\Models;


class Category extends Model
{
    protected $table = 'category';
    public $timestamps = false;

    public function getInfos(array $catIds)
    {
        $infos = $this->whereIn('id', $catIds)->get();
        return $infos;
    }

    public function getAllByUid(int $uid)
    {
        $cates = $this->where('uid', '=', $uid)
            ->where('status', '=', \App\Service\Category::STATUS_VALID)
            ->get();
        return $cates;
    }

    public function getUserCateCount(int $uid)
    {
        $count = $this->where('uid', '=', $uid)
            ->where('status', '=', \App\Service\Category::STATUS_VALID)
            ->count();
        return $count;
    }

    public function allWithTask(int $uid)
    {
        $all = $this->select([
            'task.id as task_id',
            'category.id as category_id',
            'category_name',
            'category.cover_url as category_img',
            'task_name',
            'task.cover_url as task_img'
        ])
            ->leftJoin('task', 'category.id', '=', 'task.category_id')
            ->where('category.uid', '=', $uid)
            ->where('category.status', '=', \App\Service\Category::STATUS_VALID)
            ->get();
        return $all;
    }

}
