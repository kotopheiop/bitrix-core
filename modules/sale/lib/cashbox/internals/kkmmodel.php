<?php

namespace Bitrix\Sale\Cashbox\Internals;

use Bitrix\Main\Application;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Sale\Cashbox\Cashbox1C;

class KkmModelTable extends DataManager
{
    public static function getTableName()
    {
        return 'b_sale_kkm_model';
    }

    public static function getMap()
    {
        return array(
            'ID' => array(
                'primary' => true,
                'data_type' => 'integer',
            ),
            'NAME' => array(
                'data_type' => 'string',
                'required' => true,
            ),
            'SETTINGS' => array(
                'data_type' => 'string',
                'serialized' => true
            ),
        );
    }

    public static function delete($primary)
    {
        if ($primary == Cashbox1C::getId()) {
            $cacheManager = Application::getInstance()->getManagedCache();
            $cacheManager->clean(Cashbox1C::CACHE_ID);
        }

        return parent::delete($primary);
    }
}
