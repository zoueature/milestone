<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected $table = 'flag';
    public $timestamps = false;

    public function getFlagsByUid(int $uid)
    {
        $flags = $this->where(['uid' => $uid])->get();
        return $flags;
    }
}
