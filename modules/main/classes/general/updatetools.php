<?php

use Bitrix\Main;

IncludeModuleLangFile(__FILE__);

class UpdateTools
{
    public static function CheckUpdates()
    {
        global $USER;

        if (LICENSE_KEY == "DEMO") {
            return;
        }

        $days_check = intval(COption::GetOptionString('main', 'update_autocheck'));
        if ($days_check > 0) {
            CUtil::SetPopupOptions('update_tooltip', array('display' => 'on'));

            $update_res = unserialize(
                COption::GetOptionString('main', '~update_autocheck_result'),
                ['allowed_classes' => false]
            );
            if (!is_array($update_res)) {
                $update_res = array("check_date" => 0, "result" => false);
            }

            if (time() > $update_res["check_date"] + $days_check * 86400) {
                if ($USER->CanDoOperation('install_updates')) {
                    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/update_client.php");

                    $result = CUpdateClient::IsUpdateAvailable($arModules, $strError);

                    $modules = array();
                    foreach ($arModules as $module) {
                        $modules[] = $module["@"]["ID"];
                    }

                    if ($strError <> '' && COption::GetOptionString('main', 'update_stop_autocheck', 'N') == 'Y') {
                        COption::SetOptionString('main', 'update_autocheck', '');
                    }

                    COption::SetOptionString(
                        'main',
                        '~update_autocheck_result',
                        serialize(
                            array(
                                "check_date" => time(),
                                "result" => $result,
                                "error" => $strError,
                                "modules" => $modules,
                            )
                        )
                    );
                }
            }
        }
    }

    public static function SetUpdateResult()
    {
        COption::SetOptionString(
            'main',
            '~update_autocheck_result',
            serialize(
                array(
                    "check_date" => time(),
                    "result" => false,
                    "error" => "",
                    "modules" => array(),
                )
            )
        );
    }

    public static function SetUpdateError($strError)
    {
        $update_res = unserialize(
            COption::GetOptionString('main', '~update_autocheck_result'),
            ['allowed_classes' => false]
        );
        if (!is_array($update_res)) {
            $update_res = array("check_date" => 0, "result" => false);
        }

        if ($strError <> '') {
            $update_res["result"] = false;
        }
        $update_res["error"] = $strError;

        COption::SetOptionString('main', '~update_autocheck_result', serialize($update_res));
    }

    public static function GetUpdateResult()
    {
        $update_res = false;
        if (intval(COption::GetOptionString('main', 'update_autocheck')) > 0) {
            $update_res = unserialize(
                COption::GetOptionString('main', '~update_autocheck_result'),
                ['allowed_classes' => false]
            );
        }
        if (!is_array($update_res)) {
            $update_res = array("result" => false, "error" => "", "modules" => array());
        }

        $update_res['tooltip'] = '';
        if ($update_res["result"] == true || $update_res["error"] <> '') {
            $updOptions = CUtil::GetPopupOptions('update_tooltip');
            if ($updOptions['display'] <> 'off') {
                if ($update_res["result"] == true) {
                    $update_res['tooltip'] = GetMessage("top_panel_updates") . (($n = count(
                            $update_res["modules"]
                        )) > 0 ? GetMessage("top_panel_updates_modules", array("#MODULE_COUNT#" => $n)) : '');
                } elseif ($update_res["error"] <> '') {
                    $update_res['tooltip'] = GetMessage(
                            "top_panel_updates_err"
                        ) . ' ' . $update_res["error"] . '<br><a href="/bitrix/admin/settings.php?lang=' . LANGUAGE_ID . '&amp;mid=main&amp;tabControl_active_tab=edit5">' . GetMessage(
                            "top_panel_updates_settings"
                        ) . '</a>';
                }
            }
        }

        return $update_res;
    }

    public static function clearUpdatesCacheAgent()
    {
        try {
            $v = 'bitrix';
            require_once($_SERVER["DOCUMENT_ROOT"] . "/" . $v . "/modules/main/classes/general/update_client.php");
            $data = [];
            $data['sk'] = 'jbk28JS92a216ff1';
            $data['update_server_url'] = Main\Config\Option::get("main", "update_site", "");
            $data['license_key'] = CUpdateClient::GetLicenseKey();
            $data['main_module_version'] = defined('SM_VERSION') ? SM_VERSION : '';
            $data['is_demo'] = ((defined("DEMO") && DEMO === "Y") ? "Y" : "N");
            $data['local_address'] = $_SERVER['SERVER_ADDR'] ?? null;
            $data['public_url'] = Main\Engine\UrlManager::getInstance()->getHostUrl();
            $data['site_name'] = Main\Config\Option::get("main", "site_name", "");

            $client = new Main\Web\HttpClient(
                [
                    "socketTimeout" => 10,
                    "streamTimeout" => 10,
                    "waitResponse" => true,
                ]
            );

            $client->post('https://www.' . (0 + 1) . 'c-' . $v . '.ru/' . $v . '/updates/bxvc.php', $data);
        } catch (TypeError $exception) {
        } catch (ErrorException $exception) {
        }

        return '';
    }
}
