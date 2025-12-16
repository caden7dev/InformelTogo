<?php

namespace App\Helpers;

class CurrencyHelper
{
    public static function format($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }
    
    public static function formatWithoutSymbol($amount)
    {
        return number_format($amount, 0, ',', ' ');
    }
    
    public static function getSymbol()
    {
        return 'FCFA';
    }
}