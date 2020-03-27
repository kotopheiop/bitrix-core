<?php

namespace Bitrix\Sale\Internals;

use Bitrix\Main;
use Bitrix\Sale;

class Events
{
    /**
     * @param Main\Event $event
     *
     * @return Main\EventResult
     * @internal
     */
    public static function onSaleBasketItemEntitySaved(Main\Event $event)
    {
        return Sale\BasketComponentHelper::onSaleBasketItemEntitySaved($event);
    }

    /**
     * @param Main\Event $event
     *
     * @return Main\EventResult
     * @internal
     */
    public static function onSaleBasketItemDeleted(Main\Event $event)
    {
        return Sale\BasketComponentHelper::onSaleBasketItemDeleted($event);
    }
}