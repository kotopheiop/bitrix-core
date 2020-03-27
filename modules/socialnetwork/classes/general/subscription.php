<?

use Bitrix\Socialnetwork\Integration;
use Bitrix\Main\Localization\Loc;

class CAllSocNetSubscription
{
    function CheckFields($ACTION, &$arFields, $ID = 0)
    {
        global $APPLICATION;

        if (
            $ACTION != "ADD"
            && IntVal($ID) <= 0
        ) {
            $APPLICATION->ThrowException("System error 870164", "ERROR");
            return false;
        }

        if (
            (is_set($arFields, "USER_ID") || $ACTION == "ADD")
            && IntVal($arFields["USER_ID"]) <= 0
        ) {
            $APPLICATION->ThrowException(Loc::getMessage("SONET_SS_EMPTY_USER_ID"), "EMPTY_USER_ID");
            return false;
        } elseif (is_set($arFields, "USER_ID")) {
            $dbResult = CUser::GetByID($arFields["USER_ID"]);
            if (!$dbResult->Fetch()) {
                $APPLICATION->ThrowException(Loc::getMessage("SONET_SS_ERROR_NO_USER_ID"), "ERROR_NO_USER_ID");
                return false;
            }
        }

        if (
            (is_set($arFields, "CODE") || $ACTION == "ADD")
            && strlen(trim($arFields["CODE"])) <= 0
        ) {
            $APPLICATION->ThrowException(Loc::getMessage("SONET_SS_EMPTY_CODE"), "EMPTY_CODE");
            return false;
        }

        return True;
    }

    function Delete($ID)
    {
        global $DB;

        if (!CSocNetGroup::__ValidateID($ID))
            return false;

        $ID = IntVal($ID);

        $bSuccess = $DB->Query("DELETE FROM b_sonet_subscription WHERE ID = " . $ID . "", true);

        return $bSuccess;
    }

    public static function DeleteEx($userID = false, $code = false)
    {
        global $DB, $CACHE_MANAGER;

        $userID = IntVal($userID);
        $code = trim($code);

        if (
            $userID <= 0
            && strlen($code) <= 0
        )
            return false;

        $DB->Query("DELETE FROM b_sonet_subscription WHERE 1=1 " .
            (intval($userID) > 0 ? "AND USER_ID = " . $userID . " " : "") .
            (strlen($code) > 0 ? "AND CODE = '" . $code . "' " : "")
            , true);

        if (defined("BX_COMP_MANAGED_CACHE")) {
            $CACHE_MANAGER->ClearByTag("sonet_subscription" . ($code ? '_' . $code : ''));
        }

        return true;
    }

    public static function Set($userID, $code, $value = false)
    {
        global $CACHE_MANAGER;

        if (!CSocNetGroup::__ValidateID($userID)) {
            return false;
        }

        $userID = IntVal($userID);
        $code = trim($code);

        if (
            $userID <= 0
            || strlen($code) <= 0
        ) {
            return false;
        }

        $value = ($value == "Y" ? "Y" : "N");

        $rsSubscription = CSocNetSubscription::GetList(
            array(),
            array(
                "USER_ID" => $userID,
                "CODE" => $code
            )
        );

        $result = false;

        if ($arSubscription = $rsSubscription->Fetch()) {
            if ($value != "Y") {
                $result = CSocNetSubscription::delete($arSubscription["ID"]);
            }
        } else {
            if ($value == "Y") {
                $result = CSocNetSubscription::add(array(
                    "USER_ID" => $userID,
                    "CODE" => $code
                ));
            }
        }

        if (
            $result
            && preg_match('/^SG(\d+)$/i', $code, $matches)
        ) {
            $chatId = false;
            $groupId = $matches[1];
            $chatData = \Bitrix\Socialnetwork\Integration\Im\Chat\Workgroup::getChatData(Array(
                'group_id' => $groupId
            ));
            if (!empty($chatData[$groupId]) && intval($chatData[$groupId]) > 0) {
                $chatId = $chatData[$groupId];
            }

            if ($chatId) {
                $CIMChat = new CIMChat($userID);
                $CIMChat->muteNotify($chatId, ($value != "Y"));
            }
        }

        if (defined("BX_COMP_MANAGED_CACHE")) {
            $CACHE_MANAGER->ClearByTag("sonet_subscription_" . $code);
        }

        return true;
    }

    public static function NotifyGroup($arFields)
    {
        $arUserIDSent = array();

        if (!CModule::IncludeModule("im")) {
            return $arUserIDSent;
        }

        if (!is_array($arFields["GROUP_ID"])) {
            $arFields["GROUP_ID"] = array($arFields["GROUP_ID"]);
        }

        if (empty($arFields["GROUP_ID"])) {
            return $arUserIDSent;
        }

        if (empty($arFields["EXCLUDE_USERS"])) {
            $arFields["EXCLUDE_USERS"] = array();
        }

        if (intval($arFields["LOG_ID"]) > 0) {
            $rsUnFollower = CSocNetLogFollow::GetList(
                array(
                    "CODE" => "L" . intval($arFields["LOG_ID"]),
                    "TYPE" => "N"
                ),
                array("USER_ID")
            );

            while ($arUnFollower = $rsUnFollower->Fetch()) {
                $arFields["EXCLUDE_USERS"][] = $arUnFollower["USER_ID"];
            }

            $arFields["EXCLUDE_USERS"] = array_unique($arFields["EXCLUDE_USERS"]);
        }

        $roleList = array();

        if (
            !empty($arFields['PERMISSION'])
            && !empty($arFields['PERMISSION']['FEATURE'])
            && !empty($arFields['PERMISSION']['OPERATION'])
        ) {
            $roleList = \CSocNetFeaturesPerms::getOperationPerm(SONET_ENTITY_GROUP, $arFields["GROUP_ID"], $arFields['PERMISSION']['FEATURE'], $arFields['PERMISSION']['OPERATION']);
        }

        $chatData = $chatIdList = array();
        if (!empty($arFields["MESSAGE_CHAT"])) {
            $chatData = Integration\Im\Chat\Workgroup::getChatData(array(
                'group_id' => $arFields["GROUP_ID"]
            ));
        }

        if (!empty($chatData)) {
            $arFields["GROUP_ID"] = array_diff($arFields["GROUP_ID"], array_unique(array_keys($chatData)));
        }

        if (!empty($chatData)) {
            $tmp = \CSocNetLogTools::processPath(
                array(
                    "URL" => $arFields["URL"],
                ),
                (intval($arFields["FROM_USER_ID"]) > 0 ? $arFields["FROM_USER_ID"] : 1),
                SITE_ID
            );
            $chatUrl = $tmp["URLS"]["URL"];

            $chatMessageFields = array(
                "MESSAGE" => str_replace(
                    array("#URL#", "#url#"),
                    $chatUrl,
                    $arFields["MESSAGE_CHAT"]
                ),
            );

            if (intval($arFields["FROM_USER_ID"]) > 0) {
                $chatMessageFields["FROM_USER_ID"] = intval($arFields["FROM_USER_ID"]);
            }

            foreach ($chatData as $groupId => $chatId) {
                // don't send message to chat if it's unavailable for all members
                if (
                    isset($roleList[$groupId])
                    && $roleList[$groupId] < \Bitrix\Socialnetwork\UserToGroupTable::ROLE_USER
                ) {
                    continue;
                }

                \CIMChat::addMessage(array_merge(
                    $chatMessageFields, array(
                        "TO_CHAT_ID" => $chatId
                    )
                ));
            }
        }

        // if all groups processed by chats
        if (empty($arFields["GROUP_ID"])) {
            return $arUserIDSent;
        }

        $arMessageFields = array(
            "MESSAGE_TYPE" => IM_MESSAGE_SYSTEM,
            "NOTIFY_TYPE" => IM_NOTIFY_FROM,
            "NOTIFY_MODULE" => "socialnetwork",
            "NOTIFY_EVENT" => "sonet_group_event",
            "NOTIFY_TAG" => ""
        );

        if (intval($arFields["FROM_USER_ID"]) > 0) {
            $arMessageFields["FROM_USER_ID"] = $arFields["FROM_USER_ID"];
        }

        if (!empty($arFields["NOTIFY_TAG"])) {
            $arMessageFields["NOTIFY_TAG"] = $arFields["NOTIFY_TAG"];
            CIMNotify::DeleteByTag(
                $arFields["NOTIFY_TAG"],
                (intval($arFields["FROM_USER_ID"]) > 0 ? intval($arFields["FROM_USER_ID"]) : false)
            );
        } elseif (intval($arFields["LOG_ID"]) > 0) {
            $arMessageFields["NOTIFY_TAG"] = "SONET|EVENT|" . intval($arFields["LOG_ID"]);
        }

        $arUserToSend = array();
        $arUserIDToSend = array();
        $arGroupID = array();
        $arCodes = array();

        foreach ($arFields["GROUP_ID"] as $group_id) {
            $arCodes[] = "SG" . $group_id;
        }

        $rsSubscriber = CSocNetSubscription::GetList(
            array(),
            array(
                "CODE" => $arCodes
            ),
            false,
            false,
            array("USER_ID", "CODE")
        );

        while ($arSubscriber = $rsSubscriber->Fetch()) {
            if (
                !in_array($arSubscriber["USER_ID"], $arFields["EXCLUDE_USERS"])
                && !in_array($arSubscriber["USER_ID"], $arUserIDToSend)
            ) {
                if (preg_match('/^SG(\d+)$/', $arSubscriber["CODE"], $matches)) {
                    $arUserToSend[] = array(
                        "USER_ID" => $arSubscriber["USER_ID"],
                        "GROUP_ID" => $matches[1]
                    );
                    $arUserIDToSend[] = $arSubscriber["USER_ID"];
                    $arGroupID[] = $matches[1];
                }
            }
        }

        $arGroups = array();

        $rsGroup = CSocNetGroup::GetList(
            array(),
            array("ID" => $arGroupID),
            false,
            false,
            array("ID", "NAME", "OWNER_ID")
        );

        while ($arGroup = $rsGroup->GetNext()) {
            $arGroups[$arGroup["ID"]] = $arGroup;
        }

        $workgroupsPage = COption::GetOptionString("socialnetwork", "workgroups_page", "/workgroups/", SITE_ID);
        $groupUrlTemplate = COption::GetOptionString("socialnetwork", "group_path_template", "/workgroups/group/#group_id#/", SITE_ID);
        $groupUrlTemplate = "#GROUPS_PATH#" . substr($groupUrlTemplate, strlen($workgroupsPage), strlen($groupUrlTemplate) - strlen($workgroupsPage));

        $canViewUserIdList = array();

        foreach ($arUserToSend as $arUser) {
            $groupId = $arUser['GROUP_ID'];

            if (isset($roleList[$groupId])) {
                if (!isset($canViewUserIdList[$groupId])) {
                    $canViewUserIdList[$groupId] = array();
                    $res = \Bitrix\Socialnetwork\UserToGroupTable::getList(array(
                        'filter' => array(
                            '=GROUP_ID' => $groupId,
                            '<=ROLE' => $roleList[$groupId]
                        ),
                        'select' => array('USER_ID')
                    ));
                    while ($relation = $res->fetch()) {
                        $canViewUserIdList[$groupId][] = $relation['USER_ID'];
                    }
                }

                if (!in_array($arUser["USER_ID"], $canViewUserIdList[$groupId])) {
                    continue;
                }
            }

            $arMessageFields["TO_USER_ID"] = $arUser["USER_ID"];
            if (intval($arFields["LOG_ID"]) > 0) {
                $arMessageFields["NOTIFY_SUB_TAG"] = "SONET|EVENT|" . intval($arFields["LOG_ID"]) . "|" . intval($arUser["USER_ID"]);
            }

            $arTmp = CSocNetLogTools::ProcessPath(
                array(
                    "URL" => $arFields["URL"],
                    "GROUP_URL" => str_replace(array("#group_id#", "#GROUP_ID#"), $arUser["GROUP_ID"], $groupUrlTemplate)
                ),
                $arUser["USER_ID"],
                SITE_ID
            );
            $url = $arTmp["URLS"]["URL"];

            $serverName = (
            strpos($url, "http://") === 0
            || strpos($url, "https://") === 0
                ? ""
                : $arTmp["SERVER_NAME"]
            );

            $groupUrl = $serverName . $arTmp["URLS"]["GROUP_URL"];

            $group_name = (array_key_exists($arUser["GROUP_ID"], $arGroups) ? $arGroups[$arUser["GROUP_ID"]]["NAME"] : "");
            $arMessageFields["NOTIFY_MESSAGE"] = str_replace(
                array("#URL#", "#url#", "#group_name#", "#GROUP_ID#", "#group_id#"),
                array($url, $url, "<a href=\"" . $groupUrl . "\" class=\"bx-notifier-item-action\">" . $group_name . "</a>", $arUser["GROUP_ID"], $arUser["GROUP_ID"]),
                $arFields["MESSAGE"]
            );
            $arMessageFields["NOTIFY_MESSAGE_OUT"] = str_replace(
                array("#URL#", "#url#", "#group_name#"),
                array($serverName . $url, $serverName . $url, $group_name),
                $arFields["MESSAGE_OUT"]
            );

            $arMessageFields["PUSH_PARAMS"] = array(
                "ACTION" => "sonet_group_event",
                "TAG" => $arMessageFields["NOTIFY_TAG"]
            );

            if (intval($arFields["FROM_USER_ID"]) > 0) {
                $dbAuthor = \CUser::getByID($arFields["FROM_USER_ID"]);
                if ($arAuthor = $dbAuthor->fetch()) {
                    if (!empty($arAuthor["PERSONAL_PHOTO"])) {
                        $imageResized = CFile::resizeImageGet(
                            $arAuthor["PERSONAL_PHOTO"],
                            array(
                                "width" => 100,
                                "height" => 100
                            ),
                            BX_RESIZE_IMAGE_EXACT
                        );
                        if ($imageResized) {
                            $authorAvatarUrl = \Bitrix\Im\Common::getPublicDomain() . $imageResized["src"];
                        }
                    }

                    $authorName = CUser::formatName(\CSite::getNameFormat(), $arAuthor, true);
                }
            }

            if (empty($authorName)) {
                $authorName = Loc::getMessage("SONET_SS_PUSH_USER");
            }

            $arMessageFields["PUSH_PARAMS"]["ADVANCED_PARAMS"] = array(
                'senderName' => $authorName
            );

            if (!empty($authorAvatarUrl)) {
                $arMessageFields["PUSH_PARAMS"]["ADVANCED_PARAMS"]["avatarUrl"] = $authorAvatarUrl;
            }

            $arMessageFields["PUSH_MESSAGE"] = str_replace(
                array("[URL=#URL#]", "[URL=#url#]", "[/URL]", "#group_name#", "#GROUP_ID#", "#group_id#"),
                array('', '', '', $group_name, $arUser["GROUP_ID"], $arUser["GROUP_ID"]),
                $arFields["MESSAGE"]
            );

            $arMessageFields2Send = $arMessageFields;
            if (
                !is_set($arMessageFields2Send["FROM_USER_ID"])
                || intval($arMessageFields2Send["FROM_USER_ID"]) <= 0
            ) {
                $arMessageFields2Send["NOTIFY_TYPE"] = IM_NOTIFY_SYSTEM;
                $arMessageFields2Send["FROM_USER_ID"] = 0;
            }

            CIMNotify::Add($arMessageFields2Send);

            $arUserIDSent[] = $arUser["USER_ID"];
        }

        return $arUserIDSent;
    }

    public static function IsUserSubscribed($userID, $code)
    {
        global $CACHE_MANAGER;

        $userID = intval($userID);
        if ($userID <= 0) {
            return false;
        }

        $code = trim($code);
        if (strlen($code) <= 0) {
            return false;
        }

        $cache = new CPHPCache;
        $cache_time = 31536000;
        $cache_id = "entity_" . $code;
        $cache_path = "/sonet/subscription/";

        if ($cache->InitCache($cache_time, $cache_id, $cache_path)) {
            $arCacheVars = $cache->GetVars();
            $arSubscriberID = $arCacheVars["arSubscriberID"];
        } else {
            $cache->StartDataCache($cache_time, $cache_id, $cache_path);
            $arSubscriberID = array();

            $rsSubscription = CSocNetSubscription::GetList(
                array(),
                array("CODE" => $code)
            );

            while ($arSubscription = $rsSubscription->Fetch())
                $arSubscriberID[] = $arSubscription["USER_ID"];

            if (defined("BX_COMP_MANAGED_CACHE")) {
                $CACHE_MANAGER->StartTagCache($cache_path);
                $CACHE_MANAGER->RegisterTag("sonet_subscription_" . $code);
                $CACHE_MANAGER->RegisterTag("sonet_group");
            }

            $arCacheData = Array(
                "arSubscriberID" => $arSubscriberID
            );
            $cache->EndDataCache($arCacheData);

            if (defined("BX_COMP_MANAGED_CACHE")) {
                $CACHE_MANAGER->EndTagCache();
            }
        }

        return (in_array($userID, $arSubscriberID));
    }

    public static function OnAfterChatMuteNotify($fields)
    {
        $result = false;

        if (
            !is_array($fields)
            || empty($fields['USER_ID'])
            || empty($fields['MUTE'])
            || empty($fields['CHAT'])
            || !isset($fields['CHAT']['ENTITY_TYPE'])
            || $fields['CHAT']['ENTITY_TYPE'] != Integration\Im\Chat\Workgroup::CHAT_ENTITY_TYPE
            || empty($fields['CHAT']['ENTITY_ID'])
        ) {
            return $result;
        }

        $groupId = intval($fields['CHAT']['ENTITY_ID']);
        $userId = intval($fields['USER_ID']);

        return self::set($userId, "SG" . $groupId, ($fields['MUTE'] != "Y" ? "Y" : "N"));
    }
}

?>