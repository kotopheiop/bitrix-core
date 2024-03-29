<?php

namespace Bitrix\Catalog;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class StoreProductTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PRODUCT_ID int mandatory
 * <li> AMOUNT double mandatory
 * <li> STORE_ID int mandatory
 * <li> STORE reference to {@link \Bitrix\Catalog\StoreTable}
 * <li> PRODUCT reference to {@link \Bitrix\Catalog\ProductTable}
 * </ul>
 *
 * @package Bitrix\Catalog
 *
 * DO NOT WRITE ANYTHING BELOW THIS
 *
 * <<< ORMENTITYANNOTATION
 * @method static EO_StoreProduct_Query query()
 * @method static EO_StoreProduct_Result getByPrimary($primary, array $parameters = array())
 * @method static EO_StoreProduct_Result getById($id)
 * @method static EO_StoreProduct_Result getList(array $parameters = array())
 * @method static EO_StoreProduct_Entity getEntity()
 * @method static \Bitrix\Catalog\EO_StoreProduct createObject($setDefaultValues = true)
 * @method static \Bitrix\Catalog\EO_StoreProduct_Collection createCollection()
 * @method static \Bitrix\Catalog\EO_StoreProduct wakeUpObject($row)
 * @method static \Bitrix\Catalog\EO_StoreProduct_Collection wakeUpCollection($rows)
 */
class StoreProductTable extends Main\Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_catalog_store_product';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return array(
            'ID' => new Main\Entity\IntegerField(
                'ID', array(
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('STORE_PRODUCT_ENTITY_ID_FIELD')
            )
            ),
            'STORE_ID' => new Main\Entity\IntegerField(
                'STORE_ID', array(
                'required' => true,
                'title' => Loc::getMessage('STORE_PRODUCT_ENTITY_STORE_ID_FIELD')
            )
            ),
            'PRODUCT_ID' => new Main\Entity\IntegerField(
                'PRODUCT_ID', array(
                'required' => true,
                'title' => Loc::getMessage('STORE_PRODUCT_ENTITY_PRODUCT_ID_FIELD')
            )
            ),
            'AMOUNT' => new Main\Entity\FloatField(
                'AMOUNT', array(
                'title' => Loc::getMessage('STORE_PRODUCT_ENTITY_AMOUNT_FIELD')
            )
            ),
            'STORE' => new Main\Entity\ReferenceField(
                'STORE',
                '\Bitrix\Catalog\Store',
                array('=this.STORE_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
            'PRODUCT' => new Main\Entity\ReferenceField(
                'PRODUCT',
                '\Bitrix\Catalog\Product',
                array('=this.PRODUCT_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            )
        );
    }

    /**
     * Delete all rows for product.
     * @param int $id Product id.
     * @return void
     * @internal
     *
     */
    public static function deleteByProduct($id)
    {
        $id = (int)$id;
        if ($id <= 0) {
            return;
        }

        $conn = Main\Application::getConnection();
        $helper = $conn->getSqlHelper();
        $conn->queryExecute(
            'delete from ' . $helper->quote(self::getTableName()) . ' where ' . $helper->quote(
                'PRODUCT_ID'
            ) . ' = ' . $id
        );
        unset($helper, $conn);
    }
}