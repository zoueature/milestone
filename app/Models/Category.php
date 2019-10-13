<?php


namespace App\Models;


class Category extends Model
{
    protected $table = 'category';

    public function getInfos(array $catIds)
    {
        $infos = $this->whereIn('id', $catIds)->get();
        return $infos;
    }

    public function getAllByUid(int $uid)
    {
        $cates = $this->where('uid', '=', $uid)->get();
        return $cates;
    }


}
