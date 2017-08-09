<?php

return [
    /*
     * 源慧API - config
     */
    'yuanhui' => [
        /*
         * 客户/账号
         */
        'cid' => env('YUANHUI_CID', ''),

        /*
         * 获取 API 校验
         */
        'appkey' => env('YUANHUI_APP_KEY', ''),

        /*
         * 服务地址
         */
        'url' => env('YUANHUI_API_DOMAIN', 'http://i.eswapi.com/API/'),

        /*
         * 资源 :
         */
        'resource' => [ // productid(资源ID) => 奖品资源名称

            '10202001' => '优酷会员周卡',
            '10202002' => '优酷会员月卡',
            '10202003' => '优酷会员季卡',
            '10202004' => '优酷会员半年卡',
            '10202005' => '优酷会员年卡',

            '10205016' => '全国中石化加油充值100元',
            '10205022' => '全国中石化加油充值200元',
            '10205017' => '全国中石化加油充值500元',
            '10205018' => '全国中石化加油充值1000元',
        ],

    ],

    /**
     * 供应商: 欧飞 (Ofpay)
     */
    'ofpay' => [
        'userid'  => env('OFPAY_USERID'),
        'userpws' => env('OFPAY_USERPWD'),
        'version' => env('OFPAY_VERSION', '6.0'),
        'key' => env('OFPAY_KEY'),

        /*
         * 测试模式下 用户信息
         */
        'test_mode' => env('OFPAY_TEST_MODE', false),
        'test_api_url' => 'http://apitest.ofpay.com',
        'test_userid' => 'A08566',
        'test_userpws' => 'of111111',
        'test_key' => 'OFCARD',

        'resources' => [
            /*
             * TODO
             */
        ],
    ],
];
