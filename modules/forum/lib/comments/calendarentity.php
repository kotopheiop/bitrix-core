<?php

namespace Bitrix\Forum\Comments;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

final class CalendarEntity extends Entity
{
    const ENTITY_TYPE = 'ev';
    const MODULE_ID = 'calendar';
    const XML_ID_PREFIX = 'EVENT_';

    protected static $permissions = array();

    private $canRead = null;

    /**
     * @return bool
     * @var integer $userId User Id.
     */
    public function canRead($userId)
    {
        if ($this->canRead !== null) {
            return $this->canRead;
        }
        $this->canRead = \CCalendarEvent::canView($this->getId(), $userId);

        return $this->canRead;
    }

    /**
     * @return bool
     * @var integer $userId User Id.
     */
    public function canAdd($userId)
    {
        return $this->canRead($userId);
    }

    /**
     * @return bool
     * @var integer $userId User Id.
     */
    public function canEditOwn($userId)
    {
        return true;
    }

    /**
     * @return bool
     * @var integer $userId User Id.
     */
    public function canEdit($userId)
    {
        return false;
    }

    /**
     * Event before indexing message.
     * @param integer $id Message ID.
     * @param array $message Message data.
     * @param array &$index Search index array.
     * @return boolean
     */
    public static function onMessageIsIndexed($id, array $message, array &$index)
    {
        // not index yet because I do not have API from Calendar
        return false;

        if (!empty($message["PARAM1"]) || !empty($message["PARAM2"])) {
            return false;
        }

        if (
            preg_match("/" . self::getXmlIdPrefix() . "(\\d+)(*.?)/", $message["XML_ID"], $matches) &&
            ($eventId = intval($matches[1])) &&
            $eventId > 0
        ) {
            if (!array_key_exists($eventId, self::$permissions)) {
                self::$permissions[$eventId] = array();
            }
            $index["PERMISSIONS"] = self::$permissions[$eventId];
        }
        return true;
    }
}