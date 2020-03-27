<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2018 Bitrix
 */

namespace Bitrix\Main;

use Bitrix\Main\Entity;

class UserProfileHistoryTable extends Entity\DataManager
{
    const TYPE_ADD = 1;
    const TYPE_UPDATE = 2;
    const TYPE_DELETE = 3;

    public static function getTableName()
    {
        return 'b_user_profile_history';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField("ID", array(
                'primary' => true,
                'autocomplete' => true,
            )),
            new Entity\IntegerField("USER_ID", array(
                'required' => true,
            )),
            new Entity\IntegerField("EVENT_TYPE"),
            new Entity\DatetimeField("DATE_INSERT", array(
                'default_value' => function () {
                    return new Type\DateTime();
                }
            )),
            new Entity\StringField("REMOTE_ADDR"),
            new Entity\TextField('USER_AGENT'),
            new Entity\TextField('REQUEST_URI'),
            new Entity\IntegerField("UPDATED_BY_ID"),
        );
    }

    /**
     * @param int $userId User profile ID.
     * @param int $type See TYPE_* constants.
     * @param array|null $before Fields before update.
     * @param array|null $after Fields after update.
     * @return Entity\AddResult
     */
    public static function addHistory($userId, $type, array $before = null, array $after = null)
    {
        global $USER;

        $server = Context::getCurrent()->getServer();
        $request = Context::getCurrent()->getRequest();

        $url = preg_replace("/(&?sessid=[0-9a-z]+)/", "", $request->getDecodedUri());
        $remoteAddr = $server->get("REMOTE_ADDR");
        $userAgent = $server->get("HTTP_USER_AGENT");
        $updatedBy = (is_object($USER) && ($USER->GetID() > 0) ? $USER->GetID() : null);

        $changedFields = array();
        if (is_array($before) && is_array($after)) {
            //we shouldn't display some values
            static $hiddenFields = array("PASSWORD" => 1, "CHECKWORD" => 1, "CONFIRM_CODE" => 1);
            static $ignoredFields = array("TIMESTAMP_X" => 1);

            foreach ($after as $field => $value) {
                if (isset($ignoredFields[$field])) {
                    continue;
                }

                if ($before[$field] <> $value) {
                    $data = array(
                        "before" => (isset($hiddenFields[$field]) ? "***" : $before[$field]),
                        "after" => (isset($hiddenFields[$field]) ? "***" : $value)
                    );

                    $changedFields[] = array(
                        "FIELD" => $field,
                        "DATA" => $data,
                    );
                }
            }
        }

        $result = null;

        if (!empty($changedFields) || $type <> self::TYPE_UPDATE) {
            $result = static::add(array(
                "USER_ID" => $userId,
                "EVENT_TYPE" => $type,
                "REMOTE_ADDR" => $remoteAddr,
                "USER_AGENT" => $userAgent,
                "REQUEST_URI" => $url,
                "UPDATED_BY_ID" => $updatedBy,
            ));
        }

        if (!empty($changedFields) && $result->isSuccess()) {
            foreach ($changedFields as $value) {
                UserProfileRecordTable::add(array(
                    "HISTORY_ID" => $result->getId(),
                    "FIELD" => $value["FIELD"],
                    "DATA" => $value["DATA"],
                ));
            }
        }

        return $result;
    }

    public static function deleteByUser($userId)
    {
        $userId = intval($userId);

        UserProfileRecordTable::deleteByUser($userId);

        $entity = static::getEntity();
        $conn = $entity->getConnection();

        $conn->queryExecute("DELETE FROM b_user_profile_history WHERE USER_ID = {$userId}");

        $entity->cleanCache();
    }
}
