<?

use Bitrix\Iblock;

global $IBLOCK_CACHE_PROPERTY;
$IBLOCK_CACHE_PROPERTY = Array();
IncludeModuleLangFile(__FILE__);

class CAllIBlockProperty
{
    public $LAST_ERROR = "";

    public static function GetList($arOrder = Array(), $arFilter = Array())
    {
        global $DB;

        $strSql = "
			SELECT BP.*
			FROM b_iblock_property BP
		";

        $bJoinIBlock = false;
        $arSqlSearch = array();
        foreach ($arFilter as $key => $val) {
            $val = $DB->ForSql($val);
            $key = mb_strtoupper($key);

            switch ($key) {
                case "ACTIVE":
                case "SEARCHABLE":
                case "FILTRABLE":
                case "IS_REQUIRED":
                case "MULTIPLE":
                    if ($val == "Y" || $val == "N") {
                        $arSqlSearch[] = "BP." . $key . " = '" . $val . "'";
                    }
                    break;
                case "?CODE":
                case "?NAME":
                    $arSqlSearch[] = CIBlock::FilterCreate("BP." . mb_substr($key, 1), $val, "string", "E");
                    break;
                case "CODE":
                case "NAME":
                    $arSqlSearch[] = "UPPER(BP." . $key . ") LIKE UPPER('" . $val . "')";
                    break;
                case "XML_ID":
                case "EXTERNAL_ID":
                    $arSqlSearch[] = "BP.XML_ID LIKE '" . $val . "'";
                    break;
                case "!XML_ID":
                case "!EXTERNAL_ID":
                    $arSqlSearch[] = "(BP.XML_ID IS NULL OR NOT (BP.XML_ID LIKE '" . $val . "'))";
                    break;
                case "TMP_ID":
                    $arSqlSearch[] = "BP.TMP_ID LIKE '" . $val . "'";
                    break;
                case "!TMP_ID":
                    $arSqlSearch[] = "(BP.TMP_ID IS NULL OR NOT (BP.TMP_ID LIKE '" . $val . "'))";
                    break;
                case "PROPERTY_TYPE":
                    $ar = explode(":", $val);
                    if (count($ar) == 2) {
                        $val = $ar[0];
                        $arSqlSearch[] = "BP.USER_TYPE = '" . $ar[1] . "'";
                    }
                    $arSqlSearch[] = "BP." . $key . " = '" . $val . "'";
                    break;
                case "USER_TYPE":
                    $arSqlSearch[] = "BP." . $key . " = '" . $val . "'";
                    break;
                case "ID":
                case "IBLOCK_ID":
                case "LINK_IBLOCK_ID":
                case "VERSION":
                    $arSqlSearch[] = "BP." . $key . " = " . (int)$val;
                    break;
                case "IBLOCK_CODE":
                    $arSqlSearch[] = "UPPER(B.CODE) = UPPER('" . $val . "')";
                    $bJoinIBlock = true;
                    break;
            }
        }

        if ($bJoinIBlock) {
            $strSql .= "
				INNER JOIN b_iblock B ON B.ID = BP.IBLOCK_ID
			";
        }

        if (!empty($arSqlSearch)) {
            $strSql .= "
				WHERE " . implode("\n\t\t\t\tAND ", $arSqlSearch) . "
			";
        }

        $allowKeys = array(
            "ID" => true,
            "IBLOCK_ID" => true,
            "NAME" => true,
            "ACTIVE" => true,
            "SORT" => true,
            "FILTRABLE" => true,
            "SEARCHABLE" => true
        );
        $orderKeys = array();
        $arSqlOrder = array();
        foreach ($arOrder as $by => $order) {
            $by = mb_strtoupper($by);
            if (!isset($allowKeys[$by])) {
                $by = "TIMESTAMP_X";
            }
            if (isset($orderKeys[$by])) {
                continue;
            }
            $orderKeys[$by] = true;
            $order = mb_strtoupper($order) == "ASC" ? "ASC" : "DESC";

            $arSqlOrder[] = "BP." . $by . " " . $order;
        }

        if (!empty($arSqlOrder)) {
            $strSql .= "
				ORDER BY " . implode(", ", $arSqlOrder) . "
			";
        }

        $res = $DB->Query($strSql, false, "FILE: " . __FILE__ . "<br> LINE: " . __LINE__);
        $res = new CIBlockPropertyResult($res);
        return $res;
    }

    ///////////////////////////////////////////////////////////////////
    // Delete by property ID
    ///////////////////////////////////////////////////////////////////
    public static function Delete($ID)
    {
        /** @var CMain $APPLICATION */
        global $DB, $APPLICATION;
        $ID = (int)$ID;
        if ($ID <= 0) {
            return false;
        }

        $APPLICATION->ResetException();
        foreach (GetModuleEvents("iblock", "OnBeforeIBlockPropertyDelete", true) as $arEvent) {
            if (ExecuteModuleEventEx($arEvent, array($ID)) === false) {
                if ($ex = $APPLICATION->GetException()) {
                    $APPLICATION->ThrowException($ex->GetString());
                } else {
                    $APPLICATION->ThrowException(GetMessage("MAIN_BEFORE_DEL_ERR1"));
                }

                return false;
            }
        }

        foreach (GetModuleEvents("iblock", "OnIBlockPropertyDelete", true) as $arEvent) {
            ExecuteModuleEventEx($arEvent, array($ID));
        }

        if (!CIBlockPropertyEnum::DeleteByPropertyID($ID, true)) {
            return false;
        }

        CIBlockSectionPropertyLink::DeleteByProperty($ID);
        Iblock\PropertyFeatureTable::deleteByProperty($ID);

        $rsProperty = CIBlockProperty::GetByID($ID);
        $arProperty = $rsProperty->Fetch();
        if ($arProperty["VERSION"] == 2) {
            if ($arProperty["PROPERTY_TYPE"] == "F") {
                if ($arProperty["MULTIPLE"] == "Y") {
                    $strSql = "
						SELECT	VALUE
						FROM	b_iblock_element_prop_m" . $arProperty["IBLOCK_ID"] . "
						WHERE	IBLOCK_PROPERTY_ID=" . $ID . "
					";
                } else {
                    $strSql = "
						SELECT	PROPERTY_" . $ID . " VALUE
						FROM	b_iblock_element_prop_s" . $arProperty["IBLOCK_ID"] . "
						WHERE	PROPERTY_" . $ID . " is not null
					";
                }
                $res = $DB->Query($strSql);
                while ($arr = $res->Fetch()) {
                    CFile::Delete($arr["VALUE"]);
                }
            }
            if (!$DB->Query("DELETE FROM b_iblock_section_element WHERE ADDITIONAL_PROPERTY_ID=" . $ID, true)) {
                return false;
            }
            $strSql = "
				DELETE
				FROM b_iblock_element_prop_m" . $arProperty["IBLOCK_ID"] . "
				WHERE IBLOCK_PROPERTY_ID=" . $ID . "
			";
            if (!$DB->Query($strSql)) {
                return false;
            }
            $arSql = CIBlockProperty::DropColumnSQL(
                "b_iblock_element_prop_s" . $arProperty["IBLOCK_ID"],
                array("PROPERTY_" . $ID, "DESCRIPTION_" . $ID)
            );
            foreach ($arSql as $strSql) {
                if (!$DB->DDL($strSql)) {
                    return false;
                }
            }
        } else {
            $res = $DB->Query(
                "SELECT EP.VALUE FROM b_iblock_property P, b_iblock_element_property EP WHERE P.ID=" . $ID . " AND P.ID=EP.IBLOCK_PROPERTY_ID AND P.PROPERTY_TYPE='F'"
            );
            while ($arr = $res->Fetch()) {
                CFile::Delete($arr["VALUE"]);
            }
            if (!$DB->Query("DELETE FROM b_iblock_section_element WHERE ADDITIONAL_PROPERTY_ID=" . $ID, true)) {
                return false;
            }
            if (!$DB->Query("DELETE FROM b_iblock_element_property WHERE IBLOCK_PROPERTY_ID=" . $ID, true)) {
                return false;
            }
        }

        $seq = new CIBlockSequence($arProperty["IBLOCK_ID"], $ID);
        $seq->Drop();

        CIBlock::clearIblockTagCache($arProperty["IBLOCK_ID"]);

        Iblock\PropertyTable::getEntity()->cleanCache();

        $res = $DB->Query("DELETE FROM b_iblock_property WHERE ID=" . $ID, true);

        foreach (GetModuleEvents("iblock", "OnAfterIBlockPropertyDelete", true) as $arEvent) {
            ExecuteModuleEventEx($arEvent, array($arProperty));
        }

        return $res;
    }
    ///////////////////////////////////////////////////////////////////
    // Add
    ///////////////////////////////////////////////////////////////////
    public function Add($arFields)
    {
        global $DB;

        if (is_set($arFields, "ACTIVE") && $arFields["ACTIVE"] != "Y") {
            $arFields["ACTIVE"] = "N";
        }
        if (!isset($arFields["SEARCHABLE"]) || $arFields["SEARCHABLE"] != "Y") {
            $arFields["SEARCHABLE"] = "N";
        }
        if (!isset($arFields["FILTRABLE"]) || $arFields["FILTRABLE"] != "Y") {
            $arFields["FILTRABLE"] = "N";
        }
        if (is_set($arFields, "MULTIPLE") && $arFields["MULTIPLE"] != "Y") {
            $arFields["MULTIPLE"] = "N";
        }
        if (is_set($arFields, "LIST_TYPE") && $arFields["LIST_TYPE"] != "C") {
            $arFields["LIST_TYPE"] = "L";
        }

        if (!$this->CheckFields($arFields)) {
            $Result = false;
            $arFields["RESULT_MESSAGE"] = &$this->LAST_ERROR;
        } else {
            $arFields["VERSION"] = CIBlockElement::GetIBVersion($arFields["IBLOCK_ID"]);
            unset($arFields["ID"]);
            if (isset($arFields["USER_TYPE"])) {
                $arUserType = CIBlockProperty::GetUserType($arFields["USER_TYPE"]);
                if (array_key_exists("ConvertToDB", $arUserType)) {
                    $arValue = array(
                        "VALUE" => $arFields["DEFAULT_VALUE"],
                        "DEFAULT_VALUE" => true
                    );
                    $arValue = call_user_func_array($arUserType["ConvertToDB"], array($arFields, $arValue));
                    if (is_array($arValue) && isset($arValue["VALUE"]) && mb_strlen($arValue["VALUE"])) {
                        $arFields["DEFAULT_VALUE"] = $arValue["VALUE"];
                    } else {
                        $arFields["DEFAULT_VALUE"] = false;
                    }
                }
                if (array_key_exists("PrepareSettings", $arUserType)) {
                    $arFieldsResult = call_user_func_array($arUserType["PrepareSettings"], array($arFields));
                    if (is_array($arFieldsResult) && array_key_exists('USER_TYPE_SETTINGS', $arFieldsResult)) {
                        $arFields = array_merge($arFields, $arFieldsResult);
                        $arFields["USER_TYPE_SETTINGS"] = serialize($arFields["USER_TYPE_SETTINGS"]);
                    } else {
                        $arFields["USER_TYPE_SETTINGS"] = serialize($arFieldsResult);
                    }
                } else {
                    $arFields["USER_TYPE_SETTINGS"] = false;
                }
            } else {
                $arFields["USER_TYPE_SETTINGS"] = false;
            }
            $ID = $DB->Add("b_iblock_property", $arFields, array('USER_TYPE_SETTINGS'), "iblock");

            if ($arFields["VERSION"] == 2) {
                if ($this->_Add($ID, $arFields)) {
                    $Result = $ID;
                    $arFields["ID"] = &$ID;
                } else {
                    $DB->Query("DELETE FROM b_iblock_property WHERE ID = " . (int)$ID);
                    $this->LAST_ERROR = GetMessage(
                        "IBLOCK_PROPERTY_ADD_ERROR",
                        array(
                            "#ID#" => $ID,
                            "#CODE#" => "[14]" . $DB->GetErrorSQL(),
                        )
                    );
                    $Result = false;
                    $arFields["RESULT_MESSAGE"] = &$this->LAST_ERROR;
                }
            } else {
                $Result = $ID;
                $arFields["ID"] = &$ID;
            }

            if ($Result) {
                if (array_key_exists("VALUES", $arFields)) {
                    $this->UpdateEnum($ID, $arFields["VALUES"]);
                }

                if (CIBlock::GetArrayByID($arFields["IBLOCK_ID"], "SECTION_PROPERTY") === "Y") {
                    if (
                        !array_key_exists("SECTION_PROPERTY", $arFields)
                        || $arFields["SECTION_PROPERTY"] !== "N"
                    ) {
                        $arLink = array(
                            "SMART_FILTER" => $arFields["SMART_FILTER"],
                        );
                        if (array_key_exists("DISPLAY_TYPE", $arFields)) {
                            $arLink["DISPLAY_TYPE"] = $arFields["DISPLAY_TYPE"];
                        }
                        if (array_key_exists("DISPLAY_EXPANDED", $arFields)) {
                            $arLink["DISPLAY_EXPANDED"] = $arFields["DISPLAY_EXPANDED"];
                        }
                        if (array_key_exists("FILTER_HINT", $arFields)) {
                            $arLink["FILTER_HINT"] = $arFields["FILTER_HINT"];
                        }
                        CIBlockSectionPropertyLink::Add(0, $ID, $arLink);
                    }
                }

                if (!empty($arFields['FEATURES']) && is_array($arFields['FEATURES'])) {
                    $featureResult = Iblock\Model\PropertyFeature::addFeatures(
                        $ID,
                        $arFields['FEATURES']
                    );
                    //TODO: add error handling
                    unset($featureResult);
                }

                Iblock\PropertyTable::getEntity()->cleanCache();
            }
        }

        global $BX_IBLOCK_PROP_CACHE;
        if (isset($arFields["IBLOCK_ID"])) {
            unset($BX_IBLOCK_PROP_CACHE[$arFields["IBLOCK_ID"]]);
            CIBlock::clearIblockTagCache($arFields["IBLOCK_ID"]);
        }

        $arFields["RESULT"] = &$Result;

        foreach (GetModuleEvents("iblock", "OnAfterIBlockPropertyAdd", true) as $arEvent) {
            ExecuteModuleEventEx($arEvent, array(&$arFields));
        }

        return $Result;
    }
    ///////////////////////////////////////////////////////////////////
    // This one called before any Update or Add
    ///////////////////////////////////////////////////////////////////
    public function CheckFields(&$arFields, $ID = false, $bFormValidate = false)
    {
        /** @var CMain $APPLICATION */
        global $APPLICATION;
        $this->LAST_ERROR = "";
        if ($ID === false || array_key_exists("NAME", $arFields)) {
            if ($arFields["NAME"] == '') {
                $this->LAST_ERROR .= GetMessage("IBLOCK_PROPERTY_BAD_NAME") . "<br>";
            }
        }

        if (array_key_exists("CODE", $arFields) && mb_strlen($arFields["CODE"])) {
            if (mb_strpos("0123456789", mb_substr($arFields["CODE"], 0, 1)) !== false) {
                $this->LAST_ERROR .= GetMessage("IBLOCK_PROPERTY_CODE_FIRST_LETTER") . ": " . htmlspecialcharsbx(
                        $arFields["CODE"]
                    ) . "<br>";
            }
            if (preg_match("/[^A-Za-z0-9_]/", $arFields["CODE"])) {
                $this->LAST_ERROR .= GetMessage("IBLOCK_PROPERTY_WRONG_CODE") . ": " . htmlspecialcharsbx(
                        $arFields["CODE"]
                    ) . "<br>";
            }
        }

        if (!$bFormValidate) {
            if ($ID === false && !is_set($arFields, "IBLOCK_ID")) {
                $this->LAST_ERROR .= GetMessage("IBLOCK_BAD_BLOCK_ID") . "<br>";
            }

            if (is_set($arFields, "IBLOCK_ID")) {
                $arFields["IBLOCK_ID"] = (int)$arFields["IBLOCK_ID"];
                $r = CIBlock::GetList(array(), array("ID" => $arFields["IBLOCK_ID"], "CHECK_PERMISSIONS" => "N"));
                if (!$r->Fetch()) {
                    $this->LAST_ERROR .= GetMessage("IBLOCK_BAD_BLOCK_ID") . "<br>";
                }
            }
        }

        if (isset($arFields["USER_TYPE"])) {
            $arUserType = CIBlockProperty::GetUserType($arFields["USER_TYPE"]);
            if (isset($arUserType["CheckFields"])) {
                $value = array("VALUE" => $arFields["DEFAULT_VALUE"]);
                $arError = call_user_func_array($arUserType["CheckFields"], array($arFields, $value));
                if (is_array($arError) && count($arError) > 0) {
                    $this->LAST_ERROR .= implode("<br>", $arError) . "<br>";
                }
            }
        }

        if (!$bFormValidate) {
            $APPLICATION->ResetException();
            if ($ID === false) {
                $db_events = GetModuleEvents("iblock", "OnBeforeIBlockPropertyAdd", true);
            } else {
                $arFields["ID"] = $ID;
                $db_events = GetModuleEvents("iblock", "OnBeforeIBlockPropertyUpdate", true);
            }

            foreach ($db_events as $arEvent) {
                $bEventRes = ExecuteModuleEventEx($arEvent, array(&$arFields));
                if ($bEventRes === false) {
                    if ($err = $APPLICATION->GetException()) {
                        $this->LAST_ERROR .= $err->GetString() . "<br>";
                    } else {
                        $APPLICATION->ThrowException("Unknown error");
                        $this->LAST_ERROR .= "Unknown error.<br>";
                    }
                    break;
                }
            }
        }

        if ($this->LAST_ERROR <> '') {
            return false;
        }

        return true;
    }

    ///////////////////////////////////////////////////////////////////
    // Update method
    ///////////////////////////////////////////////////////////////////
    public function Update($ID, $arFields, $bCheckDescription = false)
    {
        global $DB;
        $ID = (int)$ID;

        if (is_set($arFields, "ACTIVE") && $arFields["ACTIVE"] != "Y") {
            $arFields["ACTIVE"] = "N";
        }
        if (is_set($arFields, "SEARCHABLE") && $arFields["SEARCHABLE"] != "Y") {
            $arFields["SEARCHABLE"] = "N";
        }
        if (is_set($arFields, "FILTRABLE") && $arFields["FILTRABLE"] != "Y") {
            $arFields["FILTRABLE"] = "N";
        }
        if (is_set($arFields, "MULTIPLE") && $arFields["MULTIPLE"] != "Y") {
            $arFields["MULTIPLE"] = "N";
        }
        if (is_set($arFields, "LIST_TYPE") && $arFields["LIST_TYPE"] != "C") {
            $arFields["LIST_TYPE"] = "L";
        }

        if (!$this->CheckFields($arFields, $ID)) {
            $Result = false;
            $arFields["RESULT_MESSAGE"] = &$this->LAST_ERROR;
        } elseif (!$this->_Update($ID, $arFields, $bCheckDescription)) {
            $Result = false;
            $arFields["RESULT_MESSAGE"] = &$this->LAST_ERROR;
        } else {
            if (isset($arFields["USER_TYPE"])) {
                $arUserType = CIBlockProperty::GetUserType($arFields["USER_TYPE"]);
                if (array_key_exists("ConvertToDB", $arUserType)) {
                    $arValue = array(
                        "VALUE" => $arFields["DEFAULT_VALUE"],
                        "DEFAULT_VALUE" => true
                    );
                    $arValue = call_user_func_array($arUserType["ConvertToDB"], array($arFields, $arValue));
                    if (is_array($arValue) && isset($arValue["VALUE"]) && mb_strlen($arValue["VALUE"])) {
                        $arFields["DEFAULT_VALUE"] = $arValue["VALUE"];
                    } else {
                        $arFields["DEFAULT_VALUE"] = false;
                    }
                }

                if (array_key_exists("PrepareSettings", $arUserType)) {
                    if (!isset($arFields["USER_TYPE_SETTINGS"])) {
                        $oldData = Iblock\PropertyTable::getList(
                            array(
                                'select' => array('ID', 'PROPERTY_TYPE', 'USER_TYPE', 'USER_TYPE_SETTINGS'),
                                'filter' => array('=ID' => $ID)
                            )
                        )->fetch();
                        if (!empty($oldData) && is_array($oldData)) {
                            if ($arFields["USER_TYPE"] == $oldData["USER_TYPE"] && !empty($oldData["USER_TYPE_SETTINGS"])) {
                                $arFields["USER_TYPE_SETTINGS"] = (
                                is_array($oldData["USER_TYPE_SETTINGS"])
                                    ? $oldData["USER_TYPE_SETTINGS"]
                                    : unserialize($oldData["USER_TYPE_SETTINGS"], ['allowed_classes' => false])
                                );
                            }
                        }
                        unset($oldData);
                    }
                    $arFieldsResult = call_user_func_array($arUserType["PrepareSettings"], array($arFields));
                    if (is_array($arFieldsResult) && array_key_exists('USER_TYPE_SETTINGS', $arFieldsResult)) {
                        $arFields = array_merge($arFields, $arFieldsResult);
                        $arFields["USER_TYPE_SETTINGS"] = serialize($arFields["USER_TYPE_SETTINGS"]);
                    } else {
                        $arFields["USER_TYPE_SETTINGS"] = serialize($arFieldsResult);
                    }
                } else {
                    $arFields["USER_TYPE_SETTINGS"] = false;
                }
            }

            unset($arFields["ID"]);
            unset($arFields["VERSION"]);
            unset($arFields["TIMESTAMP_X"]);

            $strUpdate = $DB->PrepareUpdate("b_iblock_property", $arFields);
            if ($strUpdate <> '') {
                $strSql = "UPDATE b_iblock_property SET " . $strUpdate . " WHERE ID=" . $ID;
                $DB->QueryBind($strSql, array("USER_TYPE_SETTINGS" => $arFields["USER_TYPE_SETTINGS"]));
            }

            if (is_set($arFields, "VALUES")) {
                $this->UpdateEnum($ID, $arFields["VALUES"]);
            }

            if (
                array_key_exists("IBLOCK_ID", $arFields)
                && CIBlock::GetArrayByID($arFields["IBLOCK_ID"], "SECTION_PROPERTY") === "Y"
            ) {
                if (
                    !array_key_exists("SECTION_PROPERTY", $arFields)
                    || $arFields["SECTION_PROPERTY"] !== "N"
                ) {
                    $arLink = array(
                        "SMART_FILTER" => $arFields["SMART_FILTER"],
                    );
                    if (array_key_exists("DISPLAY_TYPE", $arFields)) {
                        $arLink["DISPLAY_TYPE"] = $arFields["DISPLAY_TYPE"];
                    }
                    if (array_key_exists("DISPLAY_EXPANDED", $arFields)) {
                        $arLink["DISPLAY_EXPANDED"] = $arFields["DISPLAY_EXPANDED"];
                    }
                    if (array_key_exists("FILTER_HINT", $arFields)) {
                        $arLink["FILTER_HINT"] = $arFields["FILTER_HINT"];
                    }
                    CIBlockSectionPropertyLink::Set(0, $ID, $arLink);
                } else {
                    CIBlockSectionPropertyLink::Delete(0, $ID);
                }
            }

            if (!empty($arFields['FEATURES']) && is_array($arFields['FEATURES'])) {
                $featureResult = Iblock\Model\PropertyFeature::setFeatures(
                    $ID,
                    $arFields['FEATURES']
                );
                //TODO: add error handling
                unset($featureResult);
            }

            Iblock\PropertyTable::getEntity()->cleanCache();

            global $BX_IBLOCK_PROP_CACHE;
            if (isset($arFields["IBLOCK_ID"])) {
                unset($BX_IBLOCK_PROP_CACHE[$arFields["IBLOCK_ID"]]);
                CIBlock::clearIblockTagCache($arFields["IBLOCK_ID"]);
            }

            $Result = true;
        }

        $arFields["ID"] = $ID;
        $arFields["RESULT"] = &$Result;

        foreach (GetModuleEvents("iblock", "OnAfterIBlockPropertyUpdate", true) as $arEvent) {
            ExecuteModuleEventEx($arEvent, array(&$arFields));
        }

        return $Result;
    }

    ///////////////////////////////////////////////////////////////////
    // Get property information by ID
    ///////////////////////////////////////////////////////////////////
    public static function GetByID($ID, $IBLOCK_ID = false, $IBLOCK_CODE = false)
    {
        global $DB;

        if ($IBLOCK_CODE && $IBLOCK_ID) {
            $cond = " AND (B.ID = " . (int)$IBLOCK_ID . " OR B.CODE = '" . $DB->ForSql($IBLOCK_CODE) . "') ";
        } elseif ($IBLOCK_CODE) {
            $cond = " AND B.CODE = '" . $DB->ForSql($IBLOCK_CODE) . "' ";
        } elseif ($IBLOCK_ID) {
            $cond = " AND B.ID = " . (int)$IBLOCK_ID . " ";
        } else {
            $cond = "";
        }

        $strSql =
            "SELECT BP.* " .
            "FROM b_iblock_property BP, b_iblock B " .
            "WHERE BP.IBLOCK_ID=B.ID " .
            $cond .
            (is_numeric(mb_substr($ID, 0, 1))
                ?
                "	AND BP.ID=" . (int)$ID
                :
                "	AND UPPER(BP.CODE)=UPPER('" . $DB->ForSql($ID) . "') "
            );

        $res = new CIBlockPropertyResult($DB->Query($strSql));
        return $res;
    }

    public static function GetPropertyArray($ID, $IBLOCK_ID, $bCached = true)
    {
        global $DB;

        $block_id = false;
        $block_code = false;
        if (is_array($IBLOCK_ID)) {
            foreach ($IBLOCK_ID as $k => $v) {
                if (is_numeric($v)) {
                    if ($block_id) {
                        $block_id .= ", ";
                    } else {
                        $block_id = "";
                    }

                    $block_id .= intval($v);
                } elseif ($v <> '') {
                    if ($block_code) {
                        $block_code .= ", ";
                    } else {
                        $block_code = "";
                    }

                    $block_code .= "'" . $DB->ForSQL($v, 200) . "'";
                }
            }
        } elseif (is_numeric($IBLOCK_ID)) {
            $block_id = intval($IBLOCK_ID);
        } elseif ($IBLOCK_ID <> '') {
            $block_code = "'" . $DB->ForSQL($IBLOCK_ID, 200) . "'";
        }

        global $IBLOCK_CACHE_PROPERTY;
        if ($bCached && is_set($IBLOCK_CACHE_PROPERTY, $ID . "|" . $block_id . "|" . $block_code)) {
            return $IBLOCK_CACHE_PROPERTY[$ID . "|" . $block_id . "|" . $block_code];
        }

        if ($block_code && $block_id) {
            $cond = " AND (B.ID IN (" . $block_id . ") OR B.CODE IN (" . $block_code . ")) ";
        } elseif ($block_code) {
            $cond = " AND B.CODE IN (" . $block_code . ") ";
        } elseif ($block_id) {
            $cond = " AND B.ID IN (" . $block_id . ") ";
        } else {
            $cond = "";
        }

        $upperID = mb_strtoupper($ID);

        $strSql = "
			SELECT BP.*
			FROM
				b_iblock_property BP
				,b_iblock B
			WHERE BP.IBLOCK_ID=B.ID
			" . $cond . "
			" . (mb_substr($upperID, -6) == '_VALUE' ?
                (is_numeric(mb_substr($ID, 0, 1)) ?
                    "AND BP.ID=" . intval($ID)
                    :
                    "AND ((UPPER(BP.CODE)='" . $DB->ForSql(
                        $upperID
                    ) . "' AND BP.PROPERTY_TYPE!='L') OR (UPPER(BP.CODE)='" . $DB->ForSql(
                        mb_substr($upperID, 0, -6)
                    ) . "' AND BP.PROPERTY_TYPE='L'))"
                )
                :
                (is_numeric(mb_substr($ID, 0, 1)) ?
                    "AND BP.ID=" . intval($ID)
                    :
                    "AND UPPER(BP.CODE)='" . $DB->ForSql($upperID) . "'"
                )
            );

        $res = $DB->Query($strSql);
        if ($arr = $res->Fetch()) {
            $arr["ORIG_ID"] = $arr["ID"];    //it saves original (digital) id
            $arr["IS_CODE_UNIQUE"] = true;   //boolean check for global code uniquess
            $arr["IS_VERSION_MIXED"] = false;//boolean check if varios versions of ibformation block properties
            while ($arr2 = $res->Fetch()) {
                $arr["IS_CODE_UNIQUE"] = false;
                if ($arr["VERSION"] != $arr2["VERSION"]) {
                    $arr["IS_VERSION_MIXED"] = true;
                }
            }

            if (
                mb_substr($upperID, -6) == '_VALUE'
                && $arr["PROPERTY_TYPE"] == "L"
                && mb_strtoupper($arr["CODE"]) == mb_substr($upperID, 0, -6)
            ) {
                $arr["ID"] = mb_substr($ID, 0, -6);
            } else {
                $arr["ID"] = $ID;
            }
        }

        $IBLOCK_CACHE_PROPERTY[$ID . "|" . $block_id . "|" . $block_code] = $arr;
        return $arr;
    }

    public static function GetPropertyEnum($PROP_ID, $arOrder = array("SORT" => "asc"), $arFilter = array())
    {
        global $DB;

        $strSqlSearch = "";
        if (is_array($arFilter)) {
            foreach ($arFilter as $key => $val) {
                $key = mb_strtoupper($key);
                switch ($key) {
                    case "ID":
                        $strSqlSearch .= "AND (BPE.ID=" . intval($val) . ")\n";
                        break;
                    case "IBLOCK_ID":
                        $strSqlSearch .= "AND (BP.IBLOCK_ID=" . intval($val) . ")\n";
                        break;
                    case "VALUE":
                        $strSqlSearch .= "AND (BPE.VALUE LIKE '" . $DB->ForSql($val) . "')\n";
                        break;
                    case "EXTERNAL_ID":
                    case "XML_ID":
                        $strSqlSearch .= "AND (BPE.XML_ID LIKE '" . $DB->ForSql($val) . "')\n";
                        break;
                }
            }
        }

        $arSqlOrder = array();
        if (is_array($arOrder)) {
            foreach ($arOrder as $by => $order) {
                $by = mb_strtolower($by);
                $order = mb_strtolower($order);
                if ($order != "asc") {
                    $order = "desc";
                }

                if ($by == "value") {
                    $arSqlOrder["BPE.VALUE"] = "BPE.VALUE " . $order;
                } elseif ($by == "id") {
                    $arSqlOrder["BPE.ID"] = "BPE.ID " . $order;
                } elseif ($by == "external_id") {
                    $arSqlOrder["BPE.XML_ID"] = "BPE.XML_ID " . $order;
                } elseif ($by == "xml_id") {
                    $arSqlOrder["BPE.XML_ID"] = "BPE.XML_ID " . $order;
                } else {
                    $arSqlOrder["BPE.SORT"] = "BPE.SORT " . $order;
                }
            }
        }

        if (empty($arSqlOrder)) {
            $strSqlOrder = "";
        } else {
            $strSqlOrder = " ORDER BY " . implode(", ", $arSqlOrder);
        }

        $res = $DB->Query(
            $s = "
			SELECT BPE.*, BPE.XML_ID as EXTERNAL_ID
			FROM
				b_iblock_property_enum BPE
				INNER JOIN b_iblock_property BP ON BP.ID = BPE.PROPERTY_ID
			WHERE
			" . (
                is_numeric(mb_substr($PROP_ID, 0, 1)) ?
                    "BP.ID = " . intval($PROP_ID) :
                    "BP.CODE = '" . $DB->ForSql($PROP_ID) . "'"
                ) . "
			" . $strSqlSearch . "
			" . $strSqlOrder . "
		"
        );

        return $res;
    }

    function UpdateEnum($ID, $arVALUES, $bForceDelete = true)
    {
        global $DB, $CACHE_MANAGER;
        $ID = intval($ID);

        if (!is_array($arVALUES) || (empty($arVALUES) && $bForceDelete)) {
            CIBlockPropertyEnum::DeleteByPropertyID($ID);
            return true;
        }

        $ar_XML_ID = array();
        $db_res = $this->GetPropertyEnum($ID);
        while ($res = $db_res->Fetch()) {
            $ar_XML_ID[rtrim($res["XML_ID"], " ")] = $res["ID"];
        }

        $sqlWhere = "";
        if (!$bForceDelete) {
            $rsProp = CIBlockProperty::GetByID($ID);
            if ($arProp = $rsProp->Fetch()) {
                if ($arProp["VERSION"] == 1) {
                    $sqlWhere = "AND NOT EXISTS (
						SELECT *
						FROM b_iblock_element_property
						WHERE b_iblock_element_property.IBLOCK_PROPERTY_ID = b_iblock_property_enum.PROPERTY_ID
						AND b_iblock_element_property.VALUE_ENUM = b_iblock_property_enum.ID
					)";
                } elseif ($arProp["MULTIPLE"] == "N") {
                    $sqlWhere = "AND NOT EXISTS (
						SELECT *
						FROM b_iblock_element_prop_s" . $arProp["IBLOCK_ID"] . "
						WHERE b_iblock_element_prop_s" . $arProp["IBLOCK_ID"] . ".PROPERTY_" . $arProp["ID"] . " = b_iblock_property_enum.ID
					)";
                } else {
                    $sqlWhere = "AND NOT EXISTS (
						SELECT *
						FROM b_iblock_element_prop_m" . $arProp["IBLOCK_ID"] . "
						WHERE b_iblock_element_prop_m" . $arProp["IBLOCK_ID"] . ".IBLOCK_PROPERTY_ID = b_iblock_property_enum.PROPERTY_ID
						AND b_iblock_element_prop_m" . $arProp["IBLOCK_ID"] . ".VALUE_ENUM = b_iblock_property_enum.ID
					)";
                }
            }
        }

        $db_res = $this->GetPropertyEnum($ID);
        while ($res = $db_res->Fetch()) {
            $VALUE = $arVALUES[$res["ID"]];
            $VAL = is_array($VALUE) ? $VALUE["VALUE"] : $VALUE;
            UnSet($arVALUES[$res["ID"]]);

            if ((string)$VAL == '') {
                unset($ar_XML_ID[rtrim($res["XML_ID"], " ")]);

                $strSql = "
					DELETE FROM b_iblock_property_enum
					WHERE ID=" . $res["ID"] . "
					" . $sqlWhere . "
				";

                $DB->Query($strSql);
            } else {
                $DEF = "";
                $SORT = 0;
                $XML_ID = "";
                if (is_array($VALUE)) {
                    if (array_key_exists("DEF", $VALUE)) {
                        $DEF = $VALUE["DEF"] == "Y" ? "Y" : "N";
                    }

                    if (array_key_exists("SORT", $VALUE)) {
                        $SORT = intval($VALUE["SORT"]);
                    }
                    if ($SORT < 0) {
                        $SORT = 0;
                    }

                    if (array_key_exists("XML_ID", $VALUE) && mb_strlen($VALUE["XML_ID"])) {
                        $XML_ID = mb_substr(rtrim($VALUE["XML_ID"], " "), 0, 200);
                    } elseif (array_key_exists("EXTERNAL_ID", $VALUE) && mb_strlen($VALUE["EXTERNAL_ID"])) {
                        $XML_ID = mb_substr(rtrim($VALUE["EXTERNAL_ID"], " "), 0, 200);
                    }
                }

                if ($XML_ID) {
                    unset($ar_XML_ID[mb_strtolower(rtrim($res["XML_ID"], " "))]);
                    if (isset($ar_XML_ID[mb_strtolower($XML_ID)])) {
                        $XML_ID = md5(uniqid(""));
                    }
                    $ar_XML_ID[mb_strtolower($XML_ID)] = $res["ID"];
                }

                $strSql = "
					UPDATE b_iblock_property_enum
					SET
						" . ($DEF ? " DEF = '" . $DEF . "', " : "") . "
						" . ($SORT ? " SORT = " . $SORT . ", " : "") . "
						" . ($XML_ID ? " XML_ID = '" . $DB->ForSQL($XML_ID, 200) . "', " : "") . "
						VALUE = '" . $DB->ForSQL($VAL, 255) . "'
					WHERE
						ID = " . $res["ID"] . "
				";

                $DB->Query($strSql);
            }
        }

        foreach ($arVALUES as $id => $VALUE) {
            $VAL = is_array($VALUE) ? $VALUE["VALUE"] : $VALUE;
            if ((string)$id <> '' && (string)$VAL <> '') {
                $DEF = "";
                $SORT = 0;
                $XML_ID = "";
                if (is_array($VALUE)) {
                    if (array_key_exists("DEF", $VALUE)) {
                        $DEF = $VALUE["DEF"] == "Y" ? "Y" : "N";
                    }

                    if (array_key_exists("SORT", $VALUE)) {
                        $SORT = intval($VALUE["SORT"]);
                    }
                    if ($SORT < 0) {
                        $SORT = 0;
                    }

                    if (array_key_exists("XML_ID", $VALUE) && mb_strlen($VALUE["XML_ID"])) {
                        $XML_ID = mb_substr(rtrim($VALUE["XML_ID"], " "), 0, 200);
                    } elseif (array_key_exists("EXTERNAL_ID", $VALUE) && mb_strlen($VALUE["EXTERNAL_ID"])) {
                        $XML_ID = mb_substr(rtrim($VALUE["EXTERNAL_ID"], " "), 0, 200);
                    }
                }

                if ($XML_ID) {
                    if (isset($ar_XML_ID[mb_strtolower($XML_ID)])) {
                        $XML_ID = md5(uniqid("", true));
                    }
                } else {
                    $XML_ID = md5(uniqid("", true));
                }
                $ar_XML_ID[mb_strtolower($XML_ID)] = 0;

                $strSql = "
					INSERT INTO b_iblock_property_enum
					(
						PROPERTY_ID
						" . ($DEF ? ",DEF" : "") . "
						" . ($SORT ? ",SORT" : "") . "
						,VALUE
						,XML_ID
					) VALUES (
						" . $ID . "
						" . ($DEF ? ",'" . $DEF . "'" : "") . "
						" . ($SORT ? "," . $SORT . "" : "") . "
						,'" . $DB->ForSQL($VAL, 255) . "'
						,'" . $DB->ForSQL($XML_ID, 200) . "'
					)
				";
                $DB->Query($strSql);
            }
        }

        if (CACHED_b_iblock_property_enum !== false) {
            $CACHE_MANAGER->CleanDir("b_iblock_property_enum");
        }

        if (defined("BX_COMP_MANAGED_CACHE")) {
            $CACHE_MANAGER->ClearByTag("iblock_property_enum_" . $ID);
        }

        return true;
    }

    public static function GetUserType($USER_TYPE = false)
    {
        static $CACHE = null;

        if (!isset($CACHE)) {
            $CACHE = array();
            foreach (GetModuleEvents("iblock", "OnIBlockPropertyBuildList", true) as $arEvent) {
                $res = ExecuteModuleEventEx($arEvent);
                if (is_array($res) && array_key_exists("USER_TYPE", $res)) {
                    $CACHE[$res["USER_TYPE"]] = $res;
                }
            }
        }

        if ($USER_TYPE !== false) {
            if (array_key_exists($USER_TYPE, $CACHE)) {
                return $CACHE[$USER_TYPE];
            } else {
                return array();
            }
        } else {
            return $CACHE;
        }
    }

    function FormatUpdateError($ID, $CODE)
    {
        return GetMessage("IBLOCK_PROPERTY_CHANGE_ERROR", array("#ID#" => $ID, "#CODE#" => $CODE));
    }

    function FormatNotFoundError($ID)
    {
        return GetMessage("IBLOCK_PROPERTY_NOT_FOUND", array("#ID#" => $ID));
    }

    /**
     * @return array
     * @see \CIBlockPropertyDateTime::GetUserTypeDescription()
     *
     * @deprecated deprecated since iblock 17.0.9
     */
    public static function _DateTime_GetUserTypeDescription()
    {
        return CIBlockPropertyDateTime::GetUserTypeDescription();
    }

    /**
     * @return array
     * @see \CIBlockPropertyDate::GetUserTypeDescription()
     *
     * @deprecated deprecated since iblock 17.0.9
     */
    public static function _Date_GetUserTypeDescription()
    {
        return CIBlockPropertyDate::GetUserTypeDescription();
    }

    /**
     * @return array
     * @see \CIBlockPropertyXmlID::GetUserTypeDescription()
     *
     * @deprecated deprecated since iblock 17.0.9
     */
    public static function _XmlID_GetUserTypeDescription()
    {
        return CIBlockPropertyXmlID::GetUserTypeDescription();
    }

    /**
     * @return array
     * @see \CIBlockPropertyFileMan::GetUserTypeDescription()
     *
     * @deprecated deprecated since iblock 17.0.9
     */
    public static function _FileMan_GetUserTypeDescription()
    {
        return CIBlockPropertyFileMan::GetUserTypeDescription();
    }

    /**
     * @return array
     * @see \CIBlockPropertyHTML::GetUserTypeDescription()
     *
     * @deprecated deprecated since iblock 17.0.9
     */
    public static function _HTML_GetUserTypeDescription()
    {
        return CIBlockPropertyHTML::GetUserTypeDescription();
    }

    /**
     * @return array
     * @see \CIBlockPropertyElementList::GetUserTypeDescription()
     *
     * @deprecated deprecated since iblock 17.0.9
     */
    public static function _ElementList_GetUserTypeDescription()
    {
        return CIBlockPropertyElementList::GetUserTypeDescription();
    }

    /**
     * @return array
     * @see \CIBlockPropertySequence::GetUserTypeDescription()
     *
     * @deprecated deprecated since iblock 17.0.9
     */
    public static function _Sequence_GetUserTypeDescription()
    {
        return CIBlockPropertySequence::GetUserTypeDescription();
    }

    /**
     * @return array
     * @see \CIBlockPropertyElementAutoComplete::GetUserTypeDescription()
     *
     * @deprecated deprecated since iblock 17.0.9
     */
    public static function _ElementAutoComplete_GetUserTypeDescription()
    {
        return CIBlockPropertyElementAutoComplete::GetUserTypeDescription();
    }

    /**
     * @return array
     * @see \CIBlockPropertySKU::GetUserTypeDescription()
     *
     * @deprecated deprecated since iblock 17.0.9
     */
    public static function _SKU_GetUserTypeDescription()
    {
        return CIBlockPropertySKU::GetUserTypeDescription();
    }

    /**
     * @return array
     * @see \CIBlockPropertySectionAutoComplete::GetUserTypeDescription()
     *
     * @deprecated deprecated since iblock 17.0.9
     */
    public static function _SectionAutoComplete_GetUserTypeDescription()
    {
        return CIBlockPropertySectionAutoComplete::GetUserTypeDescription();
    }

    function _Update($ID, $arFields, $bCheckDescription = false)
    {
        return false;
    }

    public static function DropColumnSQL($strTable, $arColumns)
    {
        return array();
    }

    function _Add($ID, $arFields)
    {
        return false;
    }
}