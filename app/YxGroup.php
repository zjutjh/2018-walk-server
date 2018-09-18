<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YxGroup extends Model
{
    public function members() {
        return $this->hasMany('App\User');
    }


}
