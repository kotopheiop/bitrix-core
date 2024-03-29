<?php

namespace Bitrix\Main\UserField\Types;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\HtmlFilter;
use CDBResult;
use CUserFieldEnum;
use CUserTypeManager;
use Bitrix\Main\Context;

Loc::loadMessages(__FILE__);

/**
 * Class EnumType
 * @package Bitrix\Main\UserField\Types
 */
class EnumType extends BaseType
{
    public const
        USER_TYPE_ID = 'enumeration',
        RENDER_COMPONENT = 'bitrix:main.field.enum';

    public const
        DISPLAY_LIST = 'LIST',
        DISPLAY_CHECKBOX = 'CHECKBOX',
        DISPLAY_UI = 'UI';

    /**
     * @return array
     */
    public static function getDescription(): array
    {
        return [
            'DESCRIPTION' => Loc::getMessage('USER_TYPE_ENUM_DESCRIPTION'),
            'BASE_TYPE' => CUserTypeManager::BASE_TYPE_ENUM,
        ];
    }

    public static function renderField(array $userField, ?array $additionalParameters = []): string
    {
        static::getEnumList($userField, $additionalParameters);
        return parent::renderField($userField, $additionalParameters);
    }

    public static function renderView(array $userField, ?array $additionalParameters = []): string
    {
        static::getEnumList($userField, $additionalParameters);
        return parent::renderView($userField, $additionalParameters);
    }

    public static function renderEdit(array $userField, ?array $additionalParameters = []): string
    {
        static::getEnumList($userField, $additionalParameters);
        return parent::renderEdit($userField, $additionalParameters);
    }

    public static function renderEditForm(array $userField, ?array $additionalParameters): string
    {
        $enum = call_user_func([$userField['USER_TYPE']['CLASS_NAME'], 'getlist'], $userField);
        if (!$enum) {
            return '';
        }
        $items = [];
        while ($item = $enum->GetNext()) {
            $items[$item['ID']] = $item;
        }
        $additionalParameters['items'] = $items;

        return parent::renderEditForm($userField, $additionalParameters);
    }

    public static function renderFilter(array $userField, ?array $additionalParameters): string
    {
        $enum = call_user_func([$userField['USER_TYPE']['CLASS_NAME'], 'getlist'], $userField);
        if (!$enum) {
            return '';
        }
        $items = [];
        while ($item = $enum->GetNext()) {
            $items[$item['ID']] = $item['VALUE'];
        }
        $additionalParameters['items'] = $items;
        return parent::renderFilter($userField, $additionalParameters);
    }

    public static function renderAdminListView(array $userField, ?array $additionalParameters): string
    {
        static $cache = [];
        $empty_caption = '&nbsp;';

        if (!array_key_exists($additionalParameters['VALUE'], $cache)) {
            $enum = call_user_func([$userField['USER_TYPE']['CLASS_NAME'], 'getlist'], $userField);
            if (!$enum) {
                $additionalParameters['VALUE'] = $empty_caption;
                return parent::renderAdminListView($userField, $additionalParameters);
            }
            while ($item = $enum->GetNext()) {
                $cache[$item['ID']] = $item['VALUE'];
            }
        }
        if (!array_key_exists($additionalParameters['VALUE'], $cache)) {
            $cache[$additionalParameters['VALUE']] = $empty_caption;
        }

        $additionalParameters['VALUE'] = $cache[$additionalParameters['VALUE']];
        return parent::renderAdminListView($userField, $additionalParameters);
    }

    public static function renderAdminListEdit(array $userField, ?array $additionalParameters): string
    {
        $enum = call_user_func([$userField['USER_TYPE']['CLASS_NAME'], 'getlist'], $userField);
        $values = [];
        if (!$enum) {
            $values = [];
        } else {
            while ($item = $enum->GetNext()) {
                $values[$item['ID']] = $item['VALUE'];
            }
        }
        $additionalParameters['enumItems'] = $values;

        return parent::renderAdminListEdit($userField, $additionalParameters);
    }

    /**
     * @return string
     */
    public static function getDbColumnType(): string
    {
        return 'int(18)';
    }

    /**
     * @param array $userField
     * @return array
     */
    public static function prepareSettings(array $userField): array
    {
        $height = (int)$userField['SETTINGS']['LIST_HEIGHT'];
        $disp = $userField['SETTINGS']['DISPLAY'];
        $caption_no_value = trim($userField['SETTINGS']['CAPTION_NO_VALUE']);
        $show_no_value = ($userField['SETTINGS']['SHOW_NO_VALUE'] === 'N' ? 'N' : 'Y');

        if ($disp !== self::DISPLAY_CHECKBOX && $disp !== self::DISPLAY_UI) {
            $disp = self::DISPLAY_LIST;
        }

        return [
            'DISPLAY' => $disp,
            'LIST_HEIGHT' => ($height < 1 ? 1 : $height),
            'CAPTION_NO_VALUE' => $caption_no_value, // no default value - only in output
            'SHOW_NO_VALUE' => $show_no_value, // no default value - only in output
        ];
    }

    /**
     * @param array $userField
     * @param string|array $value
     * @return array
     */
    public static function checkFields(array $userField, $value): array
    {
        return [];
    }

    /**
     * @param array $userField
     * @return string|null
     */
    public static function onSearchIndex(array $userField): ?string
    {
        $res = '';

        if (is_array($userField['VALUE'])) {
            $val = $userField['VALUE'];
        } else {
            $val = [$userField['VALUE']];
        }

        $val = array_filter($val, 'strlen');
        if (count($val)) {
            $ob = new CUserFieldEnum();
            $rs = $ob->GetList(
                [],
                [
                    'USER_FIELD_ID' => $userField['ID'],
                    'ID' => $val,
                ]
            );

            while ($ar = $rs->Fetch()) {
                $res .= $ar['VALUE'] . '\r\n';
            }
        }

        return $res;
    }

    /**
     * @param array $userField
     * @param array $additionalParameters
     * @return array
     */
    public static function getFilterData(array $userField, array $additionalParameters): array
    {
        $enum = call_user_func([$userField['USER_TYPE']['CLASS_NAME'], 'getlist'], $userField);
        $items = [];
        if ($enum) {
            while ($item = $enum->GetNext()) {
                $items[$item['ID']] = $item['VALUE'];
            }
        }
        return [
            'id' => $additionalParameters['ID'],
            'name' => $additionalParameters['NAME'],
            'type' => 'list',
            'items' => $items,
            'params' => ['multiple' => 'Y'],
            'filterable' => ''
        ];
    }

    /**
     * @param array $userField
     * @return bool|CDBResult
     */
    public static function getList(array $userField)
    {
        $userFieldEnum = new CUserFieldEnum();
        return $userFieldEnum->GetList([], ['USER_FIELD_ID' => $userField['ID']]);
    }

    /**
     * @param array $userField
     * @param array $additionalParameters
     */
    public static function getEnumList(array &$userField, array $additionalParameters = []): void
    {
        $showNoValue = (
            $userField['MANDATORY'] !== 'Y'
            ||
            $userField['SETTINGS']['SHOW_NO_VALUE'] !== 'N'
            ||
            (
                isset($additionalParameters['SHOW_NO_VALUE'])
                &&
                $additionalParameters['SHOW_NO_VALUE'] === true
            )
        );

        if (
            $showNoValue
            &&
            (
                $userField['SETTINGS']['DISPLAY'] !== 'CHECKBOX'
                ||
                $userField['MULTIPLE'] !== 'Y'
            )
        ) {
            $enum = [null => static::getEmptyCaption($userField)];
            $userField['USER_TYPE']['FIELDS'] = $enum;
            $userField['USER_TYPE']['~FIELDS'] = $enum;
        }

        $userFieldEnum = new CUserFieldEnum;
        $enumList = $userFieldEnum->GetList([], ['USER_FIELD_ID' => $userField['ID']]);

        while ($item = $enumList->Fetch()) {
            $userField['USER_TYPE']['FIELDS'][$item['ID']] = HtmlFilter::encode($item['VALUE']);
            $userField['USER_TYPE']['~FIELDS'][$item['ID']] = $item['VALUE'];
        }
    }

    /**
     * @array $userField
     * @param $userField
     * @return string
     */
    public static function getEmptyCaption(array $userField): string
    {
        return (
        $userField['SETTINGS']['CAPTION_NO_VALUE'] != '' ?
            HtmlFilter::encode($userField['SETTINGS']['CAPTION_NO_VALUE']) :
            Loc::getMessage('USER_TYPE_ENUM_NO_VALUE')
        );
    }

    /**
     * Returns values from multiple enumerations by their ID.
     * @param array[] $userFields It has to have the "ID" keys in subarrays.
     * @return bool|CDBResult
     */
    public static function getListMultiple(array $userFields)
    {
        $ids = [];
        foreach ($userFields as $field) {
            $ids[] = $field['ID'];
        }
        $userFieldEnum = new CUserFieldEnum();
        return $userFieldEnum->GetList(
            ['USER_FIELD_ID' => 'ASC', 'SORT' => 'ASC', 'ID' => 'ASC'],
            ['USER_FIELD_ID' => $ids]
        );
    }

    /**
     * @param array $userField
     * @param array|null $additionalParameters
     * @return array
     */
    public static function getGroupActionData(array $userField, ?array $additionalParameters): array
    {
        $result = [];
        $enum = call_user_func([$userField['USER_TYPE']['CLASS_NAME'], 'getlist'], $userField);
        if (!$enum) {
            return $result;
        }

        while ($item = $enum->GetNext()) {
            $result[] = ['NAME' => $item['VALUE'], 'VALUE' => $item['ID']];
        }

        return $result;
    }

    /**
     * @param array $userField
     * @param array|null $additionalParameters
     * @return string
     */
    public static function getAdminListEditHtmlMulty(array $userField, ?array $additionalParameters): string
    {
        return static::renderAdminListEdit($userField, $additionalParameters);
    }

    public static function getDefaultValue(array $userField, array $additionalParameters = [])
    {
        static::getEnumList($userField, $additionalParameters);

        if (!isset($userField['ENUM'])) {
            $userField['ENUM'] = [];
            $enumValuesManager = new \CUserFieldEnum();
            $dbRes = $enumValuesManager->getList(
                [],
                ['USER_FIELD_ID' => $userField['ID']]
            );

            while ($enumValue = $dbRes->fetch()) {
                $userField['ENUM'][] = [
                    'ID' => $enumValue['ID'],
                    'VALUE' => $enumValue['VALUE'],
                    'DEF' => $enumValue['DEF'],
                    'SORT' => $enumValue['SORT'],
                    'XML_ID' => $enumValue['XML_ID'],
                ];
            }
        }

        $userField['ENTITY_VALUE_ID'] = 0;
        return static::getFieldValue($userField, $additionalParameters);
    }

    public function onBeforeSave($userField, $value)
    {
        return ($userField['MULTIPLE'] !== 'Y' && is_array($value)) ? array_shift($value) : $value;
    }

    public static function getFieldValue(array $userField, array $additionalParameters = [])
    {
        if (
            !$additionalParameters['bVarsFromForm']
            && !isset($additionalParameters['VALUE'])
        ) {
            if (
                isset($userField['ENTITY_VALUE_ID'], $userField['ENUM'])
                && $userField['ENTITY_VALUE_ID'] <= 0
            ) {
                $value = ($userField['MULTIPLE'] === 'Y' ? [] : null);
                foreach ($userField['ENUM'] as $enum) {
                    if ($enum['DEF'] === 'Y') {
                        if ($userField['MULTIPLE'] === 'Y') {
                            $value[] = $enum['ID'];
                        } else {
                            $value = $enum['ID'];
                            break;
                        }
                    }
                }
            } else {
                $value = $userField['VALUE'];
            }
        } elseif (isset($additionalParameters['VALUE'])) {
            $value = $additionalParameters['VALUE'];
        } else {
            $value = Context::getCurrent()->getRequest()->get($userField['FIELD_NAME']);
        }

        return $value;
    }

}