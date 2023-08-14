<?php

namespace App\Console\Commands;

use App\Http\Traits\DateTimeTraits;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetDateTime extends Command
{
    use DateTimeTraits;

    // 静态变量
    protected $signature = 'getDateTime';
    protected $description = '清洗时间段数据';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 触发函数
     *
     * @author sunchanghao
     */
    public function handle()
    {
        $hour = $this->getDateTime()['hour'];

        $result = DB::table('data_reserve')->get();
        foreach ($result as $v) {
            if ($v->id > 5000) {
                foreach ($hour as $item) {
                    if ($v->hour == $item->id) {
                        DB::table('data_reserve')->where('id', $v->id)->update(['date_time' => $item->start_time . '-' . $item->end_time]);
                    }
                }
            }
        }
    }
}
