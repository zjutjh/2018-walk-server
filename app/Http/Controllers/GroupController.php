<?php

namespace App\Http\Controllers;

use App\User;
use App\YxApply;
use App\YxGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{

    /**
     * 队伍列表
     */
    public function groupLists()
    {
        $groups = YxGroup::paginate(15);
        return RJM(1, '获取数据成功', $groups);
    }

    /**
     * 创建队伍
     */
    public function createGroup(Request $request)
    {
        $teamInfo = $request->all();
        if (strlen($teamInfo['name']) > 180 ||
            strlen($teamInfo['description']) > 180
        ) {
            return RJM(-1, '名称或描述过长');
        }

        $user = Auth::user();
        if (!!$user->yx_group_id) {
            return RJM(-1, '你已经拥有队伍');
        }
        $teamInfo['captain_id'] = $user->id;
        $group = YxGroup::create($teamInfo);
        $user->yx_group_id = $group->id;
        $user->save();
        $uState = $user->state()->first();
        $uState->state = 3;
        $uState->save();
        $data = [
            'first' => '你已经创建了一个队伍',
            'keyword1' => '队伍创建',
            'keyword2' => '创建成功',
            'keyword3' => date('Y-m-d H:i:s', time()),
            'remark' => '快邀请大家来加入你的队伍把！ 点击查看详情'
        ];
        $user->notify($data);
        return RJM(1, '创建成功');
    }


    public function updateGroupInfo(Request $request) {
        $user = Auth::user();
        $teamInfo = $request->all();
        if (strlen($teamInfo['name']) > 180 ||
            strlen($teamInfo['description']) > 180
        ) {
            return RJM(-1, '名称或描述过长');
        }

        $yxGroup = YxGroup::where('captain_id', $user->id)->first();
        $yxGroup->fill($teamInfo);
        $yxGroup->save();

        return RJM(1, '更新成功');


    }


    /**
     * 解散队伍
     */
    public function breakGroup()
    {
        $user = Auth::user();
        $group = $user->group()->first();
        if ($user->id !== $group->captain_id) {
            return RJM(-1, '你没有权限删除队伍');
        }
        $group->delete();
        $data = [
            'first' => '你已经解散了你的队伍',
            'keyword1' => '队伍解散',
            'keyword2' => '解散成功',
            'keyword3' => date('Y-m-d H:i:s', time()),
            'remark' => '如果你还想创建一个队伍，点击详情哦'
        ];
        $user->notify($data);
        return RJM(1, '删除成功');


    }

    /**
     * 申请入队
     */
    public function doApply(Request $request)
    {
        $groupId = $request->get('groupId');
        $group = YxGroup::where('id', $groupId)->first();
        if ($group->captain_id == Auth::user()->id) {
            return RJM(-1, '这是你自己的队伍');
        }
        YxApply::create(['apply_team_id' => $groupId, 'apply_id' => Auth::user()->id]);

        $group->notifyCaptain();
        $user = Auth::user();
        $data = [
            'first' => "你正在申请 {$group->name} 的队伍",
            'keyword1' => '队伍申请',
            'keyword2' => '等待同意',
            'keyword3' => date('Y-m-d H:i:s', time()),
            'remark' => '耐心等待队长同意哦'
        ];
        $user->notify($data);
        $user->state()->update(['state' => 2]);
        return RJM(1, '正在申请中');
    }

    /**
     * 撤回申请
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteApply() {
        $user = Auth::user();
        $apply_id = $user->id;
        $user->state()->update(['state' => 1]);
        $uState = $user->state()->first();
        $uState->state = 1;
        $uState->save();
        YxApply::where('apply_id', $apply_id)->delete();
        return RJM(1, '撤回成功');
    }


    /**
     * 获取申请队伍信息
     */
    public function getApplyTeam() {
        $apply_id = Auth::user()->id;
        $yxAplly = YxApply::where('apply_id', $apply_id)->first();
        $group = YxGroup::where('id', $yxAplly->apply_team_id)->first();
        return RJM(1, '查询成功', $group);

    }

    /**
     * 离开队伍
     */
    public function leaveGroup()
    {
        $user = Auth::user();

        $user->leaveGroup();
        $data = [
            'first' => "你已经离开了一个队伍",
            'keyword1' => '离开队伍',
            'keyword2' => '离开成功',
            'keyword3' => date('Y-m-d H:i:s', time()),
            'remark' => '如果你还想加入一个队伍，请进入队伍列表寻找哦'
        ];
        $user->notify($data);
        $group = $user->group()->first();
        if ($group->toArray()['members'] < 4) {
            $group->up_to_standard = null;
            $group->save();
        }
        return RJM(1, '离开队伍');
    }


    /**
     * 锁定队伍
     */
    public function lockGroup()
    {
        $user = Auth::user();
        if ($user->state()->first()->state == 3) {
            $group = $user->group()->first();
            $group->is_lock = true;
            $group->save();

            $data = [
                'first' => "你已经锁定了你的队伍",
                'keyword1' => '队伍锁定',
                'keyword2' => '锁定成功',
                'keyword3' => date('Y-m-d H:i:s', time()),
                'remark' => '如果想解除锁定，点击详情进入队伍列表解锁哦'
            ];
            $user->notify($data);
            return RJM(1, '已经锁定队伍');
        }


        return RJM(-1, '你没有权限');

    }


    /**
     * 解锁队伍
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlockGroup()
    {
        $user = Auth::user();
        if ($user->state()->first()->state == 3) {
            $group = $user->group()->first();
            $group->is_lock = false;
            $group->save();
            $data = [
                'first' => "你已经解锁你的队伍",
                'keyword1' => '队伍解锁',
                'keyword2' => '解锁成功',
                'keyword3' => date('Y-m-d H:i:s', time()),
                'remark' => '点击详情，查看队伍信息'
            ];
            $user->notify($data);
            return RJM(1, '解锁成功');
        }

        return RJM(-1, '你没有权限');
    }

    /**
     * 同意加入
     */
    public function agreeMember(Request $request)
    {
        $uGroup = Auth::user()->group()->first();
        if ($uGroup->members === $uGroup->num) {

            return RJM(-1, '队伍已经达到上限');
        }

        $apply_id = $request->get('apply_id');
        $groupId = Auth::user()->yx_group_id;
        $user = User::where('id', $apply_id)->first();
        if ($user->state()->first()->state != 2) {
            return RJM(-1, '该申请者已经撤回申请了');
        }
        $user->addGroup($groupId);
        YxApply::where('apply_id', $apply_id)->delete();
        $data = [
            'first' => "你申请的队伍已经同意了你的申请",
            'keyword1' => '队伍申请',
            'keyword2' => '申请成功',
            'keyword3' => date('Y-m-d H:i:s', time()),
            'remark' => '点击详情，查看队伍信息'
        ];
        $user->notify($data);
        $group = $user->group()->first();

        if ($group->toArray()['members'] >= 4) {
            if (!$group->up_to_standard) {
                $group->up_to_standard = Carbon::now()->toDateTimeString();
                $group->save();
            }
        }

        return RJM(1, '同意成功');


    }

    /**
     * 拒绝加入
     */
    public function refuseMember(Request $request)
    {
        $apply_id = $request->get('apply_id');
        YxApply::where('apply_id', $apply_id)->delete();
        $user = User::where('id', $apply_id)->first();
        if ($user->state()->first()->state != 2) {
            return RJM(-1, '该申请者已经撤回申请了');
        }
        $uState = $user->state()->first();
        $uState->state = 1;
        $uState->save();
        $data = [
            'first' => "你申请的队伍已经拒绝了你的申请",
            'keyword1' => '队伍申请',
            'keyword2' => '申请失败',
            'keyword3' => date('Y-m-d H:i:s', time()),
            'remark' => '可以进入队伍列表找寻其他你希望加入的队伍哦'
        ];
        $user->notify($data);
        return RJM(1, '拒绝成功');
    }

    /**
     * 搜索队伍
     */
    public function searchTeam(Request $request)
    {
        $query_string = $request->get('query_string');
        if (!$query_string) {
            return $this->groupLists();
        }
        $groups = YxGroup::where('name', 'like', "%{$query_string}%")->orWhere('id', $query_string)->paginate(100);
        return RJM(1, '搜索成功', $groups);
    }


    /**
     * 查询申请者列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApplyList()
    {
        $groupId = Auth::user()->yx_group_id;
        $applyModels = YxApply::where('apply_team_id', $groupId)->get();
        $userId = [];
        foreach ($applyModels as $applyModel) {
            $userId [] = $applyModel->apply_id;
        }

        $applyUsers = User::find($userId);
        return RJM(1, '请求成功', $applyUsers);
    }


    /**
     * 查询申请者数量
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApplyCount()
    {
        $groupId = Auth::user()->yx_group_id;
        $applyModels = YxApply::where('apply_team_id', $groupId)->count();
        return RJM(1, '请求成功', $applyModels);
    }

    /**
     * 查询队伍信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGroupInfo()
    {
        $user = Auth::user();
        $group = $user->group()->first();
        return RJM(1, '获取成功', $group);
    }

    /**
     * 获取队伍成员信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGroupMembers() {
        $user = Auth::user();

        $members = $user->group()->first()->members()->get();
        return RJM(1, '查询成功', $members);

    }


    /**
     * 踢出队伍
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMember(Request $request) {
        $delete_id = $request->get('delete_id');
        $user = User::where('id', $delete_id)->first();
        $user->leaveGroup();
        $data = [
            'first' => "你已经被移出了队伍",
            'keyword1' => '移出队伍',
            'keyword2' => '移出成功',
            'keyword3' => date('Y-m-d H:i:s', time()),
            'remark' => '如果你还想加入一个队伍，请进入队伍列表寻找哦'
        ];
        $user->notify($data);
        $cUser = Auth::user();
        $group = $cUser->group()->first();
        if ($group->toArray()['members'] < 4) {
            $group->up_to_standard = null;
            $group->save();
        }
        return RJM(1, '踢出队伍');

    }


}
