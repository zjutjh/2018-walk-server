<?php

namespace App\Console\Commands;

use App\SuccessTeam;
use App\User;
use App\YxGroup;
use Illuminate\Console\Command;

class SendSuccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:success';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $groups = SuccessTeam::all();
        foreach ($groups as $group) {
            $team = YxGroup::find($group->yx_group_id);
            $members = $team->members()->get();
            $caption = User::find($team->captain_id);
            $mbs = $team->members()->where('id', '<>', $team->captain_id)->get();
            $mbs_string = "";
            foreach ($mbs as $mb) {
                $mbs_string .= "队员: {$mb->name}\r\n";
            }
            echo "{$team->id}:{$team->name}\n";
            foreach ($members as $member) {
                echo "{$member->id}:{$member->name}";
                $data = [
                    'first' => "{$member->name} 你好，在这里恭喜你成功报名参加精弘毅行",
                    'keyword1' => '报名成功',
                    'keyword2' => '消息通知',
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark'   => "恭喜你的队伍成功报名精弘毅行, 你当天参与活动的正式队伍号码为：{$team->success_id}"
                                  . "\r\n"
                                  . "队长：{$caption->name}\r\n"
                                  . $mbs_string . "毅行具体时间后续会在通知"
                ];
                echo $data['remark'] . "\n";
                $member->notify($data);
            }

        }
    }
}
