<?php

namespace App\Http\Controllers;

use App\Services\UserCenterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * 创建详细信息
     */
    public function detailInfo(Request $request) {
        $detail = $request->all();

        if (strlen($detail['name']) > 255 ||
            strlen($detail['qq']) > 255 ||
            strlen($detail['email']) > 255 ||
            strlen($detail['wx_id']) > 255
        ) {
            return RJM(-1, '字段过长');
        }
        if ($detail['type'] == 'create' && $detail['height'] > 300) {
            return RJM(-1, '身高过大');
        }

        $user = Auth::user();
        $user->fill($detail);
        $user->save();
        if ($detail['type'] == 'create') {
            $uState = $user->state()->first();
            $uState->state = 1;
            $uState->save();
        }
        return RJM(1, '更新信息成功');

    }


    /**
     * 验证学生身份； 改变状态为已经报名
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyStu(Request $request) {
        $sid = $request->get('sid');
        $passwd = $request->get('passwd');
        $identity = $request->get('identity');
        $uCenter = new UserCenterService();

        if  (!$error = $uCenter->checkJhPassport($sid, $passwd)) {
            $error = $uCenter->getError();
            return RJM(-1, $error ?  $error: '用户或密码错误');
        }

        $user = Auth::user();
        $user->sid = $sid;
        $user->identity = $identity;
        if (!$user->id_card) {
            $uState = $user->state()->first();
            $uState->state = 5;
            $uState->save();
        }

        $user->save();

        return RJM(1, '登录成功,请完善信息');
    }

    /**
     * 确定身份： 教职工 校友 其他; 改变状态为已经报名
     */
    public function verifyOther(Request $request) {
        $identity = $request->get('identity');

        $user = Auth::user();
        $user->identity = $identity;
        if (!$user->id_card) {
            $uState = $user->state()->first();
            $uState->state = 5;
            $uState->save();
        }
        $user->save();

        return RJM(1, '登录成功,请完善信息');

    }


}
