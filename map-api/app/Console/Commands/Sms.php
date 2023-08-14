<?php

namespace App\Console\Commands;

use App\Services\ReserveService;
use Illuminate\Console\Command;
use App\Jobs\SendMessageAsyncJob;

class Sms extends Command
{
    // 静态变量
    protected $signature = 'sms:sendSms';
    protected $description = '短信预约发送';
    public static $reserveService = null;

    public function __construct(ReserveService $reserveService)
    {
        parent::__construct();
        self::$reserveService = $reserveService;
    }

    /**
     * 触发函数
     *
     * @author sunchanghao
     */
    public function handle()
    {
        $this->smsHandle();
    }

    /**
     * 任务调度创建订单
     *
     * @author sunchanghao
     */
    public function smsHandle()
    {
        // 打日志处理
        app('log')->info('发送短信开始' . date('Y-m-d H:i:s', time()));
        // 获取短信
        $telephones = self::$reserveService->getSendSms();
        app('log')->info('总条数：' . count($telephones));

        if (count($telephones) > 0) {
            foreach ($telephones as $v) {
                dispatch(new SendMessageAsyncJob($v->telephone, 'SMS_213551102'));
                app('log')->info($v->telephone);
            }
        }
        // dispatch(new SendMessageAsyncJob('13418515003', 'SMS_206880230'));
        // dispatch(new SendMessageAsyncJob('18611224596', 'SMS_206880230'));
    }
}
