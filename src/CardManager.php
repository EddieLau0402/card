<?php

namespace Eddie\Card;


use Eddie\Card\Providers\Yuanhui;

class CardManager
{
    public function provider($provider)
    {
        switch (strtolower($provider)) {
            case 'yuanhui':
                $config = config('card.yuanhui');
                return new Yuanhui($config);

            default:
                throw new \Exception('找不到相应的provider', 500);
        }
    }
}