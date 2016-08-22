<?php

namespace Eddie\Card\Facades;


use Illuminate\Support\Facades\Facade;

class Card extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Eddie\Card\CardManager';
    }
}