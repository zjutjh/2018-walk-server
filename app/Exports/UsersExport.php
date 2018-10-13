<?php

namespace App\Exports;

use App\User;
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
        return [
          $user->id,
          $user->name,
          $user->sex,
          $user->campus,
          $user->height,
          $user->birthday,
          $user->identity,
          $user->sid,
          $user->phone,
          $user->wx_id,
          $user->qq,
          $user->yx_group_id
        ];
    }


    public function headings(): array
    {
        return [
            'id',
            '姓名',
            '性别',
            '校区',
            '身高',
            '生日',
            '身份',
            '学号',
            '电话号码',
            '微信',
            'qq',
            '队伍号'
        ];
    }
}
