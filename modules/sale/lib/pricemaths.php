<?php

namespace Bitrix\Sale;

use Bitrix\Main;

class PriceMaths
{
    private static $valuePrecision = null;

    /**
     * @param $value
     *
     * @return float
     * @throws Main\ArgumentNullException
     */
    public static function roundPrecision($value)
    {
        if (!isset(self::$valuePrecision)) {
            self::$valuePrecision = (int)Main\Config\Option::get('sale', 'value_precision');
            if (self::$valuePrecision <= 0) {
                self::$valuePrecision = 2;
            }
        }

        return round(doubleval($value), self::$valuePrecision);
    }

    /**
     * @param $price
     * @param $currency
     *
     * @return float
     * @deprecated Use \Bitrix\Sale\PriceMaths::roundPrecision instead it
     *
     */
    public static function roundByFormatCurrency($price, $currency)
    {
        return floatval(SaleFormatCurrency($price, $currency, false, true));
    }
}