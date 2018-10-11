<?php

namespace App;

use App\Jobs\SendTemplate;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{



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
      'name', 'id_card', 'email', 'sex', 'qq', 'wx_id', 'height', 'birthday', 'phone'
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
        return $this->belongsTo('App\YxGroup', 'yx_group_id');
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
     */
    public function notify($data) {
        $config = [
            'openid' => $this->openid,
            'url'    => url()->current(),
            'data'   => $data
        ];
        dispatch(new SendTemplate($config));
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
        return parent::save();
    }


    /**
     * 设置身份证信息
     * @param $value
     */
    public function setIdCardAttribute($value)
    {
        $this->attributes['sex'] = iidGetSex($value);
        $this->attributes['birthday'] = iidGetBirthday($value);
        $this->attributes['id_card'] = md5(strtoupper($value));

    }


    /**
     * 加入队伍
     * @param $groupId
     * @return bool
     */
    public function addGroup($groupId) {
        $this->yx_group_id = $groupId;
        $this->state()->update(['state' => 4]);
        return parent::save();
    }




}
