<?php

namespace Bitrix\Mail;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\TextField;

Localization\Loc::loadMessages(__FILE__);

class MailMessageTable extends Entity\DataManager
{

    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'b_mail_message';
    }

    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
            ),
            'MAILBOX_ID' => array(
                'data_type' => 'integer',
                'required' => true,
            ),
            'DATE_INSERT' => array(
                'data_type' => 'datetime',
                'required' => true,
            ),
            'FULL_TEXT' => array(
                'data_type' => 'text',
            ),
            'MESSAGE_SIZE' => array(
                'data_type' => 'integer',
                'required' => true,
            ),
            'HEADER' => array(
                'data_type' => 'text',
            ),
            'FIELD_DATE' => array(
                'data_type' => 'datetime',
            ),
            'FIELD_FROM' => array(
                'data_type' => 'string',
            ),
            'FIELD_REPLY_TO' => array(
                'data_type' => 'string',
            ),
            'FIELD_TO' => array(
                'data_type' => 'string',
            ),
            'FIELD_CC' => array(
                'data_type' => 'string',
            ),
            'FIELD_BCC' => array(
                'data_type' => 'string',
            ),
            'FIELD_PRIORITY' => array(
                'data_type' => 'integer',
            ),
            'SUBJECT' => array(
                'data_type' => 'string',
            ),
            'BODY' => array(
                'data_type' => 'text',
            ),
            'BODY_HTML' => array(
                'data_type' => 'text',
            ),
            'ATTACHMENTS' => array(
                'data_type' => 'integer',
            ),
            'NEW_MESSAGE' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
            ),
            'SPAM' => array(
                'data_type' => 'enum',
                'values' => array('N', 'Y', '?'),
            ),
            'SPAM_RATING' => array(
                'data_type' => 'float',
            ),
            'SPAM_WORDS' => array(
                'data_type' => 'string',
            ),
            'SPAM_LAST_RESULT' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
            ),
            'EXTERNAL_ID' => array(
                'data_type' => 'string',
            ),
            'MSG_ID' => array(
                'data_type' => 'string',
            ),
            'IN_REPLY_TO' => array(
                'data_type' => 'string',
            ),
            'LEFT_MARGIN' => array(
                'data_type' => 'integer',
            ),
            'RIGHT_MARGIN' => array(
                'data_type' => 'integer',
            ),
            'SEARCH_CONTENT' => array(
                'data_type' => 'string',
            ),
            'INDEX_VERSION' => array(
                'data_type' => 'integer',
            ),
            new DatetimeField('READ_CONFIRMED'),
            new TextField('OPTIONS', [
                'serialized' => true,
            ]),
            'MAILBOX' => array(
                'data_type' => 'Bitrix\Mail\Mailbox',
                'reference' => array('=this.MAILBOX_ID' => 'ref.ID'),
            ),
        );
    }

}
