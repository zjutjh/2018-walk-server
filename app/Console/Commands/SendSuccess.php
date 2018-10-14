<?php

namespace App\Console\Commands;

use App\SuccessTeam;
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
            foreach ($members as $member) {
                $data = [
                    'first' => "模拟报名失败",
                    'keyword1' => '报名失败',
                    'keyword2' => '消息通知',
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark'   => '收到请忽略'
                ];
                $member->notify($data);
            }

        }
    }
}
