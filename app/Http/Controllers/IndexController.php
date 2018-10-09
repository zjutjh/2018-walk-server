<?php

namespace App\Http\Controllers;

use App\User;
use App\YxGroup;
use App\YxState;
use Illuminate\Http\Request;

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
}
