<?

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/search/prolog.php");
IncludeModuleLangFile(__FILE__);
/** @global CMain $APPLICATION */
global $APPLICATION;
/** @var CAdminMessage $message */
$searchDB = CDatabase::GetModuleConnection('search');

$SEARCH_RIGHT = $APPLICATION->GetGroupRight("search");
if ($SEARCH_RIGHT == "D") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$sTableID = "tbl_search_tags_list";
$oSort = new CAdminSorting($sTableID, "COUNT", "DESC");
$lAdmin = new CAdminList($sTableID, $oSort);

$ref = $ref_id = array();
$rs = CSite::GetList();
while ($ar = $rs->Fetch()) {
    $ref[] = $ar["ID"];
    $ref_id[] = $ar["ID"];
}
$arSiteDropdown = array("reference" => $ref, "reference_id" => $ref_id);

$arFilterFields = Array(
    "find_id",
    "find_date1",
    "find_date2",
    "find_site_id",
    "find_tags",
    "find_stat_sess_id",
    "find_url_to",
    "find_url_to_404",
);

$lAdmin->InitFilter($arFilterFields);
if ($lAdmin->IsDefaultFilter()) {
    $sdate = time();
    $sdate = mktime(0, 0, 0, date("m", $sdate), date("d", $sdate) - 1, date("Y", $sdate));
    $find_date1 = ConvertTimeStamp($sdate);
}

$arFilter = array();

if ($_REQUEST["find_id_exact_match"] == "Y") {
    $arFilter["=ID"] = $find_id;
} else {
    $arFilter["ID"] = $find_id;
}

$arFilter[">=TIMESTAMP_X"] = $find_date1;
$arFilter["<=TIMESTAMP_X"] = $find_date2;
$arFilter["=SITE_ID"] = $find_site_id;

if ($_REQUEST["find_tags_exact_match"] == "Y") {
    $arFilter["=TAGS"] = $find_tags;
} else {
    $arFilter["TAGS"] = $find_tags;
}

if ($_REQUEST["find_stat_sess_id_exact_match"] == "Y") {
    $arFilter["=STAT_SESS_ID"] = $find_stat_sess_id;
} else {
    $arFilter["STAT_SESS_ID"] = $find_stat_sess_id;
}

if ($_REQUEST["find_url_to_exact_match"] == "Y") {
    $arFilter["=URL_TO"] = $find_url_to;
} else {
    $arFilter["URL_TO"] = $find_url_to;
}

$arFilter["=URL_TO_404"] = $find_url_to_404;

foreach ($arFilter as $key => $value) {
    if ($value == '') {
        unset($arFilter[$key]);
    }
}
$arFilter["!TAGS"] = false;

$aContext = array();

$lAdmin->AddAdminContextMenu($aContext);
$arHeaders = array(
    array("id" => "TAGS", "content" => GetMessage("SEARCH_TAGSTAT_TAGS"), "sort" => "TAGS", "default" => true),
    array(
        "id" => "COUNT",
        "content" => GetMessage("SEARCH_TAGSTAT_COUNT"),
        "sort" => "COUNT",
        "default" => true,
        "align" => "right"
    ),
);

$lAdmin->AddHeaders($arHeaders);

$arFields = $lAdmin->GetVisibleHeaderColumns();
$arFields[] = "COUNT";

$rsData = CSearchStatistic::GetList(array($by => $order), $arFilter, $arFields, true);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

// navigation setup
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("SEARCH_TAGSTAT_TAGS")));

while ($arRes = $rsData->NavNext(true, "f_")) {
    $row =& $lAdmin->AddRow($f_ID, $arRes);
}

$lAdmin->AddFooter(
    array(
        array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value" => $rsData->SelectedRowsCount()),
    )
);

$lAdmin->CheckListMode();
/***************************************************************************
 * HTML form
 ****************************************************************************/
$APPLICATION->SetTitle(GetMessage("SEARCH_TAGSTAT_TITLE"));
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

if (is_object($message)) {
    echo $message->Show();
}
?>

<form name="form1" method="GET" action="<?= $APPLICATION->GetCurPage() ?>">
    <?
    $oFilter = new CAdminFilter(
        $sTableID . "_filter",
        array(
            "find_id" => GetMessage("SEARCH_TAGSTAT_ID"),
            "find_dates" => GetMessage("SEARCH_TAGSTAT_DATE"),
            "find_site_id" => GetMessage("SEARCH_TAGSTAT_SITE_ID"),
            "find_url_to" => GetMessage("SEARCH_TAGSTAT_URL_TO"),
            "find_stat_sess_id" => GetMessage("SEARCH_TAGSTAT_STAT_SESS_ID"),
        )
    );

    $oFilter->Begin();
    ?>
    <tr>
        <td nowrap><b><? echo GetMessage("SEARCH_TAGSTAT_TAGS") ?>:</b></td>
        <td><input type="text" name="find_tags" size="47" value="<? echo htmlspecialcharsbx($find_tags) ?>"></td>
    </tr>
    <tr>
        <td><? echo GetMessage("SEARCH_TAGSTAT_ID") ?>:</td>
        <td><input type="text" name="find_id" size="47" value="<? echo htmlspecialcharsbx($find_id) ?>"></td>
    </tr>
    <tr>
        <td width="0%" nowrap><? echo GetMessage("SEARCH_TAGSTAT_DATE") ?>:</td>
        <td width="0%" nowrap><? echo CalendarPeriod(
                "find_date1",
                $find_date1,
                "find_date2",
                $find_date2,
                "form1",
                "Y"
            ) ?></td>
    </tr>
    <tr>
        <td><? echo GetMessage("SEARCH_TAGSTAT_SITE_ID") ?>:</td>
        <td><? echo SelectBoxFromArray(
                "find_site_id",
                $arSiteDropdown,
                $find_site_id,
                GetMessage("SEARCH_TAGSTAT_SITE")
            ); ?></td>
    </tr>

    <tr>
        <td nowrap><? echo GetMessage("SEARCH_TAGSTAT_URL_TO") ?></td>
        <td><?
            echo SelectBoxFromArray(
                "find_url_to_404",
                array(
                    "reference" => array(GetMessage("MAIN_YES"), GetMessage("MAIN_NO")),
                    "reference_id" => array("Y", "N")
                ),
                htmlspecialcharsbx($find_url_to_404),
                GetMessage("SEARCH_TAGSTAT_404")
            );
            ?>&nbsp;<input type="text" name="find_url_to" size="33" value="<? echo htmlspecialcharsbx($find_url_to) ?>">
        </td>
    </tr>
    <tr>
        <td nowrap><? echo GetMessage("SEARCH_TAGSTAT_STAT_SESS_ID") ?></td>
        <td><input type="text" name="find_stat_sess_id" size="47"
                   value="<? echo htmlspecialcharsbx($find_stat_sess_id) ?>"></td>
    </tr>

    <?
    $oFilter->Buttons(array("table_id" => $sTableID, "url" => $APPLICATION->GetCurPage()));
    $oFilter->End();
    ?>
</form>

<?
$lAdmin->DisplayList();

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>
