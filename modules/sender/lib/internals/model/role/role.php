<?php

namespace Bitrix\Sender\Internals\Model\Role;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

/**
 * Class RoleTable
 *
 * @package Bitrix\Sender\Internals\Model\Role
 */
class RoleTable extends Entity\DataManager
{
    /**
     * Get table name.
     *
     * @return string
     * @inheritdoc
     */
    public static function getTableName()
    {
        return 'b_sender_role';
    }

    /**
     * Get map.
     *
     * @return array
     * @inheritdoc
     */
    public static function getMap()
    {
        return array(
            'ID' => new Entity\IntegerField(
                'ID', array(
                'primary' => true,
                'autocomplete' => true,
            )
            ),
            'NAME' => new Entity\StringField(
                'NAME', array(
                'required' => true,
                'title' => Loc::getMessage('SENDER_INTERNALS_MODEL_ROLE_FIELD_NAME')
            )
            ),
            'XML_ID' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateXmlId'),
            ),
        );
    }

    /**
     * Returns validators for XML_ID field.
     *
     * @return array
     */
    public static function validateXmlId()
    {
        return [
            new Entity\Validator\Length(null, 255),
        ];
    }
}