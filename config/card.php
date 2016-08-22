<?php

return [
    /*
     * 源慧API - config
     */
    'yuanhui' => [
        /*
         * 客户/账号
         */
        'cid' => env('YUANHUI_CID'),

        /*
         * 获取 API 校验
         */
        'appkey' => env('YUANHUI_APP_KEY'),

        /*
         * 服务地址
         */
        'url' => env('YUANHUI_API_DOMAIN'),

        /*
         * 资源 :
         */
        'resource' => [ // productid(资源ID) => 奖品资源名称
            '10202001' => '优酷会员周卡',
            '10202002' => '优酷会员月卡',
            '10202003' => '优酷会员季卡',
            '10202004' => '优酷会员半年卡',
            '10202005' => '优酷会员年卡',
        ],

    ],
];
