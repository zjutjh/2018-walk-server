<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserState extends Model
{
    /**
     * 获取状态对应的用户
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo('App\User');
    }
}
