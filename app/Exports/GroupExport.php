<?php

namespace App\Exports;

use App\YxGroup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class GroupExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return YxGroup::all();
    }


    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        $members = $row->members()->get();
        $names = [];
        foreach ($members as $member) {
            $names [] = ['name' => $member->name, 'id' => $member->id];
        }

        return [
            isset($row->success()->id)? $row->success()->id : '',
            $row->id,
            $row->name,
            $row->description,
            $row->start_campus,
            $row->select_route,
            $row->up_to_standard,
            isset($names[0]) ? $names[0]['name'] : '',
            isset($names[0]) ? $names[0]['id'] : '',
            isset($names[1]) ? $names[1]['name'] : '',
            isset($names[1]) ? $names[1]['id'] : '',
            isset($names[2]) ? $names[2]['name'] : '',
            isset($names[2]) ? $names[2]['id'] : '',
            isset($names[3]) ? $names[3]['name'] : '',
            isset($names[3]) ? $names[3]['id'] : '',
            isset($names[4]) ? $names[4]['name'] : '',
            isset($names[4]) ? $names[4]['id'] : '',
            isset($names[5]) ? $names[5]['name'] : '',
            isset($names[5]) ? $names[5]['id'] : '',
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            '队伍参与活动号码',
            '队伍报名系统号码',
            '队伍名称',
            '队伍描述',
            '出发校区',
            '队伍路线',
            '达到要求时间',
            '队员1',
            '队员1-id'.
            '队员2',
            '队员2-id',
            '队员3',
            '队员3-id',
            '队员4',
            '队员4-id',
            '队员5',
            '队员5-id',
            '队员6',
            '队员6-id'

        ];
    }
}
