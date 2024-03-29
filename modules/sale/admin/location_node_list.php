<?

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

use Bitrix\Sale\Location;
use Bitrix\Sale\Location\Admin\LocationHelper as Helper;
use Bitrix\Sale\Location\Admin\SearchHelper;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

\Bitrix\Main\Loader::includeModule('sale');

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sale/prolog.php');

Loc::loadMessages(__FILE__);

$publicMode = $adminPage->publicMode;
$selfFolderUrl = $adminPage->getSelfFolderUrl();

if ($APPLICATION->GetGroupRight("sale") < "W") {
    $APPLICATION->AuthForm(Loc::getMessage('SALE_MODULE_ACCES_DENIED'));
}

$userIsAdmin = $APPLICATION->GetGroupRight("sale") >= "W";

#####################################
#### Data prepare
#####################################

try {
    if (!\Bitrix\Sale\Location\LocationTable::checkIntegrity()) {
        throw new Main\SystemException(Loc::getMessage('SALE_LOCATION_L_ERROR'));
    }

    $itemId = intval($_REQUEST[Helper::URL_PARAM_PARENT_ID]) ? intval($_REQUEST[Helper::URL_PARAM_PARENT_ID]) : false;
    $nameToDisplay = Helper::getNameToDisplay($itemId);

    // get entity fields for columns & filter
    $columns = Helper::getColumns('list');

    $sTableID = "tbl_location_node_list";

    $oSort = new CAdminUiSorting($sTableID, "SORT", "asc");
    $lAdmin = new CAdminUiList($sTableID, $oSort);

    ob_start();
    $APPLICATION->IncludeComponent(
        "bitrix:sale.location.selector.search",
        "",
        array(
            "ID" => "PARENT_ID",
            "CODE" => "",
            "INPUT_NAME" => "PARENT_ID",
            "PROVIDE_LINK_BY" => "id",
            "SHOW_ADMIN_CONTROLS" => "N",
            "SELECT_WHEN_SINGLE" => "N",
            "FILTER_BY_SITE" => "N",
            "SHOW_DEFAULT_LOCATIONS" => "N",
            "SEARCH_BY_PRIMARY" => "Y",
            "INITIALIZE_BY_GLOBAL_EVENT" => "onAdminFilterInited",
            "GLOBAL_EVENT_SCOPE" => "window",
            "UI_FILTER" => true
        ),
        false
    );
    $locationInput = ob_get_clean();
    $listTypes = array();
    foreach (Helper::getTypeList() as $tId => $tName) {
        $listTypes[$tId] = $tName;
    }

    $langObject = new \CLanguage();
    $langQueryObject = $langObject->getList();
    $quickSearchLangId = "EN";
    $quickSearchAlreadyUse = false;
    while ($lang = $langQueryObject->fetch()) {
        if ($lang["DEF"] == "Y") {
            $quickSearchLangId = mb_strtoupper($lang["LANGUAGE_ID"]);
        }
    }

    $filterFields = array();
    $listDefaultFields = array("ID");
    $listIgnoreFields = array("PARENT_ID", "TYPE_ID");
    foreach ($columns as $code => $fld) {
        if (in_array($code, $listIgnoreFields)) {
            continue;
        }
        $fType = "";
        $filterable = "";
        switch ($fld["data_type"]) {
            case "string":
                $filterable = "?";
                break;
            case "integer":
                $fType = "number";
                $filterable = "=";
                break;
        }
        $filterField = array(
            "id" => $code,
            "name" => $columns[$code]["title"]
        );
        if ($fType) {
            $filterField["type"] = $fType;
        }
        $filterField["filterable"] = $filterable;
        if (in_array($code, $listDefaultFields)) {
            $filterField["default"] = true;
        }
        if (preg_match("/^NAME_/", $code, $match) || preg_match("/^SHORT_NAME_/", $code, $match)) {
            $langId = str_replace($match[0], "", $code);
            $filterField["id"] = "NAME__" . $langId . "." . rtrim($match[0], "_");
            if ($quickSearchLangId == $langId && !$quickSearchAlreadyUse) {
                $filterField["quickSearch"] = "%";
                $quickSearchAlreadyUse = true;
            }
        }
        $filterFields[] = $filterField;
    }
    $filterFields[] = array(
        "id" => "PARENT_ID",
        "name" => $columns["PARENT_ID"]["title"],
        "type" => "custom",
        "value" => $locationInput,
        "filterable" => "=",
        "default" => true
    );
    $filterFields[] = array(
        "id" => "TYPE_ID",
        "name" => $columns["TYPE_ID"]["title"],
        "type" => "list",
        "items" => $listTypes,
        "filterable" => "=",
        "default" => true
    );
    $filterFields[] = array(
        "id" => "LEVEL",
        "name" => GetMessage("SALE_LOCATION_L_LEVEL"),
        "type" => "list",
        "items" => array(
            "ANY" => GetMessage("SALE_LOCATION_L_ANY"),
            "CURRENT_AND_LOWER" => GetMessage("SALE_LOCATION_L_CURRENT_AND_LOWER"),
            "CURRENT" => GetMessage("SALE_LOCATION_L_CURRENT")
        ),
        "filterable" => "",
        "default" => true
    );

    // order, select and filter for the list
    $listParams = Helper::proxyListRequest('list');

    $lAdmin->AddFilter($filterFields, $listParams['filter']);

    if (!empty($listParams['filter']['LEVEL'])) {
        if ($listParams['filter']['LEVEL'] == 'ANY') {
            unset($listParams['filter']['=PARENT_ID']);
        } elseif ($listParams['filter']['LEVEL'] == 'CURRENT_AND_LOWER') {
            if (intval($listParams['filter']['=PARENT_ID']) > 0) {
                $res = Location\LocationTable::getList(
                    array(
                        'filter' => array(
                            'ID' => intval($listParams['filter']['=PARENT_ID'])
                        ),
                        'select' => array('ID', 'LEFT_MARGIN', 'RIGHT_MARGIN')
                    )
                );

                if ($loc = $res->fetch()) {
                    $listParams['filter']['>LEFT_MARGIN'] = $loc['LEFT_MARGIN'];
                    $listParams['filter']['<RIGHT_MARGIN'] = $loc['RIGHT_MARGIN'];
                }
            }
            unset($listParams['filter']['=PARENT_ID']);
        }
    }

    unset($listParams['filter']['LEVEL']);

    #####################################
    #### ACTIONS
    #####################################

    global $DB;

    // group UPDATE
    if ($lAdmin->EditAction() && $userIsAdmin) {
        foreach ($FIELDS as $id => $arFields) {
            $DB->StartTransaction();

            if (!$lAdmin->IsUpdated($id)) // if there were no data change on this row - do nothing with it
            {
                continue;
            }

            try {
                $res = Helper::update($id, $arFields, true);

                if (!empty($res['errors'])) {
                    foreach ($res['errors'] as &$error) {
                        $error = '&nbsp;&nbsp;' . $error;
                    }
                    unset($error);

                    throw new Main\SystemException(implode(',<br />', $res['errors']));
                }
            } catch (Main\SystemException $e) {
                // todo: do smth
                $lAdmin->AddUpdateError(
                    Loc::getMessage(
                        'SALE_LOCATION_L_ITEM_SAVE_ERROR',
                        array('#ITEM#' => $id)
                    ) . ": <br />" . $e->getMessage() . '<br />',
                    $id
                );
                $DB->Rollback();
            }

            $DB->Commit();
        }

        Location\LocationTable::resetLegacyPath();
    }

    // group DELETE
    if (($ids = $lAdmin->GroupAction()) && $userIsAdmin) {
        // all by filter or certain ids
        if ($_REQUEST['action_target'] == 'selected') // get all ids if they were not specified (user choice was "for all")
        {
            $ids = Helper::getIdsByFilter($listParams['filter']);
        }

        @set_time_limit(0);

        foreach ($ids as $id) {
            if (!($id = intval($id))) {
                continue;
            }

            if ($_REQUEST['action'] == 'delete') {
                $DB->StartTransaction();

                try {
                    $res = Helper::delete($id, true);
                    if (!$res['success']) {
                        throw new Main\SystemException(
                            Loc::getMessage('SALE_LOCATION_L_ITEM') . ' ' . $id . ' : ' . implode(
                                '<br />',
                                $res['errors']
                            )
                        );
                    }
                    $DB->Commit();
                } catch (Main\SystemException $e) {
                    $lAdmin->AddGroupError(
                        Loc::getMessage('SALE_LOCATION_L_ITEM_DELETE_ERROR') . ": <br /><br />" . $e->getMessage(),
                        $id
                    );
                    $adminSidePanelHelper->sendJsonErrorResponse($e->getMessage());
                    $DB->Rollback();
                }
            }
        }

        Location\LocationTable::resetLegacyPath();

        $adminSidePanelHelper->sendSuccessResponse();
    }

    $adminResult = Helper::getList(
        $listParams,
        $sTableID,
        CAdminUiResult::GetNavSize($sTableID),
        array("uiMode" => true)
    );
    $adminResult = new \CAdminUiResult($adminResult, $sTableID);
    $lAdmin->SetNavigationParams($adminResult, array("BASE_LINK" => $selfFolderUrl . "sale_location_node_list.php"));
} catch (Main\SystemException $e) {
    $code = $e->getCode();
    $fatal = $e->getMessage() . (!empty($code) ? ' (' . $code . ')' : '');
}

#####################################
#### PAGE INTERFACE GENERATION
#####################################

if (empty($fatal)) {
    $headers = array();
    foreach (Helper::getListGridColumns() as $code => $fld) {
        $headers[] = array(
            "id" => $code,
            "content" => $columns[$code]['title'],
            "default" => $fld['DEFAULT'],
            "sort" => $code
        );
    }

    $lAdmin->AddHeaders($headers);
    while ($elem = $adminResult->NavNext(false)) {
        // urls
        $editUrl = Helper::getEditUrl($elem['ID']);
        $copyUrl = Helper::getEditUrl(false, array('copy_id' => $elem['ID']));
        $listUrl = Helper::getListUrl($elem['ID'], array());
        $editUrl = $adminSidePanelHelper->editUrlToPublicPage($editUrl);
        $copyUrl = $adminSidePanelHelper->editUrlToPublicPage($copyUrl);
        $listUrl = $adminSidePanelHelper->editUrlToPublicPage($listUrl);

        $row =& $lAdmin->AddRow($elem["ID"], $elem, $editUrl, Loc::getMessage('SALE_LOCATION_L_VIEW_CHILDREN'));

        foreach ($columns as $code => $fld) {
            if ($code == 'ID') {
                $row->AddViewField(
                    $code,
                    '<a href="' . $editUrl . '" title="' . Loc::getMessage(
                        'SALE_LOCATION_L_EDIT_ITEM'
                    ) . '">' . $elem["ID"] . '</a>'
                );
            } elseif ($code == 'TYPE_ID') {
                $row->AddSelectField($code, Helper::getTypeList());
            } else {
                $row->AddInputField($code);
            }
        }

        $arActions = array();

        $arActions[] = array(
            "ICON" => "edit",
            "TEXT" => Loc::getMessage('SALE_LOCATION_L_EDIT_ITEM'),
            "LINK" => $editUrl,
            "DEFAULT" => true
        );

        if ($userIsAdmin) {
            $arActions[] = array(
                "ICON" => "copy",
                "TEXT" => Loc::getMessage('SALE_LOCATION_L_COPY_ITEM'),
                "LINK" => $copyUrl
            );
            $arActions[] = array(
                "ICON" => "delete",
                "TEXT" => Loc::getMessage('SALE_LOCATION_L_DELETE_ITEM'),
                "ACTION" => "if(confirm('" . CUtil::JSEscape(
                        Loc::getMessage('SALE_LOCATION_L_CONFIRM_DELETE_ITEM')
                    ) . "')) " .
                    $lAdmin->ActionDoGroup($elem["ID"], "delete")
            );
        }

        $row->AddActions($arActions);
    }

    $lAdmin->AddGroupActionTable(
        Array(
            "delete" => true
        )
    );

    $aContext = array(
        array(
            "TEXT" => Loc::getMessage('SALE_LOCATION_L_ADD_ITEM'),
            "LINK" => $adminSidePanelHelper->editUrlToPublicPage(
                Helper::getEditUrl(false, array('parent_id' => $itemId))
            ),
            "TITLE" => Loc::getMessage('SALE_LOCATION_L_ADD_ITEM'),
            "ICON" => "btn_new"
        )
    );

    if ($_REQUEST[Helper::URL_PARAM_PARENT_ID] > 0) {
        $aContext[] = array(
            "TEXT" => GetMessage('SALE_LOCATION_L_EDIT_CURRENT'),
            "LINK" => $adminSidePanelHelper->editUrlToPublicPage(
                Helper::getEditUrl(false, array('id' => $_REQUEST[Helper::URL_PARAM_PARENT_ID]))
            ),
            "TITLE" => GetMessage('SALE_LOCATION_L_EDIT_CURRENT')
        );
    };
    $lAdmin->setContextSettings(array("pagePath" => $selfFolderUrl . "sale_location_node_list.php"));
    $lAdmin->AddAdminContextMenu($aContext);
    $lAdmin->CheckListMode();
} // empty($fatal)
?>

<? $APPLICATION->SetTitle(
    Loc::getMessage('SALE_LOCATION_L_EDIT_PAGE_TITLE') . ($nameToDisplay ? ': ' . $nameToDisplay : '')
) ?>

<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
if (!$publicMode && \Bitrix\Sale\Update\CrmEntityCreatorStepper::isNeedStub()) {
    $APPLICATION->IncludeComponent("bitrix:sale.admin.page.stub", ".default");
} else {
    SearchHelper::checkIndexesValid();

    if ($fatal <> '') {
        $messageParams = array('MESSAGE' => $fatal, 'type' => 'ERROR');
        if ($publicMode) {
            $messageParams["SKIP_PUBLIC_MODE"] = true;
        }
        ?>
        <div class="error-message">
            <? CAdminMessage::ShowMessage($messageParams) ?>
        </div>
        <?
    } else {
        $lAdmin->DisplayFilter($filterFields);
        $lAdmin->DisplayList();
    }
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");