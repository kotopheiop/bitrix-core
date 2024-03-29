<?php

namespace Bitrix\Rest\Configuration;

use Bitrix\Main\Type\DateTime;

/**
 * Temp work with current context for step by step action
 */
class Setting
{
    public const SETTING_MANIFEST = 'SETTING_MANIFEST';
    public const MANIFEST_CODE = 'MANIFEST_CODE';
    public const SETTING_RATIO = 'SETTING_RATIO';
    public const SETTING_APP_INFO = 'APP_INFO';
    public const SETTING_EXPORT_ARCHIVE_NAME = 'EXPORT_ARCHIVE_NAME';
    public const SETTING_ACTION_INFO = 'ACTION_INFO';
    public const SETTING_NOTICE_COLLECTION = 'NOTICE_COLLECTION';

    private $context = 'null';
    private $ttlContext = 14400;//3600*4
    private $multipleCode = [];

    /**
     * @param $context string [a-zA-Z0-9_]
     */
    public function __construct($context)
    {
        if ($context != '') {
            $context = preg_replace('/[^a-zA-Z0-9_]/', '', $context);
            $this->context = $context;
        }
    }

    public function addMultipleCode($code): bool
    {
        $this->multipleCode[] = $code;
        return true;
    }

    public function getMultipleCode()
    {
        return $this->multipleCode;
    }

    /**
     * Add data in context
     *
     * @param $code string needed code setting
     * @param $data mixed any saved data
     *
     * @return boolean
     */
    public function set($code, $data)
    {
        $id = 0;
        if (!in_array($code, $this->multipleCode, true)) {
            $res = StorageTable::getList(
                [
                    'filter' => [
                        '=CONTEXT' => $this->context,
                        '=CODE' => $code,
                    ],
                ]
            );
            if ($item = $res->fetch()) {
                StorageTable::deleteFile($item);
                $id = $item['ID'];
            }
        }

        $save = [
            'CREATE_TIME' => new DateTime(),
            'CONTEXT' => $this->context,
            'CODE' => $code,
            'DATA' => $data
        ];
        if ($id > 0) {
            $result = StorageTable::update($id, $save);
        } else {
            $result = StorageTable::add($save);
        }

        return $result->isSuccess();
    }

    /**
     * Return needed setting by code with context
     * @param $code string
     *
     * @return mixed|null
     */
    public function get($code)
    {
        $result = null;
        $now = new DateTime();
        $isMultiple = in_array($code, $this->multipleCode, true);

        $res = StorageTable::getList(
            [
                'filter' => [
                    '=CONTEXT' => $this->context,
                    '=CODE' => $code,
                ],
            ]
        );
        while ($item = $res->fetch()) {
            $item['CREATE_TIME']->add($this->ttlContext . 'second');
            if ($item['CREATE_TIME'] > $now) {
                if (!$isMultiple) {
                    $result = $item['DATA'];
                    break;
                }

                $result[$item['ID']] = $item['DATA'];
            } else {
                StorageTable::deleteFile($item);
                StorageTable::delete($item['ID']);
            }
        }

        return $result;
    }

    /**
     * @param $code string
     *
     * @return boolean
     */
    public function delete($code)
    {
        StorageTable::deleteByFilter(
            [
                '=CONTEXT' => $this->context,
                '=CODE' => $code,
            ]
        );

        return true;
    }

    /**
     * @return boolean
     */
    public function deleteFull()
    {
        StorageTable::deleteByFilter(
            [
                '=CONTEXT' => $this->context,
            ]
        );

        return true;
    }
}