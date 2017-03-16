<?php

namespace Eddie\Card;


use Eddie\Card\Providers\Ofpay;
use Eddie\Card\Providers\Yuanhui;

class CardManager
{
    public function provider($provider)
    {
        switch (strtolower($provider)) {
            case 'yuanhui':
                $config = config('card.yuanhui');
                return new Yuanhui($config);

            case 'ofpay':
                dd(config('card.ofpay'));
                return new Ofpay(config('card.ofpay'));

            default:
                throw new \Exception('找不到相应的provider', 500);
        }
    }
}