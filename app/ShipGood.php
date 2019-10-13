<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShipGood extends Model
{
    protected $table = 'ship_good';
    protected $primaryKey = 'ship_good_id';

    public function goods()
    {
        return $this->hasMany('App\ShipGood', 'ship_id', 'ship_id');
    }
}
