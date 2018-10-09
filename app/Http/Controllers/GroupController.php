<?php

namespace App\Http\Controllers;

use App\User;
use App\YxApply;
use App\YxGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{

    /**
     * 队伍列表
     */
    public function groupLists() {
        $groups = YxGroup::paginate(15);
        return RJM(1, '获取数据成功', $groups);
    }

    /**
     * 创建队伍
     */
    public function createGroup(Request $request){
        $teamInfo = $request->all();
        $group = YxGroup::create($teamInfo);
        $user = Auth::user();
        $group->captain_id = $user->id;
        $uState = $user->state()->first();
        $uState->state = 3;
        $group->save();
        $uState->save();
        return RJM(1, '创建成功');
    }


    /**
     * 解散队伍
     */
    public function breakGroup() {
        $user = Auth::user();
        $group = $user->group()->first();
        if ($user->id !== $group->captain_id) {
            return RJM(-1, '你没有权限删除队伍');
        }
        $group->delete();
        return RJM(1, '删除成功');


    }

    /**
     * 申请入队
     */
    public function doApply(Request $request) {
        $groupId = $request->get('groupId');
        YxApply::create(['apply_team_id' => $groupId, 'apply_id' => Auth::user()->id]);
        $group = YxGroup::where('id', $groupId)->first();
        $group->notifyCaptain();
        return RJM(1, '正在申请中');
    }

    /**
     * 离开队伍
     */
    public function leaveGroup() {
        $user = Auth::user();
        $user->levelGroup();
        return RJM(1, '离开队伍');
    }


    /**
     * 锁定队伍
     */
    public function lockGroup() {
        $user = Auth::user();
        if ($user->state()->first()->state == 3) {
            $user->group()->update(['is_lock' => true]);
            RJM(1, '已经锁定队伍');
        }

        return RJM(-1, '你没有权限');

    }

    /**
     * 同意加入
     */
    public function agreeMember(Request $request) {
        $apply_id = $request->get('apply_id');
        $groupId = Auth::user()->yx_group_id;
        $user = User::where('id', $apply_id)->first();
        $user->addGroup($groupId);
        YxApply::where('apply_id', $apply_id)->delete();

        // todo: add template
        $user->notify();

        return RJM(1, '同意成功');


    }

    /**
     * 拒绝加入
     */
    public function refuseMember(Request $request) {
        $apply_id = $request->get('apply_id');
        YxApply::where('apply', $apply_id)->delete();
        $user = User::where('id', $apply_id)->first();
        return RJM(1, '拒绝成功');
    }

    /**
     * 搜索队伍
     */
    public function searchTeam(Request $request) {
        $query_string = $request->get('query_string');
        $groups = YxGroup::where('name', 'like', "%{$query_string}%")->orWhere('id', $query_string)->paginate(15);
        return RJM(1, '搜索成功', $groups);
    }


    public function getApplyList() {
        $groupId = Auth::user()->yx_group_id;
        $applyModels = YxApply::where('apply_team_id', $groupId)->get();
        $userId = [];
        foreach ($applyModels as $applyModel) {
            $userId [] = $applyModel->apply_id;
        }

        $applyUsers = User::find($userId);
        return RJM(1, '请求成功', $applyUsers);
    }

    public function getApplyCount() {
        $groupId = Auth::user()->yx_group_id;
        $applyModels = YxApply::where('apply_team_id', $groupId)->count();
        return RJM(1, '请求成功', $applyModels);
    }




}
