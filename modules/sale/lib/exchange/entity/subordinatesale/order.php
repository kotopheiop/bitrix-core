<?php

namespace Bitrix\Sale\Exchange\Entity\SubordinateSale;

class Order extends \Bitrix\Sale\Exchange\Entity\OrderImport
{
    /**
     * @param \Bitrix\Sale\BasketBase $basket
     * @param array $item
     * @return \Bitrix\Sale\BasketItem|bool
     *
     * ��� ����� ����������� ����������, ��� �������� ������ ������ �� 1� �������� � ������� �� �����������.
     * �������� ���������� ������ �� ��������������
     */
    static public function getBasketItemByItem(\Bitrix\Sale\BasketBase $basket, array $item)
    {
        foreach ($basket as $basketItem) {
            /** @var  \Bitrix\Sale\BasketItem $basketItem */
            if ($item['ID'] == $basketItem->getField('PRODUCT_XML_ID')) {
                return $basketItem;
            } else
                continue;
        }
        return false;
    }

    /**
     * @param $typeId
     * @return \Bitrix\Sale\Exchange\ImportBase
     * @internal
     */
    protected function entityCreateByFactory($typeId)
    {
        return \Bitrix\Sale\Exchange\Entity\SubordinateSale\EntityImportFactory::create($typeId);
    }
}