<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * 微信回调
     */
    public function oauth() {

    }

    /**
     *  通过openid 自动登陆
     * @param Request $request
     */
    public function wxLogin(Request $request) {
        $code = $request->get('code');


    }

}
