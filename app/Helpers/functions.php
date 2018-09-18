<?php
/**
 * Created by PhpStorm.
 * User: 70473
 * Date: 2018/9/18
 * Time: 15:42
 */

/**
 *  api json response
 * @param $code 状态码
 * @param string $msg 状态信息
 * @param null $data 返回数据
 * @return \Illuminate\Http\JsonResponse
 */
function RJM($code, $msg = '', $data = null) {
    $json = [
        'code' => $code,
        'msg' => $msg,
        'data' => $data
    ];
    return response()->json($json);
}