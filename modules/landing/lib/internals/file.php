<?php

namespace Bitrix\Landing\Internals;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class FileTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'b_landing_file';
    }

    /**
     * Returns entity map definition.
     * @return array
     */
    public static function getMap()
    {
        return array(
            'ID' => new Entity\IntegerField(
                'ID', array(
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID'
            )
            ),
            'ENTITY_ID' => new Entity\IntegerField(
                'ENTITY_ID', array(
                'title' => Loc::getMessage('LANDING_TABLE_FIELD_ENTITY_ID'),
                'required' => true
            )
            ),
            'ENTITY_TYPE' => new Entity\StringField(
                'ENTITY_TYPE', array(
                'title' => Loc::getMessage('LANDING_TABLE_FIELD_ENTITY_TYPE'),
                'required' => true
            )
            ),
            'FILE_ID' => new Entity\IntegerField(
                'FILE_ID', array(
                'title' => Loc::getMessage('LANDING_TABLE_FIELD_FILE_ID'),
                'required' => true
            )
            )
        );
    }
}