<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'status';

    public function payments()
    {
        return $this->hasMany('App\Models\Payment', 'status_id');
    }
}