<?

use Bitrix\Sale\Location;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$selfFolderUrl = $adminPage->getSelfFolderUrl();
$listUrl = $selfFolderUrl . "sale_tax_rate.php?lang=" . LANGUAGE_ID;
$listUrl = $adminSidePanelHelper->editUrlToPublicPage($listUrl);

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions < "W") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

IncludeModuleLangFile(__FILE__);

\Bitrix\Main\Loader::includeModule('sale');

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/prolog.php");

$ID = intval($ID);

ClearVars();
ClearVars("fp_");

$strError = "";
$bInitVars = false;

$lpEnabled = CSaleLocation::isLocationProEnabled();

if (($save <> '' || $apply <> '') && $REQUEST_METHOD == "POST" && $saleModulePermissions == "W" && check_bitrix_sessid(
    )) {
    $adminSidePanelHelper->decodeUriComponent();
    $TAX_ID = intval($TAX_ID);
    if ($TAX_ID <= 0) {
        $strError .= GetMessage("ERROR_NO_TAX_ID") . "<br>";
    }

    $VALUE = str_replace(",", ".", $VALUE);
    $VALUE = DoubleVal($VALUE);
    if ($VALUE <= 0) {
        $strError .= GetMessage("ERROR_NO_VALUE") . "<br>";
    }

    if ($IS_IN_PRICE != "Y") {
        $IS_IN_PRICE = "N";
    }
    if ($ACTIVE != "Y") {
        $ACTIVE = "N";
    }

    $IS_PERCENT = "Y";
    if ($IS_PERCENT != "Y") {
        $IS_PERCENT = "N";
    }

    if ($IS_PERCENT != "Y" && $CURRENCY == '') {
        $strError .= GetMessage("ERROR_PERCENT_OR_CURRENCY") . "<br>";
    }

    $APPLY_ORDER = intval($APPLY_ORDER);
    if ($APPLY_ORDER <= 0) {
        $APPLY_ORDER = 100;
    }

    $arLocation = array();
    if ($lpEnabled) {
        if ($_REQUEST['LOCATION']['L'] <> '') {
            $LOCATION1 = explode(':', $_REQUEST['LOCATION']['L']);
        }

        if ($_REQUEST['LOCATION']['G'] <> '') {
            $LOCATION2 = explode(':', $_REQUEST['LOCATION']['G']);
        }
    }

    if (isset($LOCATION1) && is_array($LOCATION1) && count($LOCATION1) > 0) {
        $countLocation = count($LOCATION1);
        for ($i = 0; $i < $countLocation; $i++) {
            if ($LOCATION1[$i] <> '') {
                $arLocation[] = array(
                    "LOCATION_ID" => $LOCATION1[$i],
                    "LOCATION_TYPE" => "L"
                );
            }
        }
    }

    if (isset($LOCATION2) && is_array($LOCATION2) && count($LOCATION2) > 0) {
        $countLocation2 = count($LOCATION2);
        for ($i = 0; $i < $countLocation2; $i++) {
            if ($LOCATION2[$i] <> '') {
                $arLocation[] = array(
                    "LOCATION_ID" => $LOCATION2[$i],
                    "LOCATION_TYPE" => "G"
                );
            }
        }
    }

    if (!is_array($arLocation) || count($arLocation) <= 0) {
        $strError .= GetMessage("ERROR_NO_LOCATION") . "<br>";
    }

    if ($strError == '') {
        unset($arFields);
        $arFields = array(
            "PERSON_TYPE_ID" => (intval($PERSON_TYPE_ID) > 0) ? intval($PERSON_TYPE_ID) : false,
            "TAX_ID" => $TAX_ID,
            "VALUE" => $VALUE,
            "CURRENCY" => ($CURRENCY <> '') ? $CURRENCY : false,
            "IS_PERCENT" => $IS_PERCENT,
            "IS_IN_PRICE" => $IS_IN_PRICE,
            "APPLY_ORDER" => $APPLY_ORDER,
            "ACTIVE" => $ACTIVE,

            "TAX_LOCATION" => $arLocation
        );

        if ($ID > 0) {
            if (!CSaleTaxRate::Update($ID, $arFields, array("EXPECT_LOCATION_CODES" => $lpEnabled))) {
                $strError .= GetMessage("ERROR_EDIT_TAX_RATE") . "<br>";
            }
        } else {
            $ID = CSaleTaxRate::Add($arFields, array("EXPECT_LOCATION_CODES" => $lpEnabled));
            if ($ID <= 0) {
                $strError .= GetMessage("ERROR_ADD_TAX_RATE") . "<br>";
            }
        }
    }

    if ($strError <> '') {
        $adminSidePanelHelper->sendJsonErrorResponse($strError);
        $bInitVars = true;
    }

    $adminSidePanelHelper->sendSuccessResponse("base");

    if ($save <> '' && $strError == '') {
        $adminSidePanelHelper->localRedirect($listUrl);
        LocalRedirect($listUrl);
    }
}

if ($ID > 0) {
    $db_tax_rate = CSaleTaxRate::GetList(Array(), Array("ID" => $ID));
    $db_tax_rate->ExtractFields("str_");
} else {
    $str_APPLY_ORDER = 100;
    $str_ACTIVE = "Y";
    $str_IS_PERCENT = "Y";
    $str_IS_IN_PRICE = "N";
}

if ($bInitVars) {
    $DB->InitTableVarsForEdit("b_sale_tax_rate", "", "str_");
}

if ($ID > 0) {
    $sDocTitle = GetMessage("TAX_RATE_EDIT_RECORD", array("#ID#" => $ID));
} else {
    $sDocTitle = GetMessage("TAX_RATE_NEW_RECORD");
}
$APPLICATION->SetTitle($sDocTitle);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

/*********************************************************************/
/********************  BODY  *****************************************/
/*********************************************************************/
?>

<?
$aMenu = array(
    array(
        "TEXT" => GetMessage("STREN_2FLIST"),
        "ICON" => "btn_list",
        "LINK" => $listUrl
    )
);

if ($ID > 0 && $saleModulePermissions >= "W") {
    $aMenu[] = array("SEPARATOR" => "Y");
    $addUrl = $selfFolderUrl . "sale_tax_rate_edit.php?lang=" . LANGUAGE_ID;
    $addUrl = $adminSidePanelHelper->editUrlToPublicPage($addUrl);
    $aMenu[] = array(
        "TEXT" => GetMessage("STREN_NEW_RATE"),
        "ICON" => "btn_new",
        "LINK" => $addUrl
    );
    $deleteUrl = $selfFolderUrl . "sale_tax_rate.php?action=delete&ID[]=" . $ID . "&lang=" . LANGUAGE_ID . "&" . bitrix_sessid_get(
        ) . "#tb";
    $buttonAction = "LINK";
    if ($adminSidePanelHelper->isPublicFrame()) {
        $deleteUrl = $adminSidePanelHelper->editUrlToPublicPage($deleteUrl);
        $buttonAction = "ONCLICK";
    }
    $aMenu[] = array(
        "TEXT" => GetMessage("STREN_DELETE_RATE"),
        "ICON" => "btn_delete",
        $buttonAction => "javascript:if(confirm('" . GetMessage(
                "STREN_DELETE_RATE_CONFIRM"
            ) . "')) top.window.location.href='" . $deleteUrl . "';",
    );
}
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<? CAdminMessage::ShowMessage($strError); ?>
<?
$actionUrl = $APPLICATION->GetCurPage();
$actionUrl = $adminSidePanelHelper->setDefaultQueryParams($actionUrl);
?>
    <form method="POST" action="<?= $actionUrl ?>" name="form1">
        <? echo GetFilterHiddens("filter_"); ?>
        <input type="hidden" name="Update" value="Y">
        <input type="hidden" name="lang" value="<? echo LANG ?>">
        <input type="hidden" name="ID" value="<? echo $ID ?>">
        <?= bitrix_sessid_post() ?>

        <?
        $aTabs = array(
            array(
                "DIV" => "edit1",
                "TAB" => GetMessage("STREN_TAB_RATE"),
                "ICON" => "sale",
                "TITLE" => GetMessage("STREN_TAB_RATE_DESCR")
            )
        );

        $tabControl = new CAdminTabControl("tabControl", $aTabs);
        $tabControl->Begin();
        ?>

        <?
        $tabControl->BeginNextTab();
        ?>


        <? if ($ID > 0): ?>
            <tr>
                <td width="40%">
                    ID:
                </td>
                <td width="60%">
                    <b><? echo $ID ?></b>
                </td>
            </tr>
            <tr>
                <td width="40%">
                    <? echo GetMessage("TAX_TIMESTAMP") ?>:
                </td>
                <td width="60%">
                    <b><? echo $str_TIMESTAMP_X ?></b>
                </td>
            </tr>
        <? endif; ?>

        <tr class="adm-detail-required-field">
            <td width="40%"><? echo GetMessage("F_TAX_ID") ?>:</td>
            <td width="60%">
                <select name="TAX_ID">
                    <?
                    $db_TAX = CSaleTax::GetList(array("NAME" => "ASC"), array());
                    while ($db_TAX_arr = $db_TAX->NavNext(true, "fp_")) {
                        ?>
                        <option value="<? echo intval($fp_ID) ?>" <? if (intval($fp_ID) == intval($str_TAX_ID)) {
                            echo "selected";
                        } ?>><?= $fp_NAME ?> (<? echo $fp_LID ?>)</option><?
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="40%">
                <? echo GetMessage("RATE_ACTIVE"); ?>:
            </td>
            <td width="60%">
                <input type="checkbox" name="ACTIVE" value="Y" <? if ($str_ACTIVE == "Y") {
                    echo "checked";
                } ?>>
            </td>
        </tr>
        <tr>
            <td width="40%">
                <? echo GetMessage("SALE_F_PERSON_TYPE") ?>:
            </td>
            <td width="60%">
                <? echo CSalePersonType::SelectBox(
                    "PERSON_TYPE_ID",
                    $str_PERSON_TYPE_ID,
                    GetMessage("SALE_ANY"),
                    true,
                    "",
                    ""
                ) ?>

            </td>
        </tr>
        <tr class="adm-detail-required-field">
            <td width="40%"><? echo GetMessage("RATE_VALUE") ?>:</td>
            <td width="60%">
                <input type="text" name="VALUE" value="<? echo $str_VALUE ?>" size="10">
                <b>%</b>
                <input type="hidden" name="IS_PERCENT" value="Y">
            </td>
        </tr>
        <tr>
            <td width="40%">
                <? echo GetMessage("RATE_IS_INPRICE"); ?>:
            </td>
            <td width="60%">
                <select name="IS_IN_PRICE">
                    <option value="N" <? if ($str_IS_IN_PRICE == "N" || $str_IS_IN_PRICE == '') echo " selected" ?>><? echo GetMessage(
                            "RATE_NET"
                        ); ?></option>
                    <option value="Y" <? if ($str_IS_IN_PRICE == "Y") echo " selected" ?>><? echo GetMessage(
                            "RATE_YES"
                        ); ?></option>
                </select>
            </td>
        </tr>
        <tr class="adm-detail-required-field">
            <td width="40%"><? echo GetMessage("RATE_APPLY_ORDER"); ?>:</td>
            <td width="60%">
                <input type="text" name="APPLY_ORDER" value="<? echo $str_APPLY_ORDER ?>" size="10">
            </td>
        </tr>

        <? if ($lpEnabled): ?>

            <tr class="heading">
                <td colspan="2">
                    <?= GetMessage('F_LOCATION1') ?>
                </td>
            </tr>

            <tr class="adm-detail-required-field">
                <td colspan="2">

                    <? $APPLICATION->IncludeComponent(
                        "bitrix:sale.location.selector.system",
                        "",
                        array(
                            "ENTITY_PRIMARY" => $ID,
                            "LINK_ENTITY_NAME" => CSaleTaxRate::CONN_ENTITY_NAME,
                            "INPUT_NAME" => 'LOCATION',
                            "SELECTED_IN_REQUEST" => array(
                                'L' => isset($_REQUEST['LOCATION']['L']) ? explode(
                                    ':',
                                    $_REQUEST['LOCATION']['L']
                                ) : false,
                                'G' => isset($_REQUEST['LOCATION']['G']) ? explode(
                                    ':',
                                    $_REQUEST['LOCATION']['G']
                                ) : false
                            )
                        ),
                        false
                    ); ?>

                </td>
            </tr>

        <? else: ?>
            <tr class="adm-detail-required-field">
                <td width="40%" valign="top"><? echo GetMessage("F_LOCATION1"); ?>:</td>
                <td width="60%" valign="top">
                    <? $db_vars = CSaleLocation::GetList(
                        Array(
                            "SORT" => "ASC",
                            "COUNTRY_NAME_LANG" => "ASC",
                            "REGION_NAME_LANG" => "ASC",
                            "CITY_NAME_LANG" => "ASC"
                        ),
                        array("LID" => LANGUAGE_ID),
                        LANG
                    ) ?>

                    <? $db_location = CSaleTaxRate::GetLocationList(
                        Array("TAX_RATE_ID" => $ID, "LOCATION_TYPE" => "L")
                    ); ?>
                    <select name="LOCATION1[]" size="5" multiple>
                        <?
                        $arLOCATION1 = array();
                        if ($bInitVars) {
                            $arLOCATION1 = $LOCATION1;
                        } else {
                            while ($arLocation = $db_location->Fetch()) {
                                $arLOCATION1[] = $arLocation["LOCATION_ID"];
                            }
                        }
                        if (!is_array($arLOCATION1)) {
                            $arLOCATION1 = Array();
                        }
                        ?>
                        <? while ($vars = $db_vars->Fetch()): ?>
                            <option value="<? echo $vars["ID"] ?>"<? if (in_array(
                                intval($vars["ID"]),
                                $arLOCATION1
                            )) echo " selected" ?>><? echo htmlspecialcharsbx(
                                    $vars["COUNTRY_NAME_LANG"]
                                ) ?><? if ($vars["REGION_NAME_LANG"] <> '') echo " - " . htmlspecialcharsbx(
                                        $vars["REGION_NAME_LANG"]
                                    ) ?><? if ($vars["CITY_NAME_LANG"] <> '') echo " - " . htmlspecialcharsbx(
                                        $vars["CITY_NAME_LANG"]
                                    ) ?></option>
                        <? endwhile; ?>
                    </select>
                </td>
            </tr>
            <tr class="adm-detail-required-field">
                <td width="40%" valign="top"><? echo GetMessage("F_LOCATION2"); ?>:</td>
                <td width="60%" valign="top">
                    <select name="LOCATION2[]" size="5" multiple>
                        <? $db_vars = CSaleLocationGroup::GetList(Array("NAME" => "ASC"), array(), LANG) ?>
                        <?
                        $arLOCATION2 = array();
                        if ($bInitVars) {
                            $arLOCATION2 = $LOCATION2;
                        } else {
                            $db_location = CSaleTaxRate::GetLocationList(
                                Array("TAX_RATE_ID" => $ID, "LOCATION_TYPE" => "G")
                            );
                            while ($arLocation = $db_location->Fetch()) {
                                $arLOCATION2[] = $arLocation["LOCATION_ID"];
                            }
                        }
                        if (!is_array($arLOCATION2)) {
                            $arLOCATION2 = Array();
                        }
                        ?>
                        <? while ($vars = $db_vars->Fetch()): ?>
                            <option value="<? echo $vars["ID"] ?>"<? if (in_array(
                                intval($vars["ID"]),
                                $arLOCATION2
                            )) echo " selected" ?>><? echo htmlspecialcharsbx($vars["NAME"]) ?></option>
                        <? endwhile; ?>
                    </select>
                </td>
            </tr>
        <? endif ?>

        <?
        $tabControl->EndTab();
        $tabControl->Buttons(array("disabled" => ($saleModulePermissions < "W"), "back_url" => $listUrl));
        $tabControl->End();
        ?>

    </form>
<? require($DOCUMENT_ROOT . "/bitrix/modules/main/include/epilog_admin.php"); ?>