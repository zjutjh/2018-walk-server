<?php

namespace App\Http\Controllers;

use App\Exports\GroupExport;
use App\User;
use App\YxGroup;
use App\YxState;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class IndexController extends Controller
{
    public function indexInfo() {
        $yxState = YxState::where('id', 0)->first();
        $indexInfo = [
          'finish_time' => $yxState->finish_time,
          'apply_count' => User::getUserCount(),
          'team_count' => YxGroup::getTeamCount()
        ];

        return RJM(1, '请求成功', $indexInfo);

    }

    public function verifyApplyEnd() {
        $yxState = YxState::where('id', 0)->first();
        if ($yxState->state === 1) {
            return RJM(1, '关闭报名');
        }

        return RJM(-1, '报名正在进行');
    }


    public function count() {
        $apply_count = User::getUserCount();
        $team_count = YxGroup::getTeamCount();
        $upToTeam = YxGroup::whereNotNull('up_to_standard')->count();
//        $res  = '报名人数: ' . $apply_count . '<br>';
//        $res .= '队伍总数: ' . $team_count . '<br>';
//        $res .= '达到要求队伍数: '. $upToTeam . '<br>';
//        return $res;
        return view('count', ['apply_count' => $apply_count, 'team_count' => $team_count, 'upToTeam' => $upToTeam]);
    }


    /**
     * 获取队伍名单
     */
    public function teamDownload() {
        return Excel::download(new GroupExport(), '队伍名单.xlsx');

    }
}
