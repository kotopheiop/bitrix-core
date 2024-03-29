<?

IncludeModuleLangFile(__FILE__);

class CIBlockPropertyUserID
{
    const USER_TYPE = 'UserID';

    public static function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => self::USER_TYPE,
            "DESCRIPTION" => GetMessage("IBLOCK_PROP_USERID_DESC"),
            "GetAdminListViewHTML" => array(__CLASS__, "GetAdminListViewHTML"),
            "GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
            "ConvertToDB" => array(__CLASS__, "ConvertToDB"),
            "ConvertFromDB" => array(__CLASS__, "ConvertFromDB"),
            "GetSettingsHTML" => array(__CLASS__, "GetSettingsHTML"),
            "AddFilterFields" => array(__CLASS__, 'AddFilterFields'),
            "GetAdminFilterHTML" => array(__CLASS__, "GetAdminFilterHTML"),
            "GetUIFilterProperty" => array(__CLASS__, 'GetUIFilterProperty')
        );
    }

    public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        static $cache = array();
        $value = intval($value["VALUE"]);
        if (!array_key_exists($value, $cache)) {
            $rsUsers = CUser::GetList('', '', array("ID" => $value));
            $cache[$value] = $rsUsers->Fetch();
        }
        $arUser = $cache[$value];
        if ($arUser) {
            return "[<a title='" . GetMessage(
                    "MAIN_EDIT_USER_PROFILE"
                ) . "' href='user_edit.php?ID=" . $arUser["ID"] . "&lang=" . LANGUAGE_ID . "'>" . $arUser["ID"] . "</a>]&nbsp;(" . htmlspecialcharsbx(
                    $arUser["LOGIN"]
                ) . ") " . htmlspecialcharsbx($arUser["NAME"]) . " " . htmlspecialcharsbx($arUser["LAST_NAME"]);
        } else {
            return "&nbsp;";
        }
    }

    //PARAMETERS:
    //$arProperty - b_iblock_property.*
    //$value - array("VALUE","DESCRIPTION") -- here comes HTML form value
    //strHTMLControlName - array("VALUE","DESCRIPTION")
    //return:
    //safe html

    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        global $USER;
        $default_value = intval($value["VALUE"]);
        $res = "";
        if ($default_value == $USER->GetID()) {
            $select = "CU";
            $res = "[<a title='" . GetMessage(
                    "MAIN_EDIT_USER_PROFILE"
                ) . "'  href='/bitrix/admin/user_edit.php?ID=" . $USER->GetID() . "&lang=" . LANG . "'>" . $USER->GetID(
                ) . "</a>] (" . htmlspecialcharsbx($USER->GetLogin()) . ") " . htmlspecialcharsbx(
                    $USER->GetFirstName()
                ) . " " . htmlspecialcharsbx($USER->GetLastName());
        } elseif ($default_value > 0) {
            $select = "SU";
            $rsUsers = CUser::GetList('', '', array("ID" => $default_value));
            if ($arUser = $rsUsers->Fetch()) {
                $res = "[<a title='" . GetMessage(
                        "MAIN_EDIT_USER_PROFILE"
                    ) . "'  href='/bitrix/admin/user_edit.php?ID=" . $arUser["ID"] . "&lang=" . LANG . "'>" . $arUser["ID"] . "</a>] (" . htmlspecialcharsbx(
                        $arUser["LOGIN"]
                    ) . ") " . htmlspecialcharsbx($arUser["NAME"]) . " " . htmlspecialcharsbx($arUser["LAST_NAME"]);
            } else {
                $res = "&nbsp;" . GetMessage("MAIN_NOT_FOUND");
            }
        } else {
            $select = "none";
            $default_value = "";
        }
        $name_x = preg_replace("/([^a-z0-9])/is", "x", $strHTMLControlName["VALUE"]);
        if (trim($strHTMLControlName["FORM_NAME"]) == '') {
            $strHTMLControlName["FORM_NAME"] = "form_element";
        }

        $selfFolderUrl = (defined("SELF_FOLDER_URL") ? SELF_FOLDER_URL : "/bitrix/admin/");

        ob_start();
        ?><select id="SELECT<?= htmlspecialcharsbx($strHTMLControlName["VALUE"]) ?>"
                  name="SELECT<?= htmlspecialcharsbx($strHTMLControlName["VALUE"]) ?>"onchange="if(this.value == 'none')
            {
            var v=document.getElementById('<?= htmlspecialcharsbx($strHTMLControlName["VALUE"]) ?>');
            v.value = '';
            v.readOnly = true;
            document.getElementById('FindUser<?= $name_x ?>').disabled = true;
            }
            else
            {
            var v=document.getElementById('<?= htmlspecialcharsbx($strHTMLControlName["VALUE"]) ?>');
            v.value = this.value == 'CU'?'<?= $USER->GetID() ?>':'';
            v.readOnly = false;
            document.getElementById('FindUser<?= $name_x ?>').disabled = false;
            }">
        <option value="none"<? if ($select == "none") echo " selected" ?>><?= GetMessage(
                "IBLOCK_PROP_USERID_NONE"
            ) ?></option>
        <option value="CU"<? if ($select == "CU") echo " selected" ?>><?= GetMessage(
                "IBLOCK_PROP_USERID_CURR"
            ) ?></option>
        <option value="SU"<? if ($select == "SU") echo " selected" ?>><?= GetMessage(
                "IBLOCK_PROP_USERID_OTHR"
            ) ?></option>
        </select>&nbsp;
        <? echo FindUserIDNew(
        htmlspecialcharsbx($strHTMLControlName["VALUE"]),
        $value["VALUE"],
        $res,
        htmlspecialcharsEx($strHTMLControlName["FORM_NAME"]),
        $select,
        "3",
        "",
        "...",
        "typeinput",
        "tablebodybutton",
        $selfFolderUrl . "user_search.php"
    );
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }

    //PARAMETERS:
    //$arProperty - b_iblock_property.*
    //$value - array("VALUE",["DESCRIPTION"]) -- here comes HTML form value
    //return:
    //array of error messages
    //PARAMETERS:
    //$arProperty - b_iblock_property.*
    //$value - array("VALUE",["DESCRIPTION"]) -- here comes HTML form value
    //return:
    //DB form of the value
    public static function ConvertToDB($arProperty, $value)
    {
        $value["VALUE"] = intval($value["VALUE"]);
        if ($value["VALUE"] <= 0) {
            $value["VALUE"] = "";
        }
        return $value;
    }

    public static function ConvertFromDB($arProperty, $value)
    {
        $value["VALUE"] = intval($value["VALUE"]);
        if ($value["VALUE"] <= 0) {
            $value["VALUE"] = "";
        }
        return $value;
    }

    public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
    {
        $arPropertyFields = array(
            "HIDE" => array("WITH_DESCRIPTION"),
        );
        return '';
    }

    public static function AddFilterFields($arProperty, $control, &$arFilter, &$filtered)
    {
        $filtered = false;
        $filterValue = self::getFilterValue($control);

        if ($filterValue !== null) {
            $arFilter["=PROPERTY_" . $arProperty["ID"]] = $filterValue;
            $filtered = true;
        }
    }

    public static function GetAdminFilterHTML($property, $control)
    {
        $controlName = $control["VALUE"];

        $value = (string)self::getFilterValue($control);
        return '<input type="text" name="' . $controlName . '" value="' . htmlspecialcharsbx($value) . '" size="30">';
    }

    /**
     * @param array $property
     * @param array $strHTMLControlName
     * @param array &$fields
     * @return void
     */
    public static function GetUIFilterProperty($property, $strHTMLControlName, &$fields)
    {
        $fields["type"] = "custom_entity";
        $fields["filterable"] = "";
        $fields["selector"] = array("type" => "user");
        $fields["operators"] = array("default" => "=");
    }

    private static function getFilterValue($control)
    {
        $filterValue = null;

        $controlName = $control["VALUE"];

        if (isset($GLOBALS[$controlName]) && !is_array($GLOBALS[$controlName])) {
            if (is_int($GLOBALS[$controlName])) {
                $filterValue = $GLOBALS[$controlName];
            } elseif (is_string($GLOBALS[$controlName])) {
                $rawValue = trim($GLOBALS[$controlName]);
                if ($rawValue !== '') {
                    $filterValue = (int)$rawValue;
                }
            }
        }

        return $filterValue;
    }
}

function FindUserIDNew(
    $tag_name,
    $tag_value,
    $user_name = "",
    $form_name = "form1",
    $select = "none",
    $tag_size = "3",
    $tag_maxlength = "",
    $button_value = "...",
    $tag_class = "typeinput",
    $button_class = "tablebodybutton",
    $search_page = "/bitrix/admin/user_search.php"
) {
    global $APPLICATION, $USER;
    $tag_name_x = preg_replace("/([^a-z0-9])/is", "x", $tag_name);
    $tag_name_escaped = CUtil::JSEscape($tag_name);

    if ($APPLICATION->GetGroupRight("main") >= "R") {
        $selfFolderUrl = (defined("SELF_FOLDER_URL") ? SELF_FOLDER_URL : "/bitrix/admin/");

        $strReturn = "
<input type=\"text\" name=\"" . $tag_name . "\" id=\"" . $tag_name . "\" value=\"" . ($select == "none" ? "" : $tag_value) . "\" size=\"" . $tag_size . "\" maxlength=\"" . $tag_maxlength . "\" class=\"" . $tag_class . "\">
<IFRAME style=\"width:0px; height:0px; border: 0px\" src=\"javascript:void(0)\" name=\"hiddenframe" . $tag_name . "\" id=\"hiddenframe" . $tag_name . "\"></IFRAME>
<input class=\"" . $button_class . "\" type=\"button\" name=\"FindUser" . $tag_name_x . "\" id=\"FindUser" . $tag_name_x . "\" OnClick=\"window.open('" . $search_page . "?lang=" . LANGUAGE_ID . "&FN=" . $form_name . "&FC=" . $tag_name_escaped . "', '', 'scrollbars=yes,resizable=yes,width=760,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));\" value=\"" . $button_value . "\" " . ($select == "none" ? "disabled" : "") . ">
<span id=\"div_" . $tag_name . "\">" . $user_name . "</span>
<script>
";
        if ($user_name == "") {
            $strReturn .= "var tv" . $tag_name_x . "='';\n";
        } else {
            $strReturn .= "var tv" . $tag_name_x . "='" . CUtil::JSEscape($tag_value) . "';\n";
        }

        $strReturn .= "
function Ch" . $tag_name_x . "()
{
	var DV_" . $tag_name_x . ";
	DV_" . $tag_name_x . " = document.getElementById(\"div_" . $tag_name_escaped . "\");
	if (!!DV_" . $tag_name_x . ")
	{
		if (
			document." . $form_name . "
			&& document." . $form_name . "['" . $tag_name_escaped . "']
			&& typeof tv" . $tag_name_x . " != 'undefined'
			&& tv" . $tag_name_x . " != document." . $form_name . "['" . $tag_name_escaped . "'].value
		)
		{
			tv" . $tag_name_x . "=document." . $form_name . "['" . $tag_name_escaped . "'].value;
			if (tv" . $tag_name_x . "!='')
			{
				DV_" . $tag_name_x . ".innerHTML = '<i>" . GetMessage("MAIN_WAIT") . "</i>';

				if (tv" . $tag_name_x . "!=" . intval($USER->GetID()) . ")
				{
					document.getElementById(\"hiddenframe" . $tag_name_escaped . "\").src='" . $selfFolderUrl . "get_user.php?ID=' + tv" . $tag_name_x . "+'&strName=" . $tag_name_escaped . "&lang=" . LANGUAGE_ID . (defined(
                "ADMIN_SECTION"
            ) && ADMIN_SECTION === true ? "&admin_section=Y" : "") . "';
					document.getElementById('SELECT" . $tag_name_escaped . "').value = 'SU';
				}
				else
				{
					DV_" . $tag_name_x . ".innerHTML = '" . CUtil::JSEscape(
                "[<a title=\"" . GetMessage(
                    "MAIN_EDIT_USER_PROFILE"
                ) . "\" class=\"tablebodylink\" href=\"/bitrix/admin/user_edit.php?ID=" . $USER->GetID(
                ) . "&lang=" . LANGUAGE_ID . "\">" . $USER->GetID() . "</a>] (" . htmlspecialcharsbx(
                    $USER->GetLogin()
                ) . ") " . htmlspecialcharsbx($USER->GetFirstName()) . " " . htmlspecialcharsbx($USER->GetLastName())
            ) . "';
					document.getElementById('SELECT" . $tag_name_escaped . "').value = 'CU';
				}
			}
			else
			{
				DV_" . $tag_name_x . ".innerHTML = '';
			}
		}
		else if (
			DV_" . $tag_name_x . "
			&& DV_" . $tag_name_x . ".innerHTML.length > 0
			&& document." . $form_name . "
			&& document." . $form_name . "['" . $tag_name_escaped . "']
			&& document." . $form_name . "['" . $tag_name_escaped . "'].value == ''
		)
		{
			document.getElementById('div_" . $tag_name . "').innerHTML = '';
		}
	}
	setTimeout(function(){Ch" . $tag_name_x . "()},1000);
}
Ch" . $tag_name_x . "();
//-->
</script>
";
    } else {
        $strReturn = "
			<input type=\"text\" name=\"$tag_name\" id=\"$tag_name\" value=\"$tag_value\" size=\"$tag_size\" maxlength=\"strMaxLenght\">
			<input type=\"button\" name=\"FindUser" . $tag_name_x . "\" id=\"FindUser" . $tag_name_x . "\" OnClick=\"window.open('" . $search_page . "?lang=" . LANGUAGE_ID . "&FN=$form_name&FC=$tag_name_escaped', '', 'scrollbars=yes,resizable=yes,width=760,height=560,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));\" value=\"$button_value\">
			$user_name
			";
    }
    return $strReturn;
}