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

}
