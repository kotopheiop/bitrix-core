<?
if (!\Bitrix\Main\Loader::includeModule('rest') || !\Bitrix\Main\Loader::includeModule('calendar')) {
    return;
}

use Bitrix\Main;
use Bitrix\Rest\RestException;
use Bitrix\Calendar\Internals;
use Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);

/**
 * This class used for internal use only, not a part of public API.
 * It can be changed at any time without notification.
 *
 * @access private
 */
final class CCalendarRestService extends IRestService
{
    const SCOPE_NAME = 'calendar';
    const PLACEMENT_GRID_VIEW = 'CALENDAR_GRIDVIEW';

    public static function OnRestServiceBuildDescription()
    {
        $methods = [
            //Methods list
            "calendar.event.get" => [__CLASS__, "EventGet"],
            "calendar.event.add" => [__CLASS__, "EventAdd"],
            "calendar.event.update" => [__CLASS__, "EventUpdate"],
            "calendar.event.delete" => [__CLASS__, "EventDelete"],
            "calendar.event.get.nearest" => [__CLASS__, "EventGetNearest"],
            "calendar.event.getbyid" => [__CLASS__, "EventGetById"],

            "calendar.section.get" => [__CLASS__, "SectionGet"],
            "calendar.section.add" => [__CLASS__, "SectionAdd"],
            "calendar.section.update" => [__CLASS__, "SectionUpdate"],
            "calendar.section.delete" => [__CLASS__, "SectionDelete"],

            "calendar.resource.list" => [__CLASS__, "ResourceList"],
            "calendar.resource.add" => [__CLASS__, "ResourceAdd"],
            "calendar.resource.update" => [__CLASS__, "ResourceUpdate"],
            "calendar.resource.delete" => [__CLASS__, "ResourceDelete"],

            "calendar.resource.booking.list" => [__CLASS__, "ResourceBookingList"],
//			"calendar.resource.booking.add" => [__CLASS__, "ResourceBookingAdd"],
//			"calendar.resource.booking.update" => [__CLASS__, "ResourceBookingUpdate"],
//			"calendar.resource.booking.delete" => [__CLASS__, "ResourceBookingDelete"],

            "calendar.meeting.status.set" => [__CLASS__, "MeetingStatusSet"],
            "calendar.meeting.status.get" => [__CLASS__, "MeetingStatusGet"],
            "calendar.meeting.params.set" => [__CLASS__, "MeetingParamsSet"],
            "calendar.accessibility.get" => [__CLASS__, "MeetingAccessibilityGet"],
            "calendar.settings.get" => [__CLASS__, "SettingsGet"],
            "calendar.user.settings.get" => [__CLASS__, "UserSettingsGet"],
            "calendar.user.settings.set" => [__CLASS__, "UserSettingsSet"],
            // End methods list

            //Placements list
            \CRestUtil::PLACEMENTS => [
                self::PLACEMENT_GRID_VIEW => []
            ],
            //End placements list

            // Events
            \CRestUtil::EVENTS => [
                'OnCalendarEntryAdd' => array(
                    'calendar',
                    'OnAfterCalendarEntryAdd',
                    array(__CLASS__, 'PrepareOnCalendarEntryEvent'),
                    array(
                        "sendRefreshToken" => true,
                    )
                ),
                'OnCalendarEntryUpdate' => array(
                    'calendar',
                    'OnAfterCalendarEntryUpdate',
                    array(__CLASS__, 'PrepareOnCalendarEntryEvent'),
                    array(
                        "sendRefreshToken" => true,
                    )
                ),
                'OnCalendarEntryDelete' => array(
                    'calendar',
                    'OnAfterCalendarEventDelete',
                    array(__CLASS__, 'PrepareOnCalendarEntryEvent'),
                    array(
                        "sendRefreshToken" => true,
                    )
                ),
                'OnCalendarSectionAdd' => array(
                    'calendar',
                    'OnAfterCalendarSectionAdd',
                    array(__CLASS__, 'PrepareOnCalendarSectionEvent'),
                    array(
                        "sendRefreshToken" => true,
                    )
                ),
                'OnCalendarSectionUpdate' => array(
                    'calendar',
                    'OnAfterCalendarSectionUpdate',
                    array(__CLASS__, 'PrepareOnCalendarSectionEvent'),
                    array(
                        "sendRefreshToken" => true,
                    )
                ),
                'OnCalendarSectionDelete' => array(
                    'calendar',
                    'OnAfterCalendarSectionDelete',
                    array(__CLASS__, 'PrepareOnCalendarSectionEvent'),
                    array(
                        "sendRefreshToken" => true,
                    )
                )
            ]
        ];

        return array(self::SCOPE_NAME => $methods);
    }

    /*
     * Returns array of events
     *
     * @param array $params - incomoning params:
     * $params['type'] - (required) calendar type ('user'|'group')
     * $params['ownerId'] - owner id
     * $params['from'] - datetime, "from" limit, default value - 1 month before current date
     * $params['to'] - datetime, "to" limit, default value - 3 month after current date
     * $params['section'] - inline or array of sections
     * @return array of events
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.event.get",
     * {
     * 		type: 'user',
     *		ownerId: '1',
     * 		from: '2013-06-20',
     * 		to: '2013-08-20',
     * 		section: [21, 44]
     * });
     *
     */
    public static function EventGet($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.event.get";

        $necessaryParams = array('type');
        foreach ($necessaryParams as $param) {
            if (!isset($params[$param]) || empty($params[$param]))
                throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#PARAM_NAME#' => $param, '#REST_METHOD#' => $methodName)));
        }

        $type = $params['type'];
        $ownerId = intval($params['ownerId']);
        $from = false;
        $to = false;
        if (isset($params['from']))
            $from = CRestUtil::unConvertDateTime($params['from']);
        if (isset($params['to']))
            $to = CRestUtil::unConvertDateTime($params['to']);

        // Default values for from-to period
        if ($from === false && $to === false) {
            // Limits
            $ts = time();
            $pastDays = 30;
            $futureDays = 90;
            $from = CCalendar::Date($ts - CCalendar::DAY_LENGTH * $pastDays, false);
            $to = CCalendar::Date($ts + CCalendar::DAY_LENGTH * $futureDays, false);
        } elseif ($from !== false && $to === false) {
            $to = CCalendar::Date(CCalendar::GetMaxTimestamp(), false);
        }

        $arSectionIds = array();

        $sections = CCalendarSect::GetList(array('arFilter' => array('CAL_TYPE' => $type, 'OWNER_ID' => $ownerId)));
        foreach ($sections as $section) {
            if ($section['PERM']['view_full'] || $section['PERM']['view_title'] || $section['PERM']['view_time'])
                $arSectionIds[] = $section['ID'];
        }

        if (isset($params['section'])) {
            if (!is_array($params['section']) && $params['section'] > 0)
                $params['section'] = array($params['section']);
            $arSectionIds = array_intersect($arSectionIds, $params['section']);
        }

        $params = array(
            'type' => $type,
            'ownerId' => $ownerId,
            'userId' => $userId,
            'section' => $arSectionIds,
            'fromLimit' => $from,
            'toLimit' => $to
        );

        $arAttendees = array();
        $arEvents = CCalendar::GetEventList($params, $arAttendees);

        return $arEvents;
    }


    /*
     * Returns event by it id
     *
     * @param array $params - incomoning params:
     * $params['id'] - int, (required) calendar event id
     * @return event or null
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.event.getbyid",
     * {
     * 		id: 324
     * });
     *
     */
    public static function EventGetById($params = array(), $nav = null, $server = null)
    {
        $methodName = "calendar.event.getbyid";

        if (!isset($params['id']) || empty($params['id'])) {
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#PARAM_NAME#' => 'id', '#REST_METHOD#' => $methodName)));
        }

        $event = CCalendarEvent::GetById($params['id']);

        return $event ? $event : null;
    }

    /*
     * Add new event
     *
     * @param array $params - incomoning params:
     * $params['type'] - (required), number, calendar type
     * $params['ownerId'] - (required), number, owner id
     * $params['from'] - (required) datetime, "from" limit
     * $params['to'] - (required) datetime, "to" limit
     * $params['timezone_from'] - string, timezone, dafault value - timezone of current user
     * $params['timezone_to'] - string, timezone, dafault value - timezone of current user
     * $params['from_ts'] - timestamp, "from" limit, can be set instead of $params['from']
     * $params['to_ts'] - timestamp, "to" limit, can be set instead of $params['to']
     * $params['section'] - (required if $params['auto_detect_section'] is not "Y"), number, id of the section
     * $params['auto_detect_section'] - "Y" | "N", if "Y" $params['section'] could be skipped
     * $params['name'] - (required), string, name of the event
     * $params['skip_time'] - "Y"|"N",
     * $params['description'] - string, description of the event
     * $params['color'] - background color of the event
     * $params['text_color'] - text color of the event
     * $params['accessibility'] - 'busy'|'quest'|'free'|'absent' - accessibility for user
     * $params['importance'] - 'high' | 'normal' | 'low' - importance for the event
     * $params['private_event'] - "Y" | "N"
     * $params['rrule'] - array of the recurence Rule
     * $params['is_meeting'] - "Y" | "N"
     * $params['location'] - location
     * $params['remind'] - array(
     * 	array(
     * 		'type' => 'min'|'hour'|'day', type of reminder
     * 		'count' => count of time
     * 	)
     * ) - reminders
     * $params['attendees'] - array of the attendees for meeting if ($params['is_meeting'] == "Y")
     * $params['host'] - host of the event
     * $params['meeting'] = array(
        'text' =>  inviting text,
        'open' => true|false if meeting is open,
        'notify' => true|false,
        'reinvite' => true|false
    )
     * @return id of the new event.
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.event.add",
     * {
     *		type: 'user',
     *	 	ownerId: '2',
     * 		name: 'New Event Name',
     * 		description: 'Description for event',
     * 		from: '2013-06-14',
     * 		to: '2013-06-14',
     * 		skip_time: 'Y',
     * 		section: 5,
     * 		color: '#9cbe1c',
     * 		text_color: '#283033',
     * 		accessibility: 'absent',
     * 		importance: 'normal',
     * 		is_meeting: 'Y',
     * 		private_event: 'N',
     * 		remind: [{type: 'min', count: 20}],
     * 		location: 'Kaliningrad',
     * 		attendees: [1, 2, 3],
     *		host: 2,
     * 		meeting: {
     * 			text: 'inviting text',
     * 			open: true,
     * 			notify: true,
     * 			reinvite: false
     * 		}
     * });
     */
    public static function EventAdd($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.event.add";

        if (isset($params['from']))
            $params['from'] = CRestUtil::unConvertDateTime($params['from'], true);

        if (isset($params['to']))
            $params['to'] = CRestUtil::unConvertDateTime($params['to'], true);

        if (isset($params['from_ts']) && !isset($params['from']))
            $params['from'] = CCalendar::Date($params['from_ts']);

        if (isset($params['to_ts']) && !isset($params['to']))
            $params['to'] = CCalendar::Date($params['to_ts']);

        $necessaryParams = array('from', 'to', 'name', 'ownerId', 'type');
        if ($params['auto_detect_section'] !== "Y") {
            $necessaryParams[] = 'section';
        }
        foreach ($necessaryParams as $param) {
            if (!isset($params[$param]) || empty($params[$param]))
                throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#PARAM_NAME#' => $param, '#REST_METHOD#' => $methodName)));
        }

        $type = $params['type'];
        $ownerId = intval($params['ownerId']);

        $sectionId = $params['section'];
        $res = CCalendarSect::GetList(array('arFilter' => array('CAL_TYPE' => $type, 'OWNER_ID' => $ownerId, 'ID' => $sectionId)));
        if ($res && is_array($res) && isset($res[0])) {
            if (!$res[0]['PERM']['add'])
                throw new RestException(Loc::getMessage('CAL_REST_ACCESS_DENIED'));
        } else {
            throw new RestException('CAL_REST_SECTION_ERROR');
        }

        $arFields = array(
            "CAL_TYPE" => $type,
            "OWNER_ID" => $ownerId,
            "NAME" => trim($params['name']),
            "DATE_FROM" => $params['from'],
            "DATE_TO" => $params['to'],
            "SECTIONS" => $sectionId
        );

        if (isset($params['skip_time'])) {
            $arFields["SKIP_TIME"] = $params['skip_time'] == 'Y';
        }

        if (isset($params['skipTime'])) {
            $arFields["SKIP_TIME"] = $params['skipTime'] == 'Y';
        }

        if (!$arFields["SKIP_TIME"] && isset($params['timezone_from'])) {
            $arFields['TZ_FROM'] = $params['timezone_from'];
            $arFields['TZ_TO'] = isset($params['timezone_to']) ? $params['timezone_to'] : $params['timezone_from'];
        }

        if (isset($params['description']))
            $arFields["DESCRIPTION"] = trim($params['description']);

        if (isset($params['color'])) {
            $color = CCalendar::Color($params['color']);
            if ($color)
                $arFields["COLOR"] = $color;
        }

        if (isset($params['text_color'])) {
            $color = CCalendar::Color($params['text_color']);
            if ($color)
                $arFields["TEXT_COLOR"] = $color;
        }

        if (isset($params['accessibility']))
            $arFields["ACCESSIBILITY"] = $params['accessibility'];

        if (isset($params['importance']))
            $arFields["IMPORTANCE"] = $params['importance'];

        if (isset($params['private_event']))
            $arFields["PRIVATE_EVENT"] = $params['private_event'] == "Y";

        if (isset($params['rrule']))
            $arFields["RRULE"] = $params['rrule'];

        if (isset($params['is_meeting']))
            $arFields["IS_MEETING"] = $params['is_meeting'] == "Y";

        if (isset($params['location']))
            $arFields["LOCATION"] = $params['location'];

        if (isset($params['remind']))
            $arFields["REMIND"] = $params['remind'];

        $saveParams = array();
        if ($arFields['IS_MEETING']) {
            $arFields['ATTENDEES'] = (isset($params['attendees']) && is_array($params['attendees'])) ? $params['attendees'] : false;
            $arFields['ATTENDEES_CODES'] = array();
            if (is_array($arFields['ATTENDEES'])) {
                foreach ($arFields['ATTENDEES'] as $attendeeId) {
                    $code = 'U' . intval($attendeeId);
                    if (!in_array($code, $arFields['ATTENDEES_CODES'])) {
                        $arFields['ATTENDEES_CODES'][] = $code;
                    }
                }
            }

            $meeting = isset($params['meeting']) ? $params['meeting'] : array();
            $arFields['MEETING_HOST'] = isset($params['host']) ? intVal($params['host']) : $userId;
            $arFields['MEETING'] = array(
                'HOST_NAME' => CCalendar::GetUserName($arFields['MEETING_HOST']),
                'TEXT' => $meeting['text'],
                'OPEN' => (boolean)$meeting['open'],
                'NOTIFY' => (boolean)$meeting['notify'],
                'REINVITE' => (boolean)$meeting['reinvite']
            );

            $saveParams['userId'] = $arFields['MEETING_HOST'];
        }
        $saveParams['arFields'] = $arFields;
        if (isset($params['auto_detect_section']) && $params['auto_detect_section'] === 'Y') {
            $saveParams['autoDetectSection'] = true;
            $saveParams['autoCreateSection'] = true;
        }

        $newId = CCalendar::SaveEvent($saveParams);

        if (!$newId)
            throw new RestException(Loc::getMessage("CAL_REST_EVENT_NEW_ERROR"));

        return $newId;
    }

    /*
     * Edit existent event
     *
     * @param array $params - incomoning params:
     * $params['id'] - (required) event id,
     * $params['type'] - number, (required) calendar type
     * $params['ownerId'] - number, owner id
     * $params['from'] - datetime, "from" limit
     * $params['to'] - datetime, "to" limit
     * $params['timezone_from'] - string, timezone, dafault value - timezone of current user
     * $params['timezone_to'] - string, timezone, dafault value - timezone of current user
     * $params['from_ts'] - timestamp, "from" limit,
     * $params['to_ts'] - timestamp, "to" limit
     * $params['section'] - number,(required) id of the section
     * $params['name'] - string, (required) name of the event
     * $params['skip_time'] - "Y"|"N",
     * $params['description'] - string, description of the event
     * $params['color'] - background color of the event
     * $params['text_color'] - text color of the event
     * $params['accessibility'] - 'busy'|'quest'|'free'|'absent' - accessibility for user
     * $params['importance'] - 'high' | 'normal' | 'low' - importance for the event
     * $params['private_event'] - "Y" | "N"
     * $params['rrule'] - array of the recurence Rule
     * $params['is_meeting'] - "Y" | "N"
     * $params['location'] - location
     * $params['remind'] - array(
     * 	array(
     * 		'type' => 'min'|'hour'|'day', type of reminder
     * 		'count' => count of time
     * 	)
     * ) - reminders
     * $params['attendees'] - array of the attendees for meeting if ($params['is_meeting'] == "Y")
     * $params['host'] - host of the event
     * $params['meeting'] = array(
     * 		'text' =>  inviting text,
     * 		'open' => true|false if meeting is open,
     * 		'notify' => true|false,
     * 		'reinvite' => true|false
     * 	)
     * @return id of edited event
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.event.update",
     * {
     * 		id: 699
     *		type: 'user',
     *	 	ownerId: '2',
     * 		name: 'Changed Event Name',
     * 		description: 'New description for event',
     * 		from: '2013-06-17',
     * 		to: '2013-06-17',
     * 		skip_time: 'Y',
     * 		section: 5,
     * 		color: '#9cbe1c',
     * 		text_color: '#283033',
     * 		accessibility: 'free',
     * 		importance: 'normal',
     * 		is_meeting: 'N',
     * 		private_event: 'Y',
     * 		remind: [{type: 'min', count: 10}]
     * });
     */
    public static function EventUpdate($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.event.update";

        $necessaryParams = array('id', 'ownerId', 'type');
        foreach ($necessaryParams as $param) {
            if (!isset($params[$param]) || empty($params[$param]))
                throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#PARAM_NAME#' => $param, '#REST_METHOD#' => $methodName)));
        }

        $id = intVal($params['id']);
        $type = $params['type'];
        $ownerId = intval($params['ownerId']);

        if (isset($params['from']))
            $params['from'] = CRestUtil::unConvertDateTime($params['from'], true);

        if (isset($params['to']))
            $params['to'] = CRestUtil::unConvertDateTime($params['to'], true);

        if (isset($params['from_ts']) && !isset($params['from']))
            $params['from'] = CCalendar::Date($params['from_ts']);

        if (isset($params['to_ts']) && !isset($params['to']))
            $params['to'] = CCalendar::Date($params['to_ts']);

        $arFields = array(
            "ID" => $id,
            "DATE_FROM" => $params['from'],
            "DATE_TO" => $params['to']
        );

        if (isset($params['skipTime']))
            $arFields["SKIP_TIME"] = $params['skipTime'] == 'Y';
        if (isset($params['skip_time']))
            $arFields["SKIP_TIME"] = $params['skip_time'] == 'Y';

        if (!$arFields["SKIP_TIME"] && isset($params['timezone_from'])) {
            $arFields['TZ_FROM'] = $params['timezone_from'];
            $arFields['TZ_TO'] = isset($params['timezone_to']) ? $params['timezone_to'] : $params['timezone_from'];
        }

        if (isset($params['name'])) {
            $arFields["NAME"] = trim($params['name']);
            if (empty($arFields["NAME"])) {
                $arFields["NAME"] = Loc::getMessage('EC_T_NEW_EVENT');
            }
        }

        if (isset($params['description']))
            $arFields["DESCRIPTION"] = trim($params['description']);

        if (isset($params['section'])) {
            $sectionId = $params['section'];
            $arFields["SECTIONS"] = array($sectionId);

            $res = CCalendarSect::GetList(array('arFilter' => array('CAL_TYPE' => $type, 'OWNER_ID' => $ownerId, 'ID' => $params['section'])));
            if ($res && is_array($res) && isset($res[0])) {
                if (!$res[0]['PERM']['edit'])
                    throw new RestException(Loc::getMessage('CAL_REST_ACCESS_DENIED'));
            } else {
                throw new RestException('CAL_REST_SECTION_ERROR');
            }
        }

        if (isset($params['color'])) {
            $color = CCalendar::Color($params['color']);
            if ($color)
                $arFields["COLOR"] = $color;
        }

        if (isset($params['text_color'])) {
            $color = CCalendar::Color($params['text_color']);
            if ($color)
                $arFields["TEXT_COLOR"] = $color;
        }

        if (isset($params['accessibility']))
            $arFields["ACCESSIBILITY"] = $params['accessibility'];

        if (isset($params['importance']))
            $arFields["IMPORTANCE"] = $params['importance'];

        if (isset($params['private_event']))
            $arFields["PRIVATE_EVENT"] = $params['private_event'] == "Y";

        if (isset($params['rrule']))
            $arFields["RRULE"] = $params['rrule'];

        if (isset($params['is_meeting']))
            $arFields["IS_MEETING"] = $params['is_meeting'] == "Y";

        if (isset($params['location']))
            $arFields["LOCATION"] = $params['location'];

        if (isset($params['remind']))
            $arFields["REMIND"] = $params['remind'];

        $saveParams = array();
        if ($arFields['IS_MEETING']) {
            $arFields['ATTENDEES'] = (isset($params['attendees']) && is_array($params['attendees'])) ? $params['attendees'] : false;
            $arFields['ATTENDEES_CODES'] = array();
            if (is_array($arFields['ATTENDEES'])) {
                foreach ($arFields['ATTENDEES'] as $attendeeId) {
                    $code = 'U' . intval($attendeeId);
                    if (in_array($code, $arFields['ATTENDEES_CODES'])) {
                        $arFields['ATTENDEES_CODES'][] = $code;
                    }
                }
            }

            $meeting = isset($params['meeting']) ? $params['meeting'] : array();
            $arFields['MEETING_HOST'] = isset($params['host']) ? intVal($params['host']) : $userId;
            $arFields['MEETING'] = array(
                'HOST_NAME' => CCalendar::GetUserName($arFields['MEETING_HOST']),
                'TEXT' => $meeting['text'],
                'OPEN' => (boolean)$meeting['open'],
                'NOTIFY' => (boolean)$meeting['notify'],
                'REINVITE' => (boolean)$meeting['reinvite']
            );

            $saveParams['userId'] = $arFields['MEETING_HOST'];
        }
        $saveParams['arFields'] = $arFields;
        $newId = CCalendar::SaveEvent($saveParams);

        if (!$newId)
            throw new RestException(Loc::getMessage("CAL_REST_EVENT_UPDATE_ERROR"));

        return $newId;
    }

    /*
     * Delete event
     *
     * @param array $params - incomoning params:
     * $params['type'] (required) calendar type
     * $params['ownerId'] (required) owner id
     * $params['id'] (required) event id
     * @return true if everything ok
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.event.delete",
     * {
     * 		id: 698
     *		type: 'user',
     *	 	ownerId: '2'
     * });
     */
    public static function EventDelete($params = array(), $nav = null, $server = null)
    {
        $methodName = "calendar.event.delete";
        if (isset($params['id']) && intVal($params['id']) > 0)
            $id = intVal($params['id']);
        else
            throw new RestException(Loc::getMessage('CAL_REST_EVENT_ID_EXCEPTION'));

        $res = CCalendar::DeleteEvent($id);

        if ($res !== true) {
            if ($res === false)
                throw new RestException(Loc::getMessage('CAL_REST_EVENT_DELETE_ERROR'));
            else
                throw new RestException($res);
        }

        return $res;
    }

    /*
     * Return array of bearest events for current user
     *
     * @param array $params - incomoning params:
     * $params['ownerId'] - owner id
     * $params['type'] - calendar type
     * $params['days'] - future days count (default - 60)
     * $params['forCurrentUser'] - true/false - list of nearest events for current user
     * $params['maxEventsCount'] - maximum events count
     * $params['detailUrl'] - url for calendar
     *
     * @return array of events
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.event.get.nearest",
     * {
     *		type: 'user',
     *	 	ownerId: '2',
     * 		days: 10,
     * 		forCurrentUser: true,
     *		detailUrl: '/company/personal/user/#user_id#/calendar/'
     * });
     *
     */
    public static function EventGetNearest($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.event.get.nearest";

        if (!isset($params['type'], $params['ownerId']) || $params['forCurrentUser']) {
            $params['type'] = 'user';
            $params['ownerId'] = $userId;
            $params['forCurrentUser'] = true;
        }

        if (!isset($params['days']))
            $params['days'] = 60;

        // Limits
        $ts = time();
        $fromLimit = CCalendar::Date($ts, false);
        $toLimit = CCalendar::Date($ts + CCalendar::DAY_LENGTH * $params['days'], false);

        $arEvents = CCalendar::GetNearestEventsList(
            array(
                'bCurUserList' => !!$params['forCurrentUser'],
                'fromLimit' => $fromLimit,
                'toLimit' => $toLimit,
                'type' => $params['CALENDAR_TYPE'],
                'sectionId' => $params['CALENDAR_SECTION_ID']
            ));

        if ($arEvents == 'access_denied' || $arEvents == 'inactive_feature') {
            throw new RestException(Loc::getMessage('CAL_REST_ACCESS_DENIED'));
        } elseif (is_array($arEvents)) {

            if (isset($params['detailUrl'])) {
                if (strpos($params['detailUrl'], '?') !== FALSE)
                    $params['detailUrl'] = substr($params['detailUrl'], 0, strpos($params['detailUrl'], '?'));
                $params['detailUrl'] = str_replace('#user_id#', $userId, strtolower($params['detailUrl']));

                for ($i = 0, $l = count($arEvents); $i < $l; $i++) {
                    $arEvents[$i]['~detailUrl'] = CHTTP::urlAddParams($params['detailUrl'], ['EVENT_ID' => $arEvents[$i]['ID'], 'EVENT_DATE' => $arEvents[$i]['DATE_FROM']]);
                }
            }

            if (isset($params['maxEventsCount']))
                array_splice($arEvents, intVal($params['maxEventsCount']));
        }

        return $arEvents;
    }

    /*
     * Return list of sections
     *
     * @param array $params - incomoning params:
     * $params['type'] (required) calendar type
     * $params['ownerId'] (required) owner id
     *
     * @return array of sections
     *
     * @throws \Bitrix\Rest\RestException
     *
     *  @example (Javascript)
     * BX24.callMethod("calendar.section.get",
     * {
     * 		type: 'user',
     *		ownerId: '1'
     * });
     */
    public static function SectionGet($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.section.get";

        if (isset($params['type']))
            $type = $params['type'];
        else
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#REST_METHOD#' => $methodName, '#PARAM_NAME#' => 'type')));

        if (isset($params['ownerId']))
            $ownerId = intval($params['ownerId']);
        elseif ($type == 'user')
            $ownerId = $userId;
        else
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#REST_METHOD#' => $methodName, '#PARAM_NAME#' => 'ownerId')));

        $arFilter = array(
            'CAL_TYPE' => $type,
            'OWNER_ID' => $ownerId,
            'ACTIVE' => "Y"
        );

        $res = CCalendarSect::GetList(array('arFilter' => $arFilter));

        foreach ($res as $i => $section) {
            unset(
                $res[$i]['OUTLOOK_JS'],
                $res[$i]['DAV_EXCH_CAL'],
                $res[$i]['DAV_EXCH_MOD'],
                $res[$i]['SORT'],
                $res[$i]['PARENT_ID'],
                $res[$i]['IS_EXCHANGE'],
                $res[$i]['EXTERNAL_ID'],
                $res[$i]['ACTIVE'],
                $res[$i]['CAL_DAV_MOD'],
                $res[$i]['CAL_DAV_CAL'],
                $res[$i]['XML_ID']
            );
        }

        return $res;
    }

    /*
     * Add new section
     *
     * @param array $params - incomoning params:
     * $params['type'] - (required), number, calendar type
     * $params['ownerId'] - (required), number, owner id
     * $params['name'] - string, (required) name of the section
     * $params['description'] - string, description of the section
     * $params['color']
     * $params['text_color']
     * $params['export'] = array(
        'ALLOW' => true|false,
        'SET' => array
    )
     * $params['access'] - array of access data
     *
     * @return id of created section
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.section.add",
     * {
     * 		type: 'user',
     *	 	ownerId: '2',
     * 		name: 'New Section',
     * 		description: 'Description for section',
     * 		color: '#9cbeee',
     * 		text_color: '#283000',
     * 		export: [{ALLOW: false}]
     * 		access: {
     * 			'D114': 17,
     * 			'G2': 13,
     * 			'U2':15
     * 		}
     * });
     */
    public static function SectionAdd($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.section.add";
        $DEFAULT_COLOR = '#E6A469';
        $DEFAULT_TEXT_COLOR = '#000000';

        if (isset($params['type']))
            $type = $params['type'];
        else
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#REST_METHOD#' => $methodName, '#PARAM_NAME#' => 'type')));

        if (isset($params['ownerId']))
            $ownerId = intval($params['ownerId']);
        elseif ($type == 'user')
            $ownerId = $userId;
        else
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#REST_METHOD#' => $methodName, '#PARAM_NAME#' => 'ownerId')));

        $perm = CCalendar::GetPermissions(array(
            'type' => $type,
            'ownerId' => $ownerId,
            'userId' => $userId,
            'setProperties' => false
        ));

        if (($type == 'group' || $type == 'user')) {
            if (!$perm['section_edit'])
                throw new RestException(Loc::getMessage('CAL_REST_ACCESS_DENIED'));
        } else // other types
        {
            if (!CCalendarType::CanDo('calendar_type_edit_section'))
                throw new RestException(Loc::getMessage('CAL_REST_ACCESS_DENIED'));
        }

        $arFields = Array(
            'CAL_TYPE' => $type,
            'OWNER_ID' => $ownerId,
            'NAME' => (isset($params['name']) && trim($params['name']) != '') ? trim($params['name']) : '',
            'DESCRIPTION' => (isset($params['description']) && trim($params['description']) != '') ? trim($params['description']) : ''
        );

        if (isset($params['export']) && isset($params['export']['ALLOW']) && isset($params['export']['SET'])) {
            $arFields['EXPORT'] = array(
                'ALLOW' => !!$params['export']['ALLOW'],
                'SET' => $params['export']['SET']
            );
        }

        if (isset($params['color']))
            $arFields['COLOR'] = CCalendar::Color($params['color'], $DEFAULT_COLOR);
        else
            $arFields['COLOR'] = $DEFAULT_COLOR;

        if (isset($params['text_color']))
            $arFields['TEXT_COLOR'] = CCalendar::Color($params['text_color'], $DEFAULT_TEXT_COLOR);
        else
            $arFields['TEXT_COLOR'] = $DEFAULT_TEXT_COLOR;

        if (isset($params['access']) && is_array($params['access']))
            $arFields['ACCESS'] = $params['access'];

        $id = CCalendar::SaveSection(
            array(
                'bAffectToDav' => false,
                'arFields' => $arFields
            )
        );

        if (!$id)
            throw new RestException(Loc::getMessage('CAL_REST_SECTION_NEW_ERROR'));

        CCalendarSect::SetClearOperationCache(true);
        return $id;
    }

    /*
     * Update section
     *
     * @param array $params - incomoning params:
     * $params['id'] - (required) number, calendar type
     * $params['type'] - (required) number, calendar type
     * $params['ownerId'] - (required) number, owner id
     * $params['name'] - string, name of the section
     * $params['description'] - string, description of the section
     * $params['color']
     * $params['text_color']
     * $params['export'] = array(
        'ALLOW' => true|false,
        'SET' => array
    )
     * $params['access'] - array of access data
     *
     * @return id of modified section
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.section.update",
     * {
     * 		id: 325,
     * 		type: 'user',
     *	 	ownerId: '2',
     * 		name: 'Changed Section Name',
     * 		description: 'New description for section',
     * 		color: '#9cbeAA',
     * 		text_color: '#283099',
     * 		export: [{ALLOW: false}]
     * 		access: {
     * 			'D114': 17,
     * 			'G2': 13,
     * 			'U2':15
     * 		}
     * });
     */
    public static function SectionUpdate($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.section.update";

        if (isset($params['type']))
            $type = $params['type'];
        else
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#REST_METHOD#' => $methodName, '#PARAM_NAME#' => 'type')));

        if (isset($params['ownerId']))
            $ownerId = intval($params['ownerId']);
        elseif ($type == 'user')
            $ownerId = $userId;
        else
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#REST_METHOD#' => $methodName, '#PARAM_NAME#' => 'ownerId')));

        if (isset($params['id']) && intVal($params['id']) > 0)
            $id = intVal($params['id']);
        else
            throw new RestException(Loc::getMessage('CAL_REST_SECT_ID_EXCEPTION'));

        if (!CCalendar::IsPersonal($type, $ownerId, $userId) && !CCalendarSect::CanDo('calendar_edit_section', $id, $userId))
            throw new RestException(Loc::getMessage('CAL_REST_ACCESS_DENIED'));

        $arFields = Array(
            'ID' => $id,
            'CAL_TYPE' => $type,
            'OWNER_ID' => $ownerId
        );

        if (isset($params['name']) && trim($params['name']) != '')
            $arFields['NAME'] = trim($params['name']);

        if (isset($params['description']) && trim($params['description']) != '')
            $arFields['DESCRIPTION'] = trim($params['description']);

        if (isset($params['color']))
            $arFields['COLOR'] = CCalendar::Color($params['color']);

        if (isset($params['text_color']))
            $arFields['TEXT_COLOR'] = CCalendar::Color($params['text_color']);

        if (isset($params['access']) && is_array($params['access']))
            $arFields['ACCESS'] = $params['access'];

        $id = intVal(CCalendar::SaveSection(
            array(
                'bAffectToDav' => false,
                'arFields' => $arFields
            )
        ));

        if (!$id)
            throw new RestException(Loc::getMessage('CAL_REST_SECTION_SAVE_ERROR'));

        return $id;
    }

    /*
     * Delete section
     *
     * @param array $params - incomoning params:
     * $params['type'] (required) calendar type
     * $params['ownerId'] (required) owner id
     * $params['id'] (required) section id
     *
     * @return true if everything ok
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.section.delete",
     * {
     * 		type: 'user',
     *	 	ownerId: '2',
     * 		id: 521
     * });
     */
    public static function SectionDelete($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.section.delete";

        if (isset($params['type']))
            $type = $params['type'];
        else
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#REST_METHOD#' => $methodName, '#PARAM_NAME#' => 'type')));

        if (isset($params['ownerId']))
            $ownerId = intval($params['ownerId']);
        elseif ($type == 'user')
            $ownerId = $userId;
        else
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#REST_METHOD#' => $methodName, '#PARAM_NAME#' => 'ownerId')));

        if (isset($params['id']) && intVal($params['id']) > 0)
            $id = intVal($params['id']);
        else
            throw new RestException(Loc::getMessage('CAL_REST_SECT_ID_EXCEPTION'));

        if (!CCalendar::IsPersonal($type, $ownerId, $userId) && !CCalendarSect::CanDo('calendar_edit_section', $id, $userId))
            throw new RestException(Loc::getMessage('CAL_REST_ACCESS_DENIED'));

        $res = CCalendar::DeleteSection($id);

        if (!$res)
            throw new RestException(Loc::getMessage('CAL_REST_SECTION_DELETE_ERROR'));

        return $res;
    }

    /*
     * Set meeting status for current user
     *
     * @param array $params - incomoning params:
     * $params['eventId'] - event id
     * $params['status'] = 'Y' | 'N' | 'Q'
     *
     * @return true if everything ok
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.meeting.status.set",
     * {
     * 		eventId: '651',
     *	 	status: 'Y'
     * });
     */
    public static function MeetingStatusSet($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.meeting.status.set";

        $necessaryParams = array('eventId', 'status');
        foreach ($necessaryParams as $param) {
            if (!isset($params[$param]) || empty($params[$param]))
                throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#PARAM_NAME#' => $param, '#REST_METHOD#' => $methodName)));
        }

        $params['status'] = strtoupper($params['status']);
        if (!in_array($params['status'], array('Y', 'N', 'Q')))
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_ERROR', array('#PARAM_NAME#')));

        CCalendarEvent::SetMeetingStatus(array(
            'userId' => $userId,
            'eventId' => $params['eventId'],
            'status' => $params['status']
        ));

        return true;
    }

    /*
     * Return meeting status for current user for given event
     *
     * @param array $params - incomoning params:
     * $params['eventId'] - (required) event id
     *
     * @return status - "Y" | "N" | "Q"
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.meeting.status.get",
     * {
     * 		eventId: '651'
     * });
     */
    public static function MeetingStatusGet($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.meeting.status.get";

        $necessaryParams = array('eventId');
        foreach ($necessaryParams as $param) {
            if (!isset($params[$param]) || empty($params[$param]))
                throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#PARAM_NAME#' => $param, '#REST_METHOD#' => $methodName)));
        }

        $status = CCalendarEvent::GetMeetingStatus(
            $userId,
            $params['eventId'],
            $params['status']
        );

        if ($status === false)
            throw new RestException(Loc::getMessage('CAL_REST_GET_STATUS_ERROR'));

        return $status;
    }

    /*
     * Set meeting params for current user
     *
     * @param array $params - incomoning params:
     * $params['eventId'] - event id
     * $params['accessibility']
     * $params['remind']
     *
     * @return true if everything ok
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.meeting.params.set",
     * {
     * 		eventId: '651',
     *	 	accessibility: 'free',
     * 		remind: [{type: 'min', count: 20}]
     * });
     */
    public static function MeetingParamsSet($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.meeting.params.set";

        $necessaryParams = array('eventId');
        foreach ($necessaryParams as $param) {
            if (!isset($params[$param]) || empty($params[$param]))
                throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#PARAM_NAME#' => $param, '#REST_METHOD#' => $methodName)));
        }

        $result = CCalendarEvent::SetMeetingParams(
            $userId,
            intVal($params['eventId']),
            array(
                'ACCESSIBILITY' => $params['accessibility'],
                'REMIND' => $params['remind']
            )
        );

        if (!$result)
            throw new RestException(Loc::getMessage('CAL_REST_GET_DATA_ERROR'));

        return true;
    }

    /*
     * Allow to get user's accessibility
     *
     * @param array $params - incomoning params:
     * $params['users'] - (required) array of user ids
     * $params['from'] - (required) date, from limit
     * $params['to'] - (required) date, to limit
     *
     * @return array - array('user_id' => array()) - information about accessibility for each asked user
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.accessibility.get",
     * {
     * 		from: '2013-06-20',
     * 		to: '2013-12-20',
     * 		users: [1, 2, 34]
     * });
     */
    public static function MeetingAccessibilityGet($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.accessibility.get";

        $necessaryParams = array('from', 'to', 'users');
        foreach ($necessaryParams as $param) {
            if (!isset($params[$param]) || empty($params[$param]))
                throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#PARAM_NAME#' => $param, '#REST_METHOD#' => $methodName)));
        }

        $from = CRestUtil::unConvertDate($params['from']);
        $to = CRestUtil::unConvertDate($params['to']);

        $res = CCalendar::GetAccessibilityForUsers(array(
            'users' => $params['users'],
            'from' => $from,
            'to' => $to,
            'getFromHR' => true
        ));

        return $res;
    }

    /*
     * Return calendar general settings
     *
     * @return array of settings
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.settings.get", {});
     */
    public static function SettingsGet($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.settings.get";
        $settings = CCalendar::GetSettings();
        return $settings;
    }

    /*
     * Set calendar settings
     *
     * @param array $params - incomoning params:
     * $params['settings'] - (required) array of user's settings
     *
     * @return true if everything ok
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.settings.set",
     * {
     * 		settings: {
     * 			work_time_start: 9,
     * 			work_time_end: 19,
     * 			year_holidays: '1.01,2.01,7.01,23.02,8.03,1.05,9.05,12.06,4.11,12.12,03.04,05.04',
     * 			week_holidays:['SA','SU'],
     *			week_start: 'MO'
     * 		}
     * });
     */
    public static function SettingsSet($params = array(), $nav = null, $server = null)
    {
        global $USER;
        $methodName = "calendar.settings.set";

        if (!$USER->CanDoOperation('bitrix24_config') && !$USER->CanDoOperation('edit_php'))
            throw new RestException(Loc::getMessage('CAL_REST_ACCESS_DENIED'));

        if (!isset($params['settings']))
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#PARAM_NAME#' => 'settings', '#REST_METHOD#' => $methodName)));

        CCalendar::SetSettings($params['settings']);

        return true;
    }

    /*
     * Clears calendar settings
     *
     * @return true if everything ok
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.settings.clear",{});
     */
    public static function SettingsClear()
    {
        global $USER;
        $methodName = "calendar.settings.clear";

        if (!$USER->CanDoOperation('bitrix24_config') && !$USER->CanDoOperation('edit_php'))
            throw new RestException(Loc::getMessage('CAL_REST_ACCESS_DENIED'));

        CCalendar::SetSettings(array(), true);
        return true;
    }

    /*
     * Returns user's settings
     *
     * @return array of settings
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.user.settings.get",{});
     */
    public static function UserSettingsGet($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.user.settings.get";
        $settings = CCalendar::GetUserSettings($userId);
        return $settings;
    }

    /*
     * Saves user's settings
     *
     * @param array $params - incomoning params:
     * $params['settings'] - (required) array of user's settings
     *
     * @return true if everything ok
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.user.settings.set",
     * {
     * 		settings: {
     * 			tabId: 'month',
     * 			meetSection: '23',
     * 			blink: true,
     * 			showDeclined: false,
     *			showMuted: true
     * 		}
     * });
     */
    public static function UserSettingsSet($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.user.settings.set";

        if (!isset($params['settings']))
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#PARAM_NAME#' => 'settings', '#REST_METHOD#' => $methodName)));

        CCalendarUserSettings::Set($params['settings'], $userId);
        return true;
    }

    /*
     * Return list of all resources
     *
     * @return array of resources
     *
     * @throws \Bitrix\Rest\RestException
     *
     *  @example (Javascript)
     * BX24.callMethod("calendar.resource.list");
     */
    public static function ResourceList()
    {
        $methodName = "calendar.resource.list";
        $resources = [];

        $resourceList = Internals\SectionTable::getList(
            array(
                "filter" => [
                    "=ACTIVE" => 'Y',
                    "=CAL_TYPE" => 'resource'
                ],
                "select" => ["ID", "NAME", "CREATED_BY"]
            )
        );

        while ($resource = $resourceList->fetch()) {
            $resources[] = $resource;
        }

        return $resources;
    }

    /*
     * Add new resource
     *
     * @param array $params - incomoning params:
     * $params['name'] - string, (required) name of the resource
     *
     * @return id of created resource
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.resource.add",
     * {
     * 		name: 'My resource title'
     * });
     */
    public static function ResourceAdd($params = array(), $nav = null, $server = null)
    {
        $methodName = "calendar.resource.add";
        $type = 'resource';

        if (!isset($params['name']) || empty($params['name'])) {
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#REST_METHOD#' => $methodName, '#PARAM_NAME#' => 'name')));
        }

        if (!CCalendarType::CanDo('calendar_type_edit_section')) {
            throw new RestException(Loc::getMessage('CAL_REST_ACCESS_DENIED'));
        }

        $id = \CCalendarSect::edit([
            'arFields' => [
                'CAL_TYPE' => $type,
                'NAME' => $params['name'],
                'ACCESS' => []
            ]
        ]);

        if (!$id) {
            throw new RestException(Loc::getMessage('CAL_REST_RESOURCE_NEW_ERROR'));
        }

        CCalendarSect::SetClearOperationCache(true);
        return $id;
    }

    /*
     * Update resource
     *
     * @param array $params - incomoning params:
     * $params['resourceId'] - (required) number,
     * $params['name'] - (required) string, name of the resource
     *
     * @return id of modified section
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.resource.update",
     * {
     * 		resourceId: 325,
     * 		name: 'Changed Resource Name'
     * });
     */
    public static function ResourceUpdate($params = array(), $nav = null, $server = null)
    {
        $methodName = "calendar.resource.update";
        $type = 'resource';

        if (isset($params['resourceId']) && intVal($params['resourceId']) > 0) {
            $id = intVal($params['resourceId']);
        } else {
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#REST_METHOD#' => $methodName, '#PARAM_NAME#' => 'id')));
        }

        if (!isset($params['name']) || empty($params['name'])) {
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#REST_METHOD#' => $methodName, '#PARAM_NAME#' => 'name')));
        }

        if (!CCalendarType::CanDo('calendar_type_edit_section')) {
            throw new RestException(Loc::getMessage('CAL_REST_ACCESS_DENIED'));
        }

        $id = \CCalendarSect::edit([
            'arFields' => [
                'ID' => $id,
                'CAL_TYPE' => $type,
                'NAME' => $params['name'],
                'ACCESS' => []
            ]
        ]);

        if (!$id) {
            throw new RestException(Loc::getMessage('CAL_REST_RESOURCE_UPDATE_ERROR'));
        }

        CCalendarSect::SetClearOperationCache(true);
        return $id;
    }

    /*
     * Delete resource
     *
     * @param array $params - incomoning params:
     * $params['resourceId'] (required) resource id
     *
     * @return true if everything ok
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.resource.delete",
     * {
     * 		resourceId: 521
     * });
     */
    public static function ResourceDelete($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.resource.delete";

        if (isset($params['resourceId']) && intVal($params['resourceId']) > 0) {
            $id = intVal($params['resourceId']);
        } else {
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#REST_METHOD#' => $methodName, '#PARAM_NAME#' => 'resourceId')));
        }

        if (!CCalendarSect::CanDo('calendar_edit_section', $id, $userId))
            throw new RestException(Loc::getMessage('CAL_REST_ACCESS_DENIED'));

        $res = CCalendar::DeleteSection($id);

        if (!$res) {
            throw new RestException(Loc::getMessage('CAL_REST_SECTION_DELETE_ERROR'));
        }

        return $res;
    }


    /*
     * Return list of booking for resources by id or by resource types
     *
     * @param array $params - incomoning params:
     * $params['filter'] - array of ids of resource bookings
     * 		$params['filter']['resourceIdList'] - array of ids of resource bookings, if this parameter is specified other filter params (resourceTypeIdList, from, to) in filter are not used.
     * 		$params['filter']['resourceTypeIdList'] - array, list of resource type ids. Required if resourceIdList is not specified.
     * 		$params['filter']['from'] - datetime, "from" limit, default value - 1 month before current date
     * 		$params['filter']['to'] - datetime, "to" limit, default value - 3 month after current date
     * @return array of booking for resources
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.resource.booking.list", {
     * 		filter: {
     *			resourceTypeIdList: [10852, 10888, 10873, 10871, 10853]
     *			from: '2013-06-20',
     * 			to: '2013-08-20',
     * 		}
     *	};
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.resource.booking.list", {
     * 		filter: {
     *			resourceIdList: [10, 18, 17]
     * 		}
     *	};
     */
    public static function ResourceBookingList($params = [])
    {
        $type = 'resource';
        $methodName = "calendar.resource.booking.list";

        $userId = CCalendar::GetCurUserId();

        $idList = $params['filter']['resourceIdList'];
        if (isset($idList)) {
            if (!is_array($idList) && $idList > 0) {
                $idList = [$idList];
            }
            if (!empty($idList)) {
                $userId = CCalendar::GetCurUserId();
                $resourseList = \Bitrix\Calendar\UserField\ResourceBooking::getResourceEntriesList($idList);
                $entries = [];

                $eventIdList = [];
                $bookingIndex = [];
                if (is_array($resourseList) && isset($resourseList['ENTRIES']) && is_array($resourseList['ENTRIES'])) {
                    foreach ($resourseList['ENTRIES'] as $resEntry) {
                        $eventIdList[] = $resEntry['EVENT_ID'];
                        $bookingIndex[$resEntry['EVENT_ID']] = intval($resEntry['ID']);
                    }

                    if (count($eventIdList) > 0) {
                        $entries = CCalendarEvent::GetList(
                            array(
                                'arFilter' => array(
                                    'ID' => $eventIdList
                                ),
                                'parseRecursion' => true,
                                'fetchAttendees' => false,
                                'userId' => $userId,
                                'fetchMeetings' => false,
                                'setDefaultLimit' => false
                            )
                        );
                    }

                    foreach ($entries as $k => $entry) {
                        $entries[$k]['RESOURCE_BOOKING_ID'] = $bookingIndex[$entry['ID']];
                    }
                }

                return $entries;
            }
        }

        $resourceTypeIdList = $params['filter']['resourceTypeIdList'];
        if (!isset($resourceTypeIdList) || !is_array($resourceTypeIdList) || !count($resourceTypeIdList)) {
            throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#PARAM_NAME#' => 'filter[\'resourceTypeIdList\']', '#REST_METHOD#' => $methodName)));
        }

        $from = isset($params['filter']['from']) ? CRestUtil::unConvertDateTime($params['filter']['from']) : false;
        $to = isset($params['filter']['to']) ? CRestUtil::unConvertDateTime($params['filter']['to']) : false;

        // Default values for from-to period
        if ($from === false && $to === false) {
            // Limits
            $ts = time();
            $pastDays = 30;
            $futureDays = 90;
            $from = CCalendar::Date($ts - CCalendar::DAY_LENGTH * $pastDays, false);
            $to = CCalendar::Date($ts + CCalendar::DAY_LENGTH * $futureDays, false);
        } elseif ($from !== false && $to === false) {
            $to = CCalendar::Date(CCalendar::GetMaxTimestamp(), false);
        }

        $attendees = [];
        $entries = CCalendar::GetEventList([
            'type' => $type,
            'userId' => $userId,
            'section' => $resourceTypeIdList,
            'fromLimit' => $from,
            'toLimit' => $to
        ], $attendees);

        $eventIdList = [];
        $eventIndex = [];

        foreach ($entries as $i => $eventEntry) {
            $eventIdList[] = $eventEntry['ID'];
            $eventIndex[$eventEntry['ID']] = $i;
            $entries[$i]['RESOURCE_BOOKING_ID'] = null;
        }

        $resourseList = \Bitrix\Calendar\Internals\ResourceTable::getList(
            array(
                "select" => ["ID", "EVENT_ID"],
                "filter" => array(
                    "=EVENT_ID" => $eventIdList
                )
            )
        );

        while ($resBooking = $resourseList->fetch()) {
            if ($eventIndex[$resBooking['EVENT_ID']] >= 0) {
                $entries[$eventIndex[$resBooking['EVENT_ID']]]['RESOURCE_BOOKING_ID'] = $resBooking['ID'];
            }
        }

        return $entries;
    }

    /*
     * Books given resource
     *
     * @param array $params - incomoning params:
     * $params['resourceType'] - (required) string, type of the resource could be 'user' or 'resource'
     * $params['resourceId'] - (required) number, id of the resource or user
     * $params['from'] - (required) datetime, "from"
     * $params['to'] - (required) datetime, "to" limit
     * $params['timezone'] - string, timezone, dafault value - timezone of current user
     * $params['skipTime'] - is it booking for whole day(s) "Y"|"N" deafault value - "N",
     * $params['bookingName'] - string, name of the booking event
     * $params['serviceName'] - string, name of the booking event
     * $params['bindingEntityType'] - string, type of entity binded to the booking (example CRM_LEAD)
     * $params['bindingEntityId'] - number, id of entity binded to the booking
     *
     * @return array of booking for esources
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.resource.booking.add", {
     *		resourceTypeIdList: [10852, 10888, 10873, 10871, 10853]
     *		from: '2013-06-20',
     * 		to: '2013-08-20',
     *	};
     */
//	public static function ResourceBookingAdd($params = array())
//	{
//		$userId = CCalendar::GetCurUserId();
//		$methodName = "calendar.resource.booking.add";
//
//		$necessaryParams = array('resourceType', 'resourceId', 'from', 'to');
//
//		if (isset($params['from']))
//		{
//			$params['from'] = CRestUtil::unConvertDateTime($params['from']);
//		}
//
//		if (isset($params['to']))
//		{
//			$params['to'] = CRestUtil::unConvertDateTime($params['to']);
//		}
//
//		if (isset($params['resourceId']))
//		{
//			$params['resourceId'] = intval($params['resourceId']);
//		}
//
//		$params['bindingEntityType'] = isset($params['bindingEntityType']) ? $params['bindingEntityType'] : 'REST';
//		$params['bindingEntityId'] = isset($params['bindingEntityId']) ? intval($params['bindingEntityId']) : 0;
//
//		foreach ($necessaryParams as $param)
//		{
//			if (!isset($params[$param]) || empty($params[$param]))
//			{
//				throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#PARAM_NAME#' => $param,'#REST_METHOD#' => $methodName)));
//			}
//		}
//
//		if (!in_array($params['resourceType'], ['user', 'resource']))
//		{
//			throw new RestException(Loc::getMessage('CAL_REST_RES_TYPE_ERROR', array('#ALLOWED_TYPES#' => 'user|resource')));
//		}
//
//		$entryFields = array(
//			"DATE_FROM" => $params['from'],
//			"DATE_TO" => $params['to'],
//			"SKIP_TIME" => $params['skip_time'],
//			"NAME" => !empty($params['bookingName']) ? $params['bookingName'] : Loc::getMessage('CAL_REST_RES_BOOKING_DEFAULT_VALUE')
//		);
//
//		if ($params['serviceName'] !== '')
//		{
//			$entryFields["DESCRIPTION"] = Loc::getMessage("CAL_REST_RES_BOOKING_SERVICE_LABEL").': '.$params['serviceName'];
//		}
//
//		if (strtoupper($params['skipTime']) !== 'Y')
//		{
//			$userTimezoneName = \CCalendar::getUserTimezoneName($userId, true);
//			if($userTimezoneName)
//			{
//				$entryFields['TZ_FROM'] = $userTimezoneName;
//				$entryFields['TZ_TO'] = $userTimezoneName;
//			}
//		}
//
//		$resourceBookingId = \Bitrix\Calendar\UserField\ResourceBooking::saveResource(
//			false,
//			$params['resourceType'],
//			$params['resourceId'],
//			$entryFields,
//			[
//				'serviceName' => $params['serviceName'],
//				'bindingEntityType' => $params['bindingEntityType'],
//				'bindingEntityId' => $params['bindingEntityId'],
//				'bindingUserfieldId' => $params['bindingUserfieldId']
//			]
//		);
//
//		if (!$resourceBookingId)
//		{
//			throw new RestException(Loc::getMessage("CAL_REST_RESOURCE_BOOKING_ADD_ERROR"));
//		}
//
//		return $resourceBookingId;
//	}

    /*
     * Edit resource booking
     *
     * @param array $params - incomoning params:
     * $params['id'] - (required) numper, booking id,
     * $params['resourceType'] - (required) string, type of the resource could be 'user' or 'resource'
     * $params['resourceId'] - (required) number, id of the resource or user
     * $params['from'] - (required) datetime, "from"
     * $params['to'] - (required) datetime, "to" limit
     * $params['timezone'] - string, timezone, dafault value - timezone of current user
     * $params['skipTime'] - is it booking for whole day(s) "Y"|"N" deafault value - "N",
     * $params['bookingName'] - string, name of the booking event
     * $params['serviceName'] - string, name of the booking event
     * $params['bindingEntityType'] - string, type of entity binded to the booking (example CRM_LEAD)
     * $params['bindingEntityId'] - number, id of entity binded to the booking
     *
     * @return array of booking for esources
     *
     * @throws \Bitrix\Rest\RestException
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.resource.booking.update", {
     * 		id:
     *		resourceTypeIdList: [10852, 10888, 10873, 10871, 10853]
     *		from: '2013-06-20',
     * 		to: '2013-08-20',
     *	};
     */
//	public static function ResourceBookingUpdate($params = array())
//	{
//		$userId = CCalendar::GetCurUserId();
//		$methodName = "calendar.resource.booking.update";
//
//		$necessaryParams = array('id', 'resourceType', 'resourceId', 'from', 'to');
//
//		if (isset($params['id']))
//		{
//			$params['id'] = intval($params['id']);
//		}
//
//		if (isset($params['from']))
//		{
//			$params['from'] = CRestUtil::unConvertDateTime($params['from']);
//		}
//
//		if (isset($params['to']))
//		{
//			$params['to'] = CRestUtil::unConvertDateTime($params['to']);
//		}
//
//		if (isset($params['resourceId']))
//		{
//			$params['resourceId'] = intval($params['resourceId']);
//		}
//
//		$params['bindingEntityType'] = isset($params['bindingEntityType']) ? $params['bindingEntityType'] : 'REST';
//		$params['bindingEntityId'] = isset($params['bindingEntityId']) ? intval($params['bindingEntityId']) : 0;
//
//		foreach ($necessaryParams as $param)
//		{
//			if (!isset($params[$param]) || empty($params[$param]))
//			{
//				throw new RestException(Loc::getMessage('CAL_REST_PARAM_EXCEPTION', array('#PARAM_NAME#' => $param,'#REST_METHOD#' => $methodName)));
//			}
//		}
//
//		if (!in_array($params['resourceType'], ['user', 'resource']))
//		{
//			throw new RestException(Loc::getMessage('CAL_REST_RES_TYPE_ERROR', array('#ALLOWED_TYPES#' => 'user|resource')));
//		}
//
//		$entryFields = array(
//			"DATE_FROM" => $params['from'],
//			"DATE_TO" => $params['to'],
//			"SKIP_TIME" => $params['skip_time'],
//			"NAME" => !empty($params['bookingName']) ? $params['bookingName'] : Loc::getMessage('CAL_REST_RES_BOOKING_DEFAULT_VALUE')
//		);
//
//		if ($params['serviceName'] !== '')
//		{
//			$entryFields["DESCRIPTION"] = Loc::getMessage("CAL_REST_RES_BOOKING_SERVICE_LABEL").': '.$params['serviceName'];
//		}
//
//		if (strtoupper($params['skipTime']) !== 'Y')
//		{
//			$userTimezoneName = \CCalendar::getUserTimezoneName($userId, true);
//			if($userTimezoneName)
//			{
//				$entryFields['TZ_FROM'] = $userTimezoneName;
//				$entryFields['TZ_TO'] = $userTimezoneName;
//			}
//		}
//
//		$resourceBookingId = \Bitrix\Calendar\UserField\ResourceBooking::saveResource(
//			$params['id'],
//			$params['resourceType'],
//			$params['resourceId'],
//			$entryFields,
//			[
//				'serviceName' => $params['serviceName'],
//				'bindingEntityType' => $params['bindingEntityType'],
//				'bindingEntityId' => $params['bindingEntityId'],
//				'bindingUserfieldId' => $params['bindingUserfieldId']
//			]
//		);
//
//		if (!$resourceBookingId)
//		{
//			throw new RestException(Loc::getMessage("CAL_REST_RESOURCE_BOOKING_ADD_ERROR"));
//		}
//
//		return $resourceBookingId;
//	}

//	public static function ResourceBookingDelete($params = array())
//	{
//		$userId = CCalendar::GetCurUserId();
//		$methodName = "calendar.resource.booking.delete";
//
//		\CCalendar::deleteEvent(intVal($entry['EVENT_ID']), false, array('checkPermissions' => false));
//		Internals\ResourceTable::delete($entry['ID']);
//	}


    /*
     * Clears user's settings
     *
     * @return true if everything ok
     *
     * @example (Javascript)
     * BX24.callMethod("calendar.settings.clear",{});
     */
    public static function UserSettingsClear($params = array(), $nav = null, $server = null)
    {
        $userId = CCalendar::GetCurUserId();
        $methodName = "calendar.user.settings.clear";
        CCalendarUserSettings::Set(false, $userId);
        return true;
    }

    /*
     * Filters data fields about entry for event handlers
     *
     * @return array - array('id' => number) - id of entry which triggered event
     */
    public static function PrepareOnCalendarEntryEvent($params, $handler)
    {
        return ['id' => $params[0]];
    }

    /*
     * Filters data fields about section for event handlers
     *
     * @return array - array('id' => number) - id of section which triggered event
     */
    public static function PrepareOnCalendarSectionEvent($params, $handler)
    {
        return ['id' => $params[0]];
    }
}
