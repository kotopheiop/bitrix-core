<?php

namespace Bitrix\Sale\Cashbox;

use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

/**
 * Class FullPrepaymentReturnCashCheck
 * @package Bitrix\Sale\Cashbox
 */
class FullPrepaymentReturnCashCheck extends FullPrepaymentCheck
{
    /**
     * @return string
     */
    public static function getType()
    {
        return 'fullprepaymentreturncash';
    }

    /**
     * @return string
     * @throws Main\NotImplementedException
     */
    public static function getCalculatedSign()
    {
        return static::CALCULATED_SIGN_CONSUMPTION;
    }

    /**
     * @return string
     */
    public static function getName()
    {
        return Main\Localization\Loc::getMessage('SALE_CASHBOX_FULLPREPAYMENT_RETURN_CASH_NAME');
    }

    /**
     * @return array
     */
    protected function extractDataInternal()
    {
        $result = parent::extractDataInternal();

        if (isset($result['PAYMENTS'])) {
            foreach ($result['PAYMENTS'] as $i => $payment) {
                $result['PAYMENTS'][$i]['IS_CASH'] = 'Y';
                $result['PAYMENTS'][$i]['TYPE'] = static::PAYMENT_TYPE_CASH;
            }

        }

        return $result;
    }
}