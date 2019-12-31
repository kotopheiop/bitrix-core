<?
/**
 * usertypestr.php, ��� ��� ���������������� ������� - ������
 *
 * �������� ����� ����������� ���������� ��� ���� "������".
 * @author Bitrix <support@bitrixsoft.com>
 * @version 1.0
 * @package usertype
 */

IncludeModuleLangFile(__FILE__);

/**
 * ������ ����� ������������ ��� ���������� ����������� ��������
 * ����������������� �������.
 *
 * <p>��������� ������ ����� ������ ������������� �� "2".
 * ��� ��������� ��� ������������ � ������� ����������� �� �������� ���������.</p>
 * @package usertype
 * @subpackage classes
 */
class CUserTypeString extends \Bitrix\Main\UserField\TypeBase
{
    const USER_TYPE_ID = 'string';

    /**
     * ���������� ������� OnUserTypeBuildList.
     *
     * <p>��� ������� �������������� � �������� ����������� ������� OnUserTypeBuildList.
     * ���������� ������ ����������� ��� ���������������� �������.</p>
     * <p>�������� �������:</p>
     * <ul>
     * <li>USER_TYPE_ID - ���������� �������������
     * <li>CLASS_NAME - ��� ������ ������ �������� ��������� ��������� ����
     * <li>DESCRIPTION - �������� ��� ������ � ���������� (���������� ������ � �.�.)
     * <li>BASE_TYPE - ������� ��� �� ������� ����� �������� �������� ������� (int, double, string, date, datetime)
     * </ul>
     * @return array
     * @static
     */
    function GetUserTypeDescription()
    {
        return array(
            "USER_TYPE_ID" => static::USER_TYPE_ID,
            "CLASS_NAME" => __CLASS__,
            "DESCRIPTION" => GetMessage("USER_TYPE_STRING_DESCRIPTION"),
            "BASE_TYPE" => \CUserTypeManager::BASE_TYPE_STRING,
            "EDIT_CALLBACK" => array(__CLASS__, 'GetPublicEdit'),
            "VIEW_CALLBACK" => array(__CLASS__, 'GetPublicView'),
            //����� ������ ��������� ��� ����������� �������� �������� � ��������� �����.
            //"VIEW_COMPONENT_NAME" => "my:system.field.view",
            //"VIEW_COMPONENT_TEMPLATE" => "string",
            //� ��� ��������������
            //"EDIT_COMPONENT_NAME" => "my:system.field.view",
            //"EDIT_COMPONENT_TEMPLATE" => "string",
            // ����� ����� ������ callback ��� ����������� ��������
            // "VIEW_CALLBACK" => callable
            // � ��� ��������������
            // "EDIT_CALLBACK" => callable
        );
    }

    /**
     * ��� ������� ���������� ��� ���������� ������ ��������.
     *
     * <p>��� ������� ���������� ��� ��������������� SQL �������
     * �������� ������� ��� �������� �� ������������� �������� ��������.</p>
     * <p>�������� ������������� ������� �������� �� � �������, � ��������� (��� � ����������)
     * � ��� ������ ���� � �� ������ text.</p>
     * @param array $arUserField ������ ����������� ����
     * @return string
     * @static
     */
    function GetDBColumnType($arUserField)
    {
        global $DB;
        switch (strtolower($DB->type)) {
            case "mysql":
                return "text";
            case "oracle":
                return "varchar2(2000 char)";
            case "mssql":
                return "varchar(2000)";
        }
    }

    /**
     * ��� ������� ���������� ����� ����������� ���������� �������� � ��.
     *
     * <p>��� ������ "��������" ������ � ����������� ���������� ���� ��������.
     * ��� ����, ����� ��������/��������� ����� �� ������� ���� ������ �����.</p>
     * @param array $arUserField ������ ����������� ����. <b>��������!</b> ��� �������� ���� ��� �� ��������� � ��!
     * @return array ������ ������� � ���������� ����� ������������ � �������� � ��.
     * @static
     */
    function PrepareSettings($arUserField)
    {
        $size = intval($arUserField["SETTINGS"]["SIZE"]);
        $rows = intval($arUserField["SETTINGS"]["ROWS"]);
        $min = intval($arUserField["SETTINGS"]["MIN_LENGTH"]);
        $max = intval($arUserField["SETTINGS"]["MAX_LENGTH"]);

        return array(
            "SIZE" => ($size <= 1 ? 20 : ($size > 255 ? 225 : $size)),
            "ROWS" => ($rows <= 1 ? 1 : ($rows > 50 ? 50 : $rows)),
            "REGEXP" => $arUserField["SETTINGS"]["REGEXP"],
            "MIN_LENGTH" => $min,
            "MAX_LENGTH" => $max,
            "DEFAULT_VALUE" => $arUserField["SETTINGS"]["DEFAULT_VALUE"],
        );
    }

    /**
     * ��� ������� ���������� ��� ������ ����� ��������� ��������.
     *
     * <p>���������� html ��� ����������� � 2-� ���������� �������.
     * � ����� usertype_edit.php</p>
     * <p>�.�. tr td bla-bla /td td edit-edit-edit /td /tr </p>
     * @param array $arUserField ������ ����������� ����. ��� ������ (��� �� ������������ ���� - <b>false</b>)
     * @param array $arHtmlControl ������ ���������� �� �����. ���� �������� ������ ���� ������� NAME (html ����������)
     * @return string HTML ��� ������.
     * @static
     */
    function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
    {
        $result = '';
        if ($bVarsFromForm)
            $value = htmlspecialcharsbx($GLOBALS[$arHtmlControl["NAME"]]["DEFAULT_VALUE"]);
        elseif (is_array($arUserField))
            $value = htmlspecialcharsbx($arUserField["SETTINGS"]["DEFAULT_VALUE"]);
        else
            $value = "";
        $result .= '
		<tr>
			<td>' . GetMessage("USER_TYPE_STRING_DEFAULT_VALUE") . ':</td>
			<td>
				<input type="text" name="' . $arHtmlControl["NAME"] . '[DEFAULT_VALUE]" size="20"  maxlength="225" value="' . $value . '">
			</td>
		</tr>
		';
        if ($bVarsFromForm)
            $value = intval($GLOBALS[$arHtmlControl["NAME"]]["SIZE"]);
        elseif (is_array($arUserField))
            $value = intval($arUserField["SETTINGS"]["SIZE"]);
        else
            $value = 20;
        $result .= '
		<tr>
			<td>' . GetMessage("USER_TYPE_STRING_SIZE") . ':</td>
			<td>
				<input type="text" name="' . $arHtmlControl["NAME"] . '[SIZE]" size="20"  maxlength="20" value="' . $value . '">
			</td>
		</tr>
		';
        if ($bVarsFromForm)
            $value = intval($GLOBALS[$arHtmlControl["NAME"]]["ROWS"]);
        elseif (is_array($arUserField))
            $value = intval($arUserField["SETTINGS"]["ROWS"]);
        else
            $value = 1;
        if ($value < 1) $value = 1;
        $result .= '
		<tr>
			<td>' . GetMessage("USER_TYPE_STRING_ROWS") . ':</td>
			<td>
				<input type="text" name="' . $arHtmlControl["NAME"] . '[ROWS]" size="20"  maxlength="20" value="' . $value . '">
			</td>
		</tr>
		';
        if ($bVarsFromForm)
            $value = intval($GLOBALS[$arHtmlControl["NAME"]]["MIN_LENGTH"]);
        elseif (is_array($arUserField))
            $value = intval($arUserField["SETTINGS"]["MIN_LENGTH"]);
        else
            $value = 0;
        $result .= '
		<tr>
			<td>' . GetMessage("USER_TYPE_STRING_MIN_LEGTH") . ':</td>
			<td>
				<input type="text" name="' . $arHtmlControl["NAME"] . '[MIN_LENGTH]" size="20"  maxlength="20" value="' . $value . '">
			</td>
		</tr>
		';
        if ($bVarsFromForm)
            $value = intval($GLOBALS[$arHtmlControl["NAME"]]["MAX_LENGTH"]);
        elseif (is_array($arUserField))
            $value = intval($arUserField["SETTINGS"]["MAX_LENGTH"]);
        else
            $value = 0;
        $result .= '
		<tr>
			<td>' . GetMessage("USER_TYPE_STRING_MAX_LENGTH") . ':</td>
			<td>
				<input type="text" name="' . $arHtmlControl["NAME"] . '[MAX_LENGTH]" size="20"  maxlength="20" value="' . $value . '">
			</td>
		</tr>
		';
        if ($bVarsFromForm)
            $value = htmlspecialcharsbx($GLOBALS[$arHtmlControl["NAME"]]["REGEXP"]);
        elseif (is_array($arUserField))
            $value = htmlspecialcharsbx($arUserField["SETTINGS"]["REGEXP"]);
        else
            $value = "";
        $result .= '
		<tr>
			<td>' . GetMessage("USER_TYPE_STRING_REGEXP") . ':</td>
			<td>
				<input type="text" name="' . $arHtmlControl["NAME"] . '[REGEXP]" size="20"  maxlength="200" value="' . $value . '">
			</td>
		</tr>
		';
        return $result;
    }

    /**
     * ��� ������� ���������� ��� ������ ����� �������������� �������� ��������.
     *
     * <p>���������� html ��� ����������� � ������ �������.
     * � ����� �������������� �������� (�� ������� "���. ��������")</p>
     * <p>�������� $arHtmlControl ��������� � html ����������� ����.</p>
     * @param array $arUserField ������ ����������� ����.
     * @param array $arHtmlControl ������ ���������� �� �����. �������� �������� NAME � VALUE.
     * @return string HTML ��� ������.
     * @static
     */
    function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        if ($arUserField["ENTITY_VALUE_ID"] < 1 && strlen($arUserField["SETTINGS"]["DEFAULT_VALUE"]) > 0)
            $arHtmlControl["VALUE"] = htmlspecialcharsbx($arUserField["SETTINGS"]["DEFAULT_VALUE"]);
        if ($arUserField["SETTINGS"]["ROWS"] < 2) {
            $arHtmlControl["VALIGN"] = "middle";
            return '<input type="text" ' .
                'name="' . $arHtmlControl["NAME"] . '" ' .
                'size="' . $arUserField["SETTINGS"]["SIZE"] . '" ' .
                ($arUserField["SETTINGS"]["MAX_LENGTH"] > 0 ? 'maxlength="' . $arUserField["SETTINGS"]["MAX_LENGTH"] . '" ' : '') .
                'value="' . $arHtmlControl["VALUE"] . '" ' .
                ($arUserField["EDIT_IN_LIST"] != "Y" ? 'disabled="disabled" ' : '') .
                '>';
        } else {
            return '<textarea ' .
                'name="' . $arHtmlControl["NAME"] . '" ' .
                'cols="' . $arUserField["SETTINGS"]["SIZE"] . '" ' .
                'rows="' . $arUserField["SETTINGS"]["ROWS"] . '" ' .
                ($arUserField["SETTINGS"]["MAX_LENGTH"] > 0 ? 'maxlength="' . $arUserField["SETTINGS"]["MAX_LENGTH"] . '" ' : '') .
                ($arUserField["EDIT_IN_LIST"] != "Y" ? 'disabled="disabled" ' : '') .
                '>' . $arHtmlControl["VALUE"] . '</textarea>';
        }
    }

    /**
     * ��� ������� ���������� ��� ������ ����� �������������� �������� <b>��������������</b> ��������.
     *
     * <p>���� ����� �� ������������� ����� �������,
     * �� �������� ����� "�������" ��������� html �� ������� GetEditFormHTML</p>
     * <p>���������� html ��� ����������� � ������ �������.
     * � ����� �������������� �������� (�� ������� "���. ��������")</p>
     * <p>�������� $arHtmlControl ��������� � html ����������� ����.</p>
     * <p>���� VALUE $arHtmlControl - ������.</p>
     * @param array $arUserField ������ ����������� ����.
     * @param array $arHtmlControl ������ ���������� �� �����. �������� �������� NAME � VALUE.
     * @return string HTML ��� ������.
     * @static
     */
    /*
        function GetEditFormHTMLMulty($arUserField, $arHtmlControl)
        {
            if($arUserField["VALUE"]===false && strlen($arUserField["SETTINGS"]["DEFAULT_VALUE"])>0)
                $arHtmlControl["VALUE"] = array(htmlspecialcharsbx($arUserField["SETTINGS"]["DEFAULT_VALUE"]));
            $result = array();
            foreach($arHtmlControl["VALUE"] as $value)
            {
                if($arUserField["SETTINGS"]["ROWS"] < 2)
                    $result[] = '<input type="text" '.
                        'name="'.$arHtmlControl["NAME"].'" '.
                        'size="'.$arUserField["SETTINGS"]["SIZE"].'" '.
                        ($arUserField["SETTINGS"]["MAX_LENGTH"]>0? 'maxlength="'.$arUserField["SETTINGS"]["MAX_LENGTH"].'" ': '').
                        'value="'.$value.'" '.
                        ($arUserField["EDIT_IN_LIST"]!="Y"? 'disabled="disabled" ': '').
                        '>';
                else
                    $result[] = '<textarea '.
                        'name="'.$arHtmlControl["NAME"].'" '.
                        'cols="'.$arUserField["SETTINGS"]["SIZE"].'" '.
                        'rows="'.$arUserField["SETTINGS"]["ROWS"].'" '.
                        ($arUserField["SETTINGS"]["MAX_LENGTH"]>0? 'maxlength="'.$arUserField["SETTINGS"]["MAX_LENGTH"].'" ': '').
                        ($arUserField["EDIT_IN_LIST"]!="Y"? 'disabled="disabled" ': '').
                        '>'.$value.'</textarea>';
            }
            return implode("<br>", $result);
        }
    */
    /**
     * ��� ������� ���������� ��� ������ ������� �� �������� ������.
     *
     * <p>���������� html ��� ����������� � ������ �������.</p>
     * <p>�������� $arHtmlControl ��������� � html ����������� ����.</p>
     * @param array $arUserField ������ ����������� ����.
     * @param array $arHtmlControl ������ ���������� �� �����. �������� �������� NAME � VALUE.
     * @return string HTML ��� ������.
     * @static
     */
    function GetFilterHTML($arUserField, $arHtmlControl)
    {
        return '<input type="text" ' .
            'name="' . $arHtmlControl["NAME"] . '" ' .
            'size="' . $arUserField["SETTINGS"]["SIZE"] . '" ' .
            'value="' . $arHtmlControl["VALUE"] . '"' .
            '>';
    }

    function GetFilterData($arUserField, $arHtmlControl)
    {
        return array(
            "id" => $arHtmlControl["ID"],
            "name" => $arHtmlControl["NAME"],
            "filterable" => ""
        );
    }

    /**
     * ��� ������� ���������� ��� ������ �������� �������� � ������ ���������.
     *
     * <p>���������� html ��� ����������� � ������ �������.</p>
     * <p>�������� $arHtmlControl ��������� � html ����������� ����.</p>
     * @param array $arUserField ������ ����������� ����.
     * @param array $arHtmlControl ������ ���������� �� �����. �������� �������� NAME � VALUE.
     * @return string HTML ��� ������.
     * @static
     */
    function GetAdminListViewHTML($arUserField, $arHtmlControl)
    {
        if (strlen($arHtmlControl["VALUE"]) > 0)
            return $arHtmlControl["VALUE"];
        else
            return '&nbsp;';
    }

    /**
     * ��� ������� ���������� ��� ������ �������� <b>��������������</b> �������� � ������ ���������.
     *
     * <p>���������� html ��� ����������� � ������ �������.</p>
     * <p>���� ����� �� ������������� ����� �������,
     * �� �������� ����� "�������" ��������� html �� ������� GetAdminListViewHTML</p>
     * <p>�������� $arHtmlControl ��������� � html ����������� ����.</p>
     * <p>���� VALUE $arHtmlControl - ������.</p>
     * @param array $arUserField ������ ����������� ����.
     * @param array $arHtmlControl ������ ���������� �� �����. �������� �������� NAME � VALUE.
     * @return string HTML ��� ������.
     * @static
     */
    /*
        function GetAdminListViewHTMLMulty($arUserField, $arHtmlControl)
        {
            return implode(", ", $arHtmlControl["VALUE"]);
        }
    */
    /**
     * ��� ������� ���������� ��� ������ �������� �������� � ������ ��������� � ������ <b>��������������</b>.
     *
     * <p>���������� html ��� ����������� � ������ �������.</p>
     * <p>�������� $arHtmlControl ��������� � html ����������� ����.</p>
     * @param array $arUserField ������ ����������� ����.
     * @param array $arHtmlControl ������ ���������� �� �����. �������� �������� NAME � VALUE.
     * @return string HTML ��� ������.
     * @static
     */
    function GetAdminListEditHTML($arUserField, $arHtmlControl)
    {
        if ($arUserField["SETTINGS"]["ROWS"] < 2)
            return '<input type="text" ' .
                'name="' . $arHtmlControl["NAME"] . '" ' .
                'size="' . $arUserField["SETTINGS"]["SIZE"] . '" ' .
                ($arUserField["SETTINGS"]["MAX_LENGTH"] > 0 ? 'maxlength="' . $arUserField["SETTINGS"]["MAX_LENGTH"] . '" ' : '') .
                'value="' . $arHtmlControl["VALUE"] . '" ' .
                '>';
        else
            return '<textarea ' .
                'name="' . $arHtmlControl["NAME"] . '" ' .
                'cols="' . $arUserField["SETTINGS"]["SIZE"] . '" ' .
                'rows="' . $arUserField["SETTINGS"]["ROWS"] . '" ' .
                ($arUserField["SETTINGS"]["MAX_LENGTH"] > 0 ? 'maxlength="' . $arUserField["SETTINGS"]["MAX_LENGTH"] . '" ' : '') .
                '>' . $arHtmlControl["VALUE"] . '</textarea>';
    }

    /**
     * ��� ������� ���������� ��� ������ <b>��������������</b> �������� � ������ ��������� � ������ <b>��������������</b>.
     *
     * <p>���������� html ��� ����������� � ������ �������.</p>
     * <p>���� ����� �� ������������� ����� �������,
     * �� �������� ����� "�������" ��������� html �� ������� GetAdminListEditHTML</p>
     * <p>�������� $arHtmlControl ��������� � html ����������� ����.</p>
     * <p>���� VALUE $arHtmlControl - ������.</p>
     * @param array $arUserField ������ ����������� ����.
     * @param array $arHtmlControl ������ ���������� �� �����. �������� �������� NAME � VALUE.
     * @return string HTML ��� ������.
     * @static
     */
    /*
        function GetAdminListEditHTMLMulty($arUserField, $arHtmlControl)
        {
            $result = array();
            foreach($arHtmlControl["VALUE"] as $value)
            {
                if($arUserField["SETTINGS"]["ROWS"] < 2)
                    $result[] = '<input type="text" '.
                        'name="'.$arHtmlControl["NAME"].'" '.
                        'size="'.$arUserField["SETTINGS"]["SIZE"].'" '.
                        ($arUserField["SETTINGS"]["MAX_LENGTH"]>0? 'maxlength="'.$arUserField["SETTINGS"]["MAX_LENGTH"].'" ': '').
                        'value="'.$value.'" '.
                        '>';
                else
                    $result[] = '<textarea '.
                        'name="'.$arHtmlControl["NAME"].'" '.
                        'cols="'.$arUserField["SETTINGS"]["SIZE"].'" '.
                        'rows="'.$arUserField["SETTINGS"]["ROWS"].'" '.
                        ($arUserField["SETTINGS"]["MAX_LENGTH"]>0? 'maxlength="'.$arUserField["SETTINGS"]["MAX_LENGTH"].'" ': '').
                    '>'.$value.'</textarea>';
            }
            return '&nbsp;'.implode("<br>", $result);
        }
    */
    /**
     * ��� ������� ���������� ��� ������ �������� �������� � ��������� ����� �����.
     *
     * <p>���������� html.</p>
     * <p>���� ����� �� ������������� ����� �������,
     * �� �������� ����� ������� ��������� ��������� � ���������� �������� ��� ��������� bitrix:system.field.view</p>
     * @param array $arUserField ������ ����������� ����.
     * @param array $arAdditionalParameters �������������� ��������� (�������� context).
     * @return string HTML ��� ������.
     * @static
     */

    public static function GetPublicView($arUserField, $arAdditionalParameters = array())
    {
        $value = static::normalizeFieldValue($arUserField["VALUE"]);

        $html = '';
        $first = true;
        foreach ($value as $res) {
            if (!$first) {
                $html .= static::getHelper()->getMultipleValuesSeparator();
            }
            $first = false;

            $res = \Bitrix\Main\Text\HtmlFilter::encode($res);

            if ($arUserField['SETTINGS']['ROWS'] > 1 && strlen($res) > 0) {
                $res = nl2br($res);
            }

            if (strlen($arUserField['PROPERTY_VALUE_LINK']) > 0) {
                $res = '<a href="' . htmlspecialcharsbx(str_replace('#VALUE#', urlencode($res), $arUserField['PROPERTY_VALUE_LINK'])) . '">' . $res . '</a>';
            }

            $html .= static::getHelper()->wrapSingleField($res);
        }

        static::initDisplay();

        return static::getHelper()->wrapDisplayResult($html);
    }

    /**
     * ��� ������� ���������� ��� �������������� �������� �������� � ��������� ����� �����.
     *
     * <p>���������� html.</p>
     * <p>���� ����� �� ������������� ����� �������,
     * �� �������� ����� ������� ��������� ��������� � ���������� �������� ��� ��������� bitrix:system.field.edit</p>
     * @param array $arUserField ������ ����������� ����.
     * @param array $arAdditionalParameters �������������� ��������� (�������� context ��� bVarsFromForm).
     * @return string HTML ��� ������.
     * @static
     */

    public static function GetPublicEdit($arUserField, $arAdditionalParameters = array())
    {
        $fieldName = static::getFieldName($arUserField, $arAdditionalParameters);
        $value = static::getFieldValue($arUserField, $arAdditionalParameters);

        $html = '';

        foreach ($value as $res) {
            $attrList = array();

            if ($arUserField["SETTINGS"]["MAX_LENGTH"] > 0) {
                $attrList['maxlength'] = intval($arUserField["SETTINGS"]["MAX_LENGTH"]);
            }

            if ($arUserField["EDIT_IN_LIST"] != "Y") {
                $attrList['disabled'] = 'disabled';
            }

            if ($arUserField["SETTINGS"]["ROWS"] < 2) {
                if ($arUserField["SETTINGS"]["SIZE"] > 0) {
                    $attrList['size'] = intval($arUserField["SETTINGS"]["SIZE"]);
                }
            } else {
                $attrList['cols'] = intval($arUserField["SETTINGS"]["SIZE"]);
                $attrList['rows'] = intval($arUserField["SETTINGS"]["ROWS"]);
            }

            if (array_key_exists('attribute', $arAdditionalParameters)) {
                $attrList = array_merge($attrList, $arAdditionalParameters['attribute']);
            }

            if (isset($attrList['class']) && is_array($attrList['class'])) {
                $attrList['class'] = implode(' ', $attrList['class']);
            }

            $attrList['class'] = static::getHelper()->getCssClassName() . (isset($attrList['class']) ? ' ' . $attrList['class'] : '');

            $attrList['name'] = $fieldName;
            $attrList['tabindex'] = '0';

            if ($arUserField["SETTINGS"]["ROWS"] < 2) {
                $attrList['type'] = 'text';
                $attrList['value'] = $res;

                $html .= static::getHelper()->wrapSingleField('<input ' . static::buildTagAttributes($attrList) . '/>');
            } else {
                $html .= static::getHelper()->wrapSingleField('<textarea ' . static::buildTagAttributes($attrList) . '>' . htmlspecialcharsbx($res) . '</textarea>');
            }
        }

        if ($arUserField["MULTIPLE"] == "Y" && $arAdditionalParameters["SHOW_BUTTON"] != "N") {
            $html .= static::getHelper()->getCloneButton($fieldName);
        }

        static::initDisplay();

        return static::getHelper()->wrapDisplayResult($html);
    }


    /**
     * ����� ���������������� ���������� ������� onBeforeGetPublicView � ��������� �����������
     * ���� ����������� � ����������� ����������������� ��������.
     * \Bitrix\Main\EventManager::getInstance()->addEventHandler(
     * 'main',
     * "onBeforeGetPublicView",
     * array("CUserTypeString", "onBeforeGetPublicView")
     * );
     * ���������� ����� ������� � ��� ��������������: onBeforeGetPublicEdit (EDIT_COMPONENT_NAME � EDIT_COMPONENT_TEMPLATE)
     */
    /*
        public static function onBeforeGetPublicView($event)
        {
            $params = $event->getParameters();
            $arUserField = &$params[0];
            $arAdditionalParameters = &$params[1];
            if ($arUserField["USER_TYPE_ID"] == "string")
            {
                $arUserField["VIEW_COMPONENT_NAME"] = "my:system.field.view";
                $arUserField["VIEW_COMPONENT_TEMPLATE"] = "string";
            }
        }
    */

    /**
     * ����� ���������������� ���������� ������� onGetPublicView � �������� �������� ���, ��� ��� �����.
     * \Bitrix\Main\EventManager::getInstance()->addEventHandler(
     * 'main',
     * "onGetPublicView",
     * array("CUserTypeString", "onGetPublicView")
     * );
     * ���������� ����� ������� � ��� ��������������: onGetPublicEdit
     */
    /*
        public static function onGetPublicView($event)
        {
            $params = $event->getParameters();
            $arUserField = $params[0];
            $arAdditionalParameters = $params[1];
            if ($arUserField["USER_TYPE_ID"] == "string")
            {
                $html = "demo string";
                return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, $html);
            }
        }
    */

    /**
     * ����� ���������������� ���������� ������� onAfterGetPublicView � �������������� html ����� ��� �������.
     * \Bitrix\Main\EventManager::getInstance()->addEventHandler(
     * 'main',
     * "onAfterGetPublicView",
     * array("CUserTypeString", "onAfterGetPublicView")
     * );
     * ���������� ����� ������� � ��� ��������������: onAfterGetPublicEdit
     */
    /*
        public static function onAfterGetPublicView($event)
        {
            $params = $event->getParameters();
            $arUserField = $params[0];
            $arAdditionalParameters = $params[1];
            $html = &$params[2];
            if ($arUserField["USER_TYPE_ID"] == "string")
            {
                $html .= "!";
            }
        }
    */

    /**
     * ��� ������� ���������.
     *
     * <p>���������� �� ������ CheckFields ������� $USER_FIELD_MANAGER.</p>
     * <p>������� � ���� ������� ����� ���� ������ �� ������� Add/Update �������� ��������� �������.</p>
     * <p>����������� 2 ��������:</p>
     * <ul>
     * <li>�� ����������� ����� (���� � ���������� ����������� ����� ������ 0).
     * <li>�� ���������� ��������� (���� ������ � ����������).
     * </ul>
     * @param array $arUserField ������ ����������� ����.
     * @param string $value �������� ��� �������� �� ����������
     * @return array ������ �������� ("id","text") ������.
     * @static
     */
    function CheckFields($arUserField, $value)
    {
        $aMsg = array();
        if ($value <> '' && strlen($value) < $arUserField["SETTINGS"]["MIN_LENGTH"]) {
            $aMsg[] = array(
                "id" => $arUserField["FIELD_NAME"],
                "text" => GetMessage("USER_TYPE_STRING_MIN_LEGTH_ERROR",
                    array(
                        "#FIELD_NAME#" => ($arUserField["EDIT_FORM_LABEL"] <> '' ? $arUserField["EDIT_FORM_LABEL"] : $arUserField["FIELD_NAME"]),
                        "#MIN_LENGTH#" => $arUserField["SETTINGS"]["MIN_LENGTH"]
                    )
                ),
            );
        }
        if ($arUserField["SETTINGS"]["MAX_LENGTH"] > 0 && strlen($value) > $arUserField["SETTINGS"]["MAX_LENGTH"]) {
            $aMsg[] = array(
                "id" => $arUserField["FIELD_NAME"],
                "text" => GetMessage("USER_TYPE_STRING_MAX_LEGTH_ERROR",
                    array(
                        "#FIELD_NAME#" => ($arUserField["EDIT_FORM_LABEL"] <> '' ? $arUserField["EDIT_FORM_LABEL"] : $arUserField["FIELD_NAME"]),
                        "#MAX_LENGTH#" => $arUserField["SETTINGS"]["MAX_LENGTH"]
                    )
                ),
            );
        }
        if ($arUserField["SETTINGS"]["REGEXP"] <> '' && !preg_match($arUserField["SETTINGS"]["REGEXP"], $value)) {
            $aMsg[] = array(
                "id" => $arUserField["FIELD_NAME"],
                "text" => (strlen($arUserField["ERROR_MESSAGE"]) > 0 ?
                    $arUserField["ERROR_MESSAGE"] :
                    GetMessage("USER_TYPE_STRING_REGEXP_ERROR",
                        array(
                            "#FIELD_NAME#" => ($arUserField["EDIT_FORM_LABEL"] <> '' ? $arUserField["EDIT_FORM_LABEL"] : $arUserField["FIELD_NAME"]),
                        )
                    )
                ),
            );
        }
        return $aMsg;
    }

    /**
     * ��� ������� ������ ������� ������������� �������� ���� ��� ������.
     *
     * <p>���������� �� ������ OnSearchIndex ������� $USER_FIELD_MANAGER.</p>
     * <p>������� � ���� ������� ���������� � ������� ���������� ���������� ������� ��������.</p>
     * <p>��� ������������� �������� ���� VALUE - ������.</p>
     * @param array $arUserField ������ ����������� ����.
     * @return string �������� ����������.
     * @static
     */
    function OnSearchIndex($arUserField)
    {
        if (is_array($arUserField["VALUE"]))
            return implode("\r\n", $arUserField["VALUE"]);
        else
            return $arUserField["VALUE"];
    }

    /**
     * ��� ������� ���������� ����� ����������� �������� � ��.
     *
     * <p>���������� �� ������ Update ������� $USER_FIELD_MANAGER.</p>
     * <p>��� ������������� �������� ������� ���������� ��������� ���.</p>
     * @param array $arUserField ������ ����������� ����.
     * @param mixed $value ��������.
     * @return string �������� ��� ������� � ��.
     * @static
     */
    /*
        function OnBeforeSave($arUserField, $value)
        {
            if(strlen($value)>0)
                return "".round(doubleval($value), $arUserField["SETTINGS"]["PRECISION"]);
        }
    */
}

?>
