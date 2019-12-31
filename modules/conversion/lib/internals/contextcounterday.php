<?php

namespace Bitrix\Conversion\Internals;

use Bitrix\Main\Entity;

/** @internal */
class ContextCounterDayTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'b_conv_context_counter_day';
    }

    public static function getMap()
    {
        return array(
            new Entity\DateField   ('DAY', array('primary' => true)),
            new Entity\IntegerField('CONTEXT_ID', array('primary' => true)),
            new Entity\StringField ('NAME', array('primary' => true, 'size' => 30)),
            new Entity\FloatField  ('VALUE', array('required' => true)),

            new Entity\ReferenceField('CONTEXT', 'ContextTable',
                array('=this.CONTEXT_ID' => 'ref.ID'),
                array('join_type' => 'INNER')
            ),
        );
    }

    public static function getFilePath()
    {
        return __FILE__;
    }
}
