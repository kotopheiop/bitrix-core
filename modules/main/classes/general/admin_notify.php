<?

IncludeModuleLangFile(__FILE__);

class CAdminNotify
{
    const TYPE_NORMAL = 'M';
    const TYPE_ERROR = 'E';

    protected static function CleanCache()
    {
        global $CACHE_MANAGER;

        $rsLangs = CLanguage::GetList('lid', 'asc');
        while ($arLang = $rsLangs->Fetch()) {
            $CACHE_MANAGER->Clean("admin_notify_list_" . $arLang['LANGUAGE_ID']);
        }
        $CACHE_MANAGER->Clean("admin_notify_list");
    }

    public static function Add($arFields)
    {
        global $DB;
        $err_mess = (self::err_mess()) . '<br />Function: Add<br />Line: ';

        if (!self::CheckFields($arFields)) {
            return false;
        }

        if (!is_set($arFields['ENABLE_CLOSE'])) {
            $arFields['ENABLE_CLOSE'] = 'Y';
        }

        if (is_set($arFields['TAG']) && trim($arFields['TAG']) <> '') {
            $arFields['TAG'] = trim($arFields['TAG']);
            self::DeleteByTag($arFields['TAG']);
        } else {
            $arFields['TAG'] = "";
        }

        $arFields['PUBLIC_SECTION'] = (isset($arFields['PUBLIC_SECTION']) && $arFields['PUBLIC_SECTION'] == 'Y' ? 'Y' : 'N');
        if (!isset($arFields['NOTIFY_TYPE']) || !in_array(
                $arFields['NOTIFY_TYPE'],
                array(self::TYPE_NORMAL, self::TYPE_ERROR)
            )) {
            $arFields['NOTIFY_TYPE'] = self::TYPE_NORMAL;
        }

        $arFields_i = array(
            'MODULE_ID' => is_set($arFields['MODULE_ID']) ? trim($arFields['MODULE_ID']) : "",
            'TAG' => $arFields['TAG'],
            'MESSAGE' => trim($arFields['MESSAGE']),
            'ENABLE_CLOSE' => $arFields['ENABLE_CLOSE'],
            'PUBLIC_SECTION' => $arFields['PUBLIC_SECTION'],
            'NOTIFY_TYPE' => $arFields['NOTIFY_TYPE']
        );
        $ID = $DB->Add('b_admin_notify', $arFields_i, array('MESSAGE'));

        if ($ID) {
            if (isset($arFields['LANG']) && !empty($arFields['LANG']) && is_array($arFields['LANG'])) {
                foreach ($arFields['LANG'] as $strLang => $strMess) {
                    $arFields_l = array(
                        'NOTIFY_ID' => $ID,
                        'LID' => $strLang,
                        'MESSAGE' => trim($strMess)
                    );
                    $intLangID = $DB->Add('b_admin_notify_lang', $arFields_l, array('MESSAGE'));
                }
            }
        }

        self::CleanCache();
        return $ID;
    }

    private static function CheckFields($arFields)
    {
        $aMsg = array();

        if (is_set($arFields, 'MODULE_ID') && trim($arFields['MODULE_ID']) == '') {
            $aMsg[] = array('id' => 'MODULE_ID', 'text' => GetMessage('MAIN_AN_ERROR_MODULE_ID'));
        }
        if (is_set($arFields, 'TAG') && trim($arFields['TAG']) == '') {
            $aMsg[] = array('id' => 'TAG', 'text' => GetMessage('MAIN_AN_ERROR_TAG'));
        }
        if (!is_set($arFields, 'MESSAGE') || trim($arFields['MESSAGE']) == '') {
            $aMsg[] = array('id' => 'MESSAGE', 'text' => GetMessage('MAIN_AN_ERROR_MESSAGE'));
        }
        if (is_set(
                $arFields,
                'ENABLE_CLOSE'
            ) && !($arFields['ENABLE_CLOSE'] == 'Y' || $arFields['ENABLE_CLOSE'] == 'N')) {
            $aMsg[] = array('id' => 'ENABLE_CLOSE', 'text' => GetMessage('MAIN_AN_ERROR_ENABLE_CLOSE'));
        }

        if (!empty($aMsg)) {
            $e = new CAdminException($aMsg);
            $GLOBALS['APPLICATION']->ThrowException($e);
            return false;
        }

        return true;
    }

    public static function Delete($ID)
    {
        global $DB;
        $err_mess = (self::err_mess()) . '<br />Function: Delete<br />Line: ';
        $ID = (int)$ID;
        if ($ID <= 0) {
            return false;
        }

        $strSql = "DELETE FROM b_admin_notify_lang WHERE NOTIFY_ID = " . $ID;
        $DB->Query($strSql, false, $err_mess . __LINE__);

        $strSql = "DELETE FROM b_admin_notify WHERE ID = " . $ID;
        $DB->Query($strSql, false, $err_mess . __LINE__);

        self::CleanCache();
        return true;
    }

    public static function DeleteByModule($moduleId)
    {
        global $DB;
        $err_mess = (self::err_mess()) . '<br />Function: DeleteByModule<br />Line: ';

        $strSql = "DELETE FROM b_admin_notify_lang WHERE NOTIFY_ID IN (SELECT ID FROM b_admin_notify WHERE MODULE_ID = '" . $DB->ForSQL(
                $moduleId
            ) . "')";
        $DB->Query($strSql, false, $err_mess . __LINE__);

        $strSql = "DELETE FROM b_admin_notify WHERE MODULE_ID = '" . $DB->ForSQL($moduleId) . "'";
        $DB->Query($strSql, false, $err_mess . __LINE__);

        self::CleanCache();
        return true;
    }

    public static function DeleteByTag($tagId)
    {
        global $DB;
        $err_mess = (self::err_mess()) . '<br />Function: DeleteByTag<br />Line: ';

        $tagId = (string)$tagId;
        if ($tagId == '') {
            return false;
        }

        $strSql = "DELETE FROM b_admin_notify_lang WHERE NOTIFY_ID IN (SELECT ID FROM b_admin_notify WHERE TAG like '%" . $DB->ForSQL(
                $tagId
            ) . "%')";
        $DB->Query($strSql, false, $err_mess . __LINE__);

        $strSql = "DELETE FROM b_admin_notify WHERE TAG like '%" . $DB->ForSQL($tagId) . "%'";
        $DB->Query($strSql, false, $err_mess . __LINE__);

        self::CleanCache();
        return true;
    }

    public static function GetHtml()
    {
        global $CACHE_MANAGER;
        $arNotify = false;

        if ($CACHE_MANAGER->Read(86400, "admin_notify_list_" . LANGUAGE_ID)) {
            $arNotify = $CACHE_MANAGER->Get("admin_notify_list_" . LANGUAGE_ID);
        }

        if ($arNotify === false) {
            $arNotify = Array();
            $CBXSanitizer = new CBXSanitizer;
            $CBXSanitizer->AddTags(
                array(
                    'a' => array('href', 'style'),
                    'b' => array(),
                    'u' => array(),
                    'i' => array(),
                    'br' => array(),
                    'span' => array('style'),
                )
            );
            $dbRes = self::GetList();
            while ($ar = $dbRes->Fetch()) {
                $ar["MESSAGE"] = $CBXSanitizer->SanitizeHtml(
                    ('' != $ar['MESSAGE_LANG'] ? $ar['MESSAGE_LANG'] : $ar['MESSAGE'])
                );
                $arNotify[] = $ar;
            }
            $CACHE_MANAGER->Set("admin_notify_list_" . LANGUAGE_ID, $arNotify);
        }

        $html = "";
        foreach ($arNotify as $value) {
            $className = ($value['NOTIFY_TYPE'] == self::TYPE_ERROR ? 'adm-warning-block adm-warning-block-red' : 'adm-warning-block');
            $html .= '<div class="' . $className . '" data-id="' . (int)$value['ID'] . '" data-ajax="Y"><span class="adm-warning-text">' . $value['MESSAGE'] . '</span><span class="adm-warning-icon"></span>' . ($value['ENABLE_CLOSE'] == 'Y' ? '<span onclick="BX.adminPanel ? BX.adminPanel.hideNotify(this.parentNode) : BX.admin.panel.hideNotify(this.parentNode);" class="adm-warning-close"></span>' : '') . '</div>';
        }

        return $html;
    }

    public static function GetList($arSort = array(), $arFilter = array())
    {
        global $DB;

        $arSqlSearch = Array();
        $strSqlSearch = '';
        $err_mess = (self::err_mess()) . '<br />Function: GetList<br />Line: ';

        if (!is_array($arFilter)) {
            $arFilter = array();
        }
        if (!isset($arFilter['LID'])) {
            $arFilter['LID'] = LANGUAGE_ID;
        }
        if (!isset($arFilter['PUBLIC_SECTION'])) {
            $arFilter['PUBLIC_SECTION'] = 'N';
        }

        $strFrom = '';
        $strSelect = "AN.*";

        if (is_array($arFilter)) {
            $filter_keys = array_keys($arFilter);
            for ($i = 0, $ic = count($filter_keys); $i < $ic; $i++) {
                $val = $arFilter[$filter_keys[$i]];
                if ((string)$val == '' || $val == 'NOT_REF') {
                    continue;
                }
                switch (mb_strtoupper($filter_keys[$i])) {
                    case 'ID':
                        $arSqlSearch[] = GetFilterQuery('AN.ID', $val, 'N');
                        break;
                    case 'MODULE_ID':
                        $arSqlSearch[] = GetFilterQuery('AN.MODULE_ID', $val);
                        break;
                    case 'TAG':
                        $arSqlSearch[] = GetFilterQuery('AN.TAG', $val);
                        break;
                    case 'MESSAGE':
                        $arSqlSearch[] = GetFilterQuery('AN.MESSAGE', $val);
                        break;
                    case 'ENABLE_CLOSE':
                        $arSqlSearch[] = ($val == 'Y') ? "AN.ENABLE_CLOSE='Y'" : "AN.ENABLE_CLOSE='N'";
                        break;
                    case 'LID':
                        $strSelect .= ", ANL.MESSAGE as MESSAGE_LANG";
                        $strFrom = 'LEFT JOIN b_admin_notify_lang ANL ON (AN.ID = ANL.NOTIFY_ID AND ANL.LID = \'' . $DB->ForSQL(
                                $val
                            ) . '\')';
                        break;
                    case 'PUBLIC_SECTION':
                        $arSqlSearch[] = ($val == 'Y') ? "AN.PUBLIC_SECTION='Y'" : "AN.PUBLIC_SECTION='N'";
                }
            }
        }

        $sOrder = '';
        foreach ($arSort as $key => $val) {
            $ord = (mb_strtoupper($val) <> 'ASC' ? 'DESC' : 'ASC');
            switch (mb_strtoupper($key)) {
                case 'ID':
                    $sOrder .= ', AN.ID ' . $ord;
                    break;
                case 'MODULE_ID':
                    $sOrder .= ', AN.MODULE_ID ' . $ord;
                    break;
                case 'ENABLE_CLOSE':
                    $sOrder .= ', AN.ENABLE_CLOSE ' . $ord;
                    break;
            }
        }

        if ($sOrder == '') {
            $sOrder = 'AN.ID DESC';
        }

        $strSqlOrder = ' ORDER BY ' . TrimEx($sOrder, ',');
        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);

        $strSql = "SELECT " . $strSelect . " FROM b_admin_notify AN " . $strFrom . " WHERE " . $strSqlSearch . " " . $strSqlOrder;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);

        return $res;
    }

    private static function err_mess()
    {
        return '<br />Class: CAdminNotify<br />File: ' . __FILE__;
    }
}
