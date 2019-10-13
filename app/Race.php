<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Race extends Model
{
    protected $table = 'race';
    protected $primaryKey = 'race_id';

    public function homeSystem()
    {
        return $this->hasOne('App\System', 'system_id', 'home_system_id');
    }
}
