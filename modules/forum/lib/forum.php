<?php

namespace Bitrix\Forum;

use Bitrix\Main\DB\SqlExpression;
use Bitrix\Main\Entity;
use Bitrix\Main\Entity\ReferenceField;
use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\EnumField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Type\DateTime;

Loc::loadMessages(__FILE__);

/**
 * Class ForumTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> FORUM_GROUP_ID int
 * <li> NAME string(255) mandatory
 * <li> DESCRIPTION text optional
 * <li> SORT int mandatory default '150'
 * <li> ACTIVE bool mandatory default 'Y'
 * <li> ALLOW_HTML bool mandatory default 'N'
 * <li> ALLOW_ANCHOR bool mandatory default 'Y'
 * <li> ALLOW_BIU bool mandatory default 'Y'
 * <li> ALLOW_IMG bool mandatory default 'Y'
 * <li> ALLOW_VIDEO bool mandatory default 'Y'
 * <li> ALLOW_LIST bool mandatory default 'Y'
 * <li> ALLOW_QUOTE bool mandatory default 'Y'
 * <li> ALLOW_CODE bool mandatory default 'Y'
 * <li> ALLOW_FONT bool mandatory default 'Y'
 * <li> ALLOW_SMILES bool mandatory default 'Y'
 * <li> ALLOW_UPLOAD bool mandatory default 'N'
 * <li> ALLOW_TABLE bool mandatory default 'N'
 * <li> ALLOW_ALIGN bool mandatory default 'Y'
 * <li> ALLOW_UPLOAD_EXT string(255) null
 * <li> ALLOW_MOVE_TOPIC bool mandatory default 'Y'
 * <li> ALLOW_TOPIC_TITLED bool mandatory default 'N'
 * <li> ALLOW_NL2BR bool mandatory default 'N'
 * <li> ALLOW_SIGNATURE bool mandatory default 'Y'
 * <li> ASK_GUEST_EMAIL bool mandatory default 'N'
 * <li> USE_CAPTCHA bool mandatory default 'N'
 * <li> INDEXATION bool mandatory default 'Y'
 * <li> DEDUPLICATION bool mandatory default 'Y'
 * <li> MODERATION bool mandatory default 'N'
 * <li> ORDER_BY enum('P', 'T', 'N', 'V', 'D', 'A', '') mandatory default 'P'
 * <li> ORDER_DIRECTION enum('DESC', 'ASC') mandatory default 'DESC'
 * <li> TOPICS int
 * <li> POSTS int
 * <li> LAST_POSTER_ID int
 * <li> LAST_POSTER_NAME string(255)
 * <li> LAST_POST_DATE datetime
 * <li> LAST_MESSAGE_ID int
 * <li> POSTS_UNAPPROVED int
 * <li> ABS_LAST_POSTER_ID int
 * <li> ABS_LAST_POSTER_NAME string(255)
 * <li> ABS_LAST_POST_DATE datetime
 * <li> ABS_LAST_MESSAGE_ID int
 * <li> EVENT1 string(255) default 'forum'
 * <li> EVENT2 string(255) default 'message'
 * <li> EVENT3 string(255)
 * <li> XML_ID varchar(255)
 * <li> HTML text
 * </ul>
 *
 * @package Bitrix\Forum
 */
class ForumTable extends \Bitrix\Main\Entity\DataManager
{
    private static $topicSort = array(
        "P" => "LAST_POST_DATE",
        "T" => "TITLE",
        "N" => "POSTS",
        "V" => "VIEWS",
        "D" => "START_DATE",
        "A" => "USER_START_NAME"
    );
    private static $cache = [];

    /**
     * Returns DB table name for entity
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_forum';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            (new IntegerField("ID", ["primary" => true, "autocomplete" => true])),
            (new IntegerField("FORUM_GROUP_ID")),
            (new StringField("NAME", ["required" => true, "size" => 255])),
            (new TextField("DESCRIPTION")),
            (new IntegerField("SORT", ["default_value" => 150])),
            (new BooleanField("ACTIVE", ["values" => ["N", "Y"], "default_value" => "Y"])),
            (new BooleanField("ALLOW_HTML", ["values" => ["N", "Y"], "default_value" => "N"])),
            (new BooleanField("ALLOW_ANCHOR", ["values" => ["N", "Y"], "default_value" => "Y"])),
            (new BooleanField("ALLOW_BIU", ["values" => ["N", "Y"], "default_value" => "Y"])),
            (new BooleanField("ALLOW_IMG", ["values" => ["N", "Y"], "default_value" => "Y"])),
            (new BooleanField("ALLOW_VIDEO", ["values" => ["N", "Y"], "default_value" => "Y"])),
            (new BooleanField("ALLOW_LIST", ["values" => ["N", "Y"], "default_value" => "Y"])),
            (new BooleanField("ALLOW_QUOTE", ["values" => ["N", "Y"], "default_value" => "Y"])),
            (new BooleanField("ALLOW_CODE", ["values" => ["N", "Y"], "default_value" => "Y"])),
            (new BooleanField("ALLOW_FONT", ["values" => ["N", "Y"], "default_value" => "Y"])),
            (new BooleanField("ALLOW_SMILES", ["values" => ["N", "Y"], "default_value" => "Y"])),
            (new BooleanField("ALLOW_TABLE", ["values" => ["N", "Y"], "default_value" => "Y"])),
            (new BooleanField("ALLOW_ALIGN", ["values" => ["N", "Y"], "default_value" => "Y"])),
            (new BooleanField("ALLOW_NL2BR", ["values" => ["N", "Y"], "default_value" => "Y"])),

            (new EnumField("ALLOW_UPLOAD", ["values" => ["Y", "F", "A"], "default_value" => "F"])),
            (new StringField("ALLOW_UPLOAD_EXT", ["size" => 255])),

            (new BooleanField("ALLOW_MOVE_TOPIC", ["values" => ["N", "Y"], "default_value" => "Y"])),
            (new BooleanField("ALLOW_TOPIC_TITLED", ["values" => ["N", "Y"], "default_value" => "Y"])),

            (new BooleanField("ALLOW_SIGNATURE", ["values" => ["N", "Y"], "default_value" => "Y"])),
            (new BooleanField("ASK_GUEST_EMAIL", ["values" => ["N", "Y"], "default_value" => "N"])),
            (new BooleanField("USE_CAPTCHA", ["values" => ["N", "Y"], "default_value" => "Y"])),

            (new BooleanField("INDEXATION", ["values" => ["N", "Y"], "default_value" => "Y"])),
            (new BooleanField("DEDUPLICATION", ["values" => ["N", "Y"], "default_value" => "N"])),
            (new BooleanField("MODERATION", ["values" => ["N", "Y"], "default_value" => "N"])),
            (new EnumField("ORDER_BY", ["values" => self::$topicSort, "default_value" => "P"])),
            (new EnumField("ORDER_DIRECTION", ["values" => ["ASC", "DESC"], "default_value" => "DESC"])),

            (new IntegerField("TOPICS")),
            (new IntegerField("POSTS")),
            (new IntegerField("POSTS_UNAPPROVED")),

            (new IntegerField("LAST_POSTER_ID")),
            (new StringField("LAST_POSTER_NAME", ["size" => 255])),
            (new DatetimeField(
                "LAST_POST_DATE", [
                "default_value" => function () {
                    return new DateTime();
                }
            ]
            )),
            (new IntegerField("LAST_MESSAGE_ID")),

            (new IntegerField("ABS_LAST_POSTER_ID")),
            (new StringField("ABS_LAST_POSTER_NAME", ["size" => 255])),
            (new DatetimeField(
                "ABS_LAST_POST_DATE", [
                "default_value" => function () {
                    return new DateTime();
                }
            ]
            )),
            (new IntegerField("ABS_LAST_MESSAGE_ID")),

            (new StringField("EVENT1")),
            (new StringField("EVENT2")),
            (new StringField("EVENT3")),
            (new StringField("XML_ID", ["size" => 255])),
            (new StringField("HTML", ["size" => 255])),

            (new Reference("PERMISSION", \Bitrix\Forum\PermissionTable::class, Join::on("this.ID", "ref.FORUM_ID"))),
            (new Reference("SITE", \Bitrix\Forum\ForumSiteTable::class, Join::on("this.ID", "ref.FORUM_ID"))),
            (new Reference("GROUP", \Bitrix\Forum\GroupTable::class, Join::on("this.FORUM_GROUP_ID", "ref.ID")))
        ];
    }

    private static function getFilteredFields()
    {
        return [
            "LAST_POSTER_NAME"
        ];
    }

    /*
     * Returns main data
     * @return array|null
     */
    public static function getMainData(int $forumId, ?string $siteId = null): ?array
    {
        $cacheKey = implode('_', ([$forumId] + ($siteId === null ? [] : [$siteId])));
        if (!array_key_exists($cacheKey, self::$cache)) {
            $q = ForumTable::query()
                ->setSelect(
                    [
                        'ID',
                        'FORUM_GROUP_ID',
                        'NAME',
                        'DESCRIPTION',
                        'SORT',
                        'ACTIVE',
                        'ALLOW_HTML',
                        'ALLOW_ANCHOR',
                        'ALLOW_BIU',
                        'ALLOW_IMG',
                        'ALLOW_VIDEO',
                        'ALLOW_LIST',
                        'ALLOW_QUOTE',
                        'ALLOW_CODE',
                        'ALLOW_FONT',
                        'ALLOW_SMILES',
                        'ALLOW_TABLE',
                        'ALLOW_ALIGN',
                        'ALLOW_UPLOAD',
                        'ALLOW_UPLOAD_EXT',
                        'ALLOW_MOVE_TOPIC',
                        'ALLOW_TOPIC_TITLED',
                        'ALLOW_NL2BR',
                        'ALLOW_SIGNATURE',
                        'ASK_GUEST_EMAIL',
                        'USE_CAPTCHA',
                        'INDEXATION',
                        'DEDUPLICATION',
                        'MODERATION',
                        'ORDER_BY',
                        'ORDER_DIRECTION',
                        'EVENT1',
                        'EVENT2',
                        'EVENT3',
                        'XML_ID'
                    ]
                )
                ->where('ID', $forumId)
                ->setCacheTtl(84600);
            if ($siteId !== null) {
                $q->registerRuntimeField(
                    '',
                    new ReferenceField(
                        'SITE',
                        ForumSiteTable::getEntity(),
                        [
                            '=ref.FORUM_ID' => 'this.ID',
                            '=ref.SITE_ID' => new SqlExpression('?s', $siteId)
                        ]
                    )
                )
                    ->addSelect('SITE.PATH2FORUM_MESSAGE', 'PATH2FORUM_MESSAGE');
            }
            self::$cache[$cacheKey] = $q->fetch() ?: null;
        }
        self::bindOldKernelEvents();
        return self::$cache[$cacheKey];
    }

    public static function onBeforeUpdate(\Bitrix\Main\ORM\Event $event)
    {
        $result = new \Bitrix\Main\ORM\EventResult();
        /** @var array $data */
        $data = $event->getParameter("fields");
        $id = $event->getParameter("id");
        $id = $id["ID"];
        $forum = self::getById($id)->fetch();

        if (\Bitrix\Main\Config\Option::get("forum", "FILTER", "Y") == "Y") {
            $filteredFields = self::getFilteredFields();
            if (!empty(array_intersect($filteredFields, array_keys($data)))) {
                $res = [];
                foreach ($filteredFields as $key) {
                    $res[$key] = $val = array_key_exists($key, $data) ? $data[$key] : $forum[$key];
                    if (!empty($val)) {
                        $res[$key] = \CFilterUnquotableWords::Filter($val);
                        if (empty($res[$key])) {
                            $res[$key] = "*";
                        }
                    }
                }
                $data["HTML"] = serialize($res);
            }
        }

        if ($data != $event->getParameter("fields")) {
            $result->modifyFields($data);
        }
        return $result;
    }

    public static function onAfterAdd(\Bitrix\Main\ORM\Event $event)
    {
        self::$cache = [];
        return new Entity\EventResult();
    }

    public static function onAfterUpdate(\Bitrix\Main\ORM\Event $event)
    {
        self::$cache = [];
        return new Entity\EventResult();
    }

    public static function onAfterDelete(\Bitrix\Main\ORM\Event $event)
    {
        self::$cache = [];
        return new Entity\EventResult();
    }

    public static function clearCache() // TODO redesign old forum new to D7
    {
        self::$cache = [];
        self::getEntity()->cleanCache();
    }

    private static function bindOldKernelEvents()  // TODO redesign old forum new to D7 and delete this function
    {
        static $bound = false;
        if ($bound === true) {
            return;
        }
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->addEventHandler("forum", "onAfterForumAdd", [__CLASS__, "clearCache"]);
        $eventManager->addEventHandler("forum", "onAfterForumUpdate", [__CLASS__, "clearCache"]);
        $eventManager->addEventHandler("forum", "OnAfterForumDelete", [__CLASS__, "clearCache"]);
        $bound = true;
    }
}

class Forum implements \ArrayAccess
{
    use \Bitrix\Forum\Internals\EntityFabric;
    use \Bitrix\Forum\Internals\EntityBaseMethods;

    /** @var int */
    protected $id = 0;
    /** @var array */
    protected $data = [];
    /** @var array */
    protected $strore = [];

    public function __construct($id)
    {
        $this->id = $id;
        if ($id <= 0) {
            throw new \Bitrix\Main\ArgumentNullException("Forum id is null.");
        }
        $this->data = ForumTable::getMainData($this->id);
        if (empty($this->data)) {
            throw new \Bitrix\Main\ObjectNotFoundException("Forum with id {$this->id} is not found.");
        }
        $this->bindEvents();
        $this->errorCollection = new \Bitrix\Main\ErrorCollection();
    }

    private function bindEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->addEventHandler("forum", "onAfterPermissionSet", [$this, "clearCache"]);
        $eventManager->addEventHandler("forum", "onAfterUserUpdate", [$this, "clearCache"]);
    }

    public function clearCache()
    {
        $this->strore = [];
    }

    public function getPermissions()
    {
        if (!array_key_exists("permission_for_all", $this->strore)) {
            $dbRes = PermissionTable::getList(
                [
                    "select" => ["GROUP_ID", "PERMISSION"],
                    "filter" => ["FORUM_ID" => $this->id],
                    "cache" => ["ttl" => 84600]
                ]
            );
            $this->strore["permission_for_all"] = [];
            while ($res = $dbRes->fetch()) {
                $this->strore["permission_for_all"][$res["GROUP_ID"]] = $res["PERMISSION"];
            }
        }
        return $this->strore["permission_for_all"];
    }

    private function getPermissionFromUserGroups(array $groups)
    {
        sort($groups);
        $key = "permission_" . implode("_", $groups);
        if (!array_key_exists($key, $this->strore)) {
            $this->strore[$key] =
            $dbRes = PermissionTable::getList(
                [
                    "select" => ["MAX_PERMISSION"],
                    "runtime" => [
                        new \Bitrix\Main\Entity\ExpressionField("MAX_PERMISSION", "MAX(%s)", ["PERMISSION"])
                    ],
                    "filter" => [
                        "FORUM_ID" => $this->id,
                        "GROUP_ID" => $groups + [2]
                    ],
                    "group" => "FORUM_ID",
                    "cache" => ["ttl" => "3600"]
                ]
            );
            $this->strore[$key] = ($res = $dbRes->fetch()) ? $res["MAX_PERMISSION"] : Permission::ACCESS_DENIED;
        }
        return $this->strore[$key];
    }

    public function getPermissionForUser(\Bitrix\Forum\User $user)
    {
        if ($user->isAdmin()) {
            $result = Permission::FULL_ACCESS;
        } elseif ($this->data["ACTIVE"] != "Y") {
            $result = Permission::ACCESS_DENIED;
        } else {
            $result = $this->getPermissionFromUserGroups($user->getGroups());
        }
        return $result;
    }

    public function getPermissionForUserGroups(array $groups)
    {
        if (\Bitrix\Forum\User::isUserAdmin($groups)) {
            $result = Permission::FULL_ACCESS;
        } elseif ($this->data["ACTIVE"] != "Y") {
            $result = Permission::ACCESS_DENIED;
        } else {
            $result = $this->getPermissionFromUserGroups($groups);
        }
        return $result;
    }

    public function setPermission(array $groups)
    {
        $dbRes = PermissionTable::getList(
            [
                "select" => ["ID"],
                "filter" => [
                    "FORUM_ID" => $this->id
                ]
            ]
        );
        while ($res = $dbRes->fetch()) {
            PermissionTable::delete($res["ID"]);
        }
        foreach ($groups as $key => $val) {
            PermissionTable::add(
                [
                    "FORUM_ID" => $this->id,
                    "GROUP_ID" => $key,
                    "PERMISSION" => mb_strtoupper($val)
                ]
            );
        }
        foreach (GetModuleEvents("forum", "onAfterPermissionSet", true) as $arEvent) {
            ExecuteModuleEventEx($arEvent, array($this->id, $groups));
        }
        return true;
    }

    public function getSites()
    {
        if (!array_key_exists("sites", $this->strore)) {
            $dbRes = ForumSiteTable::getList(
                [
                    "select" => ["*"],
                    "filter" => ["FORUM_ID" => $this->id],
                    "cache" => ["ttl" => 84600]
                ]
            );
            $this->strore["sites"] = [];
            while ($res = $dbRes->fetch()) {
                $this->strore["sites"][$res["SITE_ID"]] = $res["PATH2FORUM_MESSAGE"];
            }
        }
        return $this->strore["sites"];
    }

    public function calcStat()
    {
        $fields = [
            "TOPICS" => 0,
            "POSTS" => 0,
            "LAST_POSTER_ID" => 0,
            "LAST_POSTER_NAME" => 0,
            "LAST_POST_DATE" => 0,
            "LAST_MESSAGE_ID" => 0,
            "POSTS_UNAPPROVED" => 0,
            "ABS_LAST_POSTER_ID" => 0,
            "ABS_LAST_POSTER_NAME" => 0,
            "ABS_LAST_POST_DATE" => 0,
            "ABS_LAST_MESSAGE_ID" => 0
        ];
        if ($res = TopicTable::getList(
            [
                "select" => ["CNT_APPROVED"],
                "filter" => [
                    "FORUM_ID" => $this->id,
                    "APPROVED" => Topic::APPROVED_APPROVED
                ],
                "runtime" => [
                    new \Bitrix\Main\Entity\ExpressionField("CNT_APPROVED", "COUNT(*)")
                ]
            ]
        )->fetch()) {
            $fields["TOPICS"] = $res["CNT_APPROVED"];
            if ($res = MessageTable::getList(
                [
                    "select" => ["CNT_APPROVED", "MAX_APPROVED"],
                    "filter" => [
                        "FORUM_ID" => $this->id,
                        "APPROVED" => Message::APPROVED_APPROVED
                    ],
                    "runtime" => [
                        new \Bitrix\Main\Entity\ExpressionField("CNT_APPROVED", "COUNT(*)"),
                        new \Bitrix\Main\Entity\ExpressionField("MAX_APPROVED", "MAX(%s)", ["ID"])
                    ]
                ]
            )->fetch()) {
                $fields["POSTS"] = $res["CNT_APPROVED"];
                $fields["LAST_MESSAGE_ID"] = $res["MAX_APPROVED"];
            }
        }
        if ($res = MessageTable::getList(
            [
                "select" => ["CNT_ALL", "MAX_OF_ALL"],
                "filter" => [
                    "FORUM_ID" => $this->id
                ],
                "runtime" => [
                    new \Bitrix\Main\Entity\ExpressionField("CNT_ALL", "COUNT(*)"),
                    new \Bitrix\Main\Entity\ExpressionField("MAX_OF_ALL", "MAX(%s)", ["ID"])
                ]
            ]
        )->fetch()) {
            $fields["POSTS_UNAPPROVED"] = $res["CNT_ALL"] - $fields["POSTS"];
            $fields["ABS_LAST_MESSAGE_ID"] = $res["MAX_OF_ALL"];
        }
        if ($fields["LAST_MESSAGE_ID"] > 0 || $fields["ABS_LAST_MESSAGE_ID"] > 0) {
            $dbRes = MessageTable::getList(
                [
                    "select" => ["ID", "AUTHOR_ID", "AUTHOR_NAME", "POST_DATE"],
                    "filter" => [
                        "ID" => [
                            $fields["LAST_MESSAGE_ID"],
                            $fields["ABS_LAST_MESSAGE_ID"]
                        ]
                    ]
                ]
            );
            while ($res = $dbRes->fetch()) {
                if ($res["ID"] == $fields["LAST_MESSAGE_ID"]) {
                    $fields["LAST_POSTER_ID"] = $res["AUTHOR_ID"];
                    $fields["LAST_POSTER_NAME"] = $res["AUTHOR_NAME"];
                    $fields["LAST_POST_DATE"] = $res["POST_DATE"];
                }
                if ($res["ID"] == $fields["ABS_LAST_MESSAGE_ID"]) {
                    $fields["ABS_LAST_POSTER_ID"] = $res["AUTHOR_ID"];
                    $fields["ABS_LAST_POSTER_NAME"] = $res["AUTHOR_NAME"];
                    $fields["ABS_LAST_POST_DATE"] = $res["POST_DATE"];
                }
            }
        }
        ForumTable::update($this->id, $fields);
    }

    public function incrementStatistic(array $message)
    {
        $fields = [
            "ABS_LAST_POSTER_ID" => $message["AUTHOR_ID"],
            "ABS_LAST_POSTER_NAME" => $message["AUTHOR_NAME"],
            "ABS_LAST_POST_DATE" => $message["POST_DATE"],
            "ABS_LAST_MESSAGE_ID" => $message["ID"]
        ];
        if ($message["APPROVED"] == "Y") {
            $fields += [
                "LAST_POSTER_ID" => $message["AUTHOR_ID"],
                "LAST_POSTER_NAME" => $message["AUTHOR_NAME"],
                "LAST_POST_DATE" => $message["POST_DATE"],
                "LAST_MESSAGE_ID" => $message["ID"]
            ];
            $fields["POSTS"] = new \Bitrix\Main\DB\SqlExpression('?# + 1', "POSTS");
            if ($message["NEW_TOPIC"] == "Y") {
                $fields["TOPICS"] = new \Bitrix\Main\DB\SqlExpression('?# + 1', "TOPICS");
            }
        } else {
            $fields["POSTS_UNAPPROVED"] = new \Bitrix\Main\DB\SqlExpression('?# + 1', "POSTS_UNAPPROVED");
        }
        ForumTable::update($this->getId(), $fields);

        if (\CModule::IncludeModule("statistic")) {
            $F_EVENT1 = $this->data["EVENT1"];
            $F_EVENT2 = $this->data["EVENT2"];
            $F_EVENT3 = $this->data["EVENT3"];
            if (empty($F_EVENT3)) {
                $F_EVENT3 = $_SERVER["HTTP_REFERER"];
            }
            \CStatistics::Set_Event($F_EVENT1, $F_EVENT2, $F_EVENT3);
        }
    }
}