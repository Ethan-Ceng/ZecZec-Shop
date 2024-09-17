<?php
// 事件定義檔案
return [
    'listen' => [
        'AppInit' => [],
        'HttpRun' => [],
        'HttpEnd' => [],
        'LogLevel' => [],
        'LogWrite' => [],
        'PaySuccess' => [
            \app\api\event\PaySuccess::class
        ],
        /*供應商等級*/
        'AgentUserGrade' => [
            \app\job\event\AgentUserGrade::class
        ],
        /*任務排程*/
        'JobScheduler' => [
            \app\job\event\JobScheduler::class
        ],
        /*訂單事件*/
        'Order' => [
            \app\job\event\Order::class
        ],
        /*領取優惠券事件*/
        'UserCoupon' => [
            \app\job\event\UserCoupon::class
        ],
        /*分銷商訂單*/
        'AgentOrder' => [
            \app\job\event\AgentOrder::class
        ],
        /*拼團訂單*/
        'AssembleBill' => [
            \app\job\event\AssembleBill::class
        ],
        /*砍價任務*/
        'BargainTask' => [
            \app\job\event\BargainTask::class
        ],
        /*使用者等級*/
        'UserGrade' => [
            \app\job\event\UserGrade::class
        ],
        /*邀請送好禮*/
        'Invitation' => [
            \app\job\event\Invitation::class
        ],
        /*微信小程式直播*/
        'Live' => [
            \app\job\event\Live::class
        ],
        /*預售訂單*/
        'AdvanceOrder' => [
            \app\job\event\AdvanceOrder::class
        ],
        /*任務中心*/
        'UserTask' => [
            \app\job\event\UserTask::class
        ],
        /*微信小程式直播商品*/
        'LiveProduct' => [
            \app\job\event\LiveProduct::class
        ],
    ],

    'subscribe' => [
    ],
];
