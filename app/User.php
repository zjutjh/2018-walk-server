<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;



    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'openid', 'sex', 'id_card', 'height', 'birthday', 'sid'
    ];


    protected $appends = [
      'state'
    ];

    protected $fillable = [
      'name', 'id_card', 'email', 'sex', 'qq', 'wx_id', 'height', 'birthday'
    ];
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * 获取所在的组
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group() {
        return $this->belongsTo('App\YxGroup');
    }


    /**
     * 获取用户状态
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function state() {
        return $this->hasMany('App\UserState');
    }

    /**
     * 模板消息通知
     * @param $instance
     */
    public function notify($instance) {

    }

    /**
     * user state 访问器
     * @return mixed
     */
    public function getStateAttribute() {
        return $this->state()->first();
    }

    /**
     * 获取报名人数
     * @return mixed
     */
    static public function getUserCount() {
        return UserState::where('state', '>', 0)->count();
    }

    /**
     * 离开队伍
     * @return $this
     */
    public function leaveGroup() {
        $this->yx_group_id = null;
        $this->state()->update(['state' => 1]);
        return $this;
    }


    public function addGroup($groupId) {
        $this->yx_group_id = $groupId;
        $this->state()->update(['state' => 4]);
        return parent::save();
    }




}
