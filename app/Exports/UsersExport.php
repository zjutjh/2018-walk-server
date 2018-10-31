<?php

namespace App\Exports;

use App\SuccessTeam;
use App\User;
use App\YxGroup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $user = User::whereHas('state', function($query) {
            $query->where('state', '>', 0)->where('state', '<>', 5);
        })->get();
        return $user;
    }


    /**
     * @param User $user
     * @return array
     */
    public function map($user): array
    {

        $success = SuccessTeam::where('yx_group_id', $user->yx_group_id)->first();
        $group = YxGroup::find($user->yx_group_id);
        return [
          $user->id,
          $user->name,
          $user->sex,
          $user->campus,
          !$group ? '未组队' : $group->select_route,
          $user->height,
          $user->birthday,
          $user->identity,
          $user->sid,
          $user->phone,
          $user->wx_id,
          $user->qq,
          $user->yx_group_id,
          !$success ?  '等待报名结束': $success->id,
            !$group ? '未组队' : $group->captain_id,
        ];
    }


    public function headings(): array
    {
        return [
            'id',
            '姓名',
            '性别',
            '校区',
            '路线',
            '身高',
            '生日',
            '身份',
            '学号',
            '电话号码',
            '微信',
            'qq',
            '系统队伍号',
            '正式队伍号',
            '队长id'
        ];
    }
}
