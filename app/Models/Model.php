<?php


namespace App\Models;


class Model extends \Illuminate\Database\Eloquent\Model
{
    public function getInfosByPrimary(array $ids)
    {
        $infos = $this->whereIn($this->primaryKey, $ids)->get();
        return $infos;
    }
}
