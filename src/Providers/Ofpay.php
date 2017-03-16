<?php

namespace Eddie\Card\Providers;


use Eddie\Card\CardInterface;
use Eddie\Card\Util;

class Ofpay implements CardInterface
{
    use Util;


    /**
     * 提卡接口（order.do）
     * 此接口依据用户的请求返回卡号密码信息
     */
    const API_FLOW_ORDER = '/order.do';

    /**
     * 提货商品的数量上限: 200
     */
    const CARDNUM_MAX = 200;


    /**
     * API url
     *
     * @author Eddie
     *
     * @var mixed
     */
    protected $api_url;

    /**
     * SP编码
     *
     * @author Eddie
     *
     * @var mixed
     */
    protected $userid;

    /**
     * SP接入密码
     *
     * @author Eddie
     *
     * @var mixed
     */
    protected $userpwd;

    /**
     * 签名用key; 默认为:"OFCARD", 实际上线时可以修改; 不在接口间进行传送.
     *
     * @author Eddie
     *
     * @var
     */
    protected $key;

    /**
     * 订单号
     *
     * @author Eddie
     *
     * @var
     */
    protected $orderId;

    /**
     * 订单时间
     *
     * @author Eddie
     *
     * @var
     */
    protected $orderTime;

    /**
     * 所需提货商品的编码(需和CP商品编码一一对应)
     *
     * @author Eddie
     *
     * @var mixed
     */
    protected $cardid;

    /**
     * 所需提货商品的数量(限制200),cardnum不传默认为1
     *
     * @author Eddie
     *
     * @var mixed
     */
    protected $cardnum = 1;

    /**
     * 手机号
     *
     * @author Eddie
     *
     * @var
     */
    protected $mobile;

    /**
     * 版本
     *
     * @author Eddie
     *
     * @var mixed
     */
    protected $version;

    protected $resources;


    /**
     * Ofpay constructor.
     *
     * @author Eddie
     *
     * @param $config
     * @throws \Exception
     */
    public function __construct($config)
    {
        if (!is_array($config))
            throw new \Exception('请设置好参数并且配置参数必须是数组', 500);

        if ($config['test_mode']) {
            $this->api_url = $config['test_api_url'];
            $this->userid = $config['test_userid'];
            $this->userpwd = $config['test_userpws'];
            $this->key = $config['test_key'];
        }
        else {
            if (!$config['userid'])
                throw new \Exception('缺少userid参数', 500);

            if (!$config['userpws'])
                throw new \Exception('缺少userpws参数', 500);

            if (!$config['key'])
                throw new \Exception('缺少key参数', 500);


            /// http://AXXXX.api2.ofpay.com
            $this->api_url = 'http://' . $config['userid'] . '.api2.ofpay.com';
            $this->userid = $config['userid'];
            $this->userpwd = $config['userpws'];
            $this->key = $config['key'];
        }
        $this->version = $config['version'];
        $this->resources = $config['resources'];
    }


    /**
     * 提取
     *
     * @author Eddie
     *
     * @return mixed
     * @throws \Exception
     */
    public function exchange()
    {
        /*
         * Invalid
         */
        if (!$this->orderId) {
            throw new \Exception('订单号不能为空', 422);
        }
        if (!$this->mobile) {
            throw new \Exception('手机号不能为空', 422);
        }

        /*
         * 参数列表: [ 请求参数 | 是否必填 | 说明 ]
         *
         * userid          | 是 | SP编码(如:A00001)
         * userpws         | 是 | SP接入密码(为账户密码的MD5值)
         * cardid          | 是 | 所需提货商品的编码(需和CP商品编码一一对应)
         * cardnum         | 是 | 所需提货商品的数量(限制200),cardnum不传默认为1
         * sporder_id      | 是 | Sp商家的订单号(商家传给欧飞的订单信息需唯一)
         * sporder_time    | 是 | 订单时间(yyyyMMddHHmmss; 如: 20070323140214)
         * md5Str          | 是 | MD5后字符串(验证规则说明)
         * phone           | 否 | 收货手机号 (可为空, 不参与MD5验证)
         * email           | 否 | 收货邮箱地址 (可为空, 不参与MD5验证)
         * version         | 是 | 固定值为: 6.0 (不参与MD5验证)
         */

        $this->orderTime = date('YmdHis');

        $params = [
            'userid'  => $this->userid,
            'userpws' => md5($this->userpwd),
            'cardid'  => $this->cardid,
            'cardnum' => $this->cardnum,
            'sporder_id' => $this->orderId,
            'sporder_time' => $this->orderTime
        ];
        $params['md5Str'] = $this->sign($params);

        $params['phone'] = $this->mobile;
        $params['version'] = $this->version;

        $url = $this->api_url . self::API_FLOW_ORDER;

        \Log::info('Request url: '.$url);
        \Log::info('Request params:');
        \Log::info($params);

        $response = $this->request($url, $params, 'POST');

        return $this->parse2Json($response);
    }

    /**
     * Make a signature.
     *
     * @author Eddie
     *
     * @param $params
     * @return string
     */
    protected function sign($params)
    {
        /**
         * md5Str检验码的计算方:
         *
         * netType 为空的话，不参与MD5验证，不为空的话参与MD5验证;
         * 包体= userid + userpws + cardid + cardnum + sporder_id + sporder_time
         *
         * 1: 对: “包体+KeyStr” 这个串进行md5 的32位值. 结果大写
         * 2: KeyStr 默认为 OFCARD, 实际上线时可以修改。
         * 3: KeyStr 不在接口间进行传送。
         */

        if (is_array($params)) {
            $params = implode('', $params);
        }
        return strtoupper(md5($params . $this->key));
    }

    /**
     * Parse API data , and convert to JSON.
     *
     * @author Eddie
     *
     * @param $response
     * @return mixed
     */
    protected function parse2Json($response)
    {
        $data = simplexml_load_string($response);

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    /**
     * Setter - set mobile
     *
     * @author Eddie
     *
     * @param $mobile
     * @return $this
     */
    public function mobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
    }

    /**
     * Setter - set order_id.
     *
     * @author Eddie
     *
     * @param $orderId
     * @return $this
     */
    public function order_id($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * 通过提货商品类型 获取对应提货商品的编码(cardid)
     *
     * @author Eddie
     *
     * @param $type
     * @return $this
     */
    public function card($type)
    {
        $resources = array_flip($this->resources);

        if (!array_key_exists($type, $resources)) {
            throw new \Exception('没有对应的资源!', 500);
        }

        $this->cardid = $resources[$type];

        return $this;
    }

}