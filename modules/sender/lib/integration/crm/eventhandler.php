<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage sender
 * @copyright 2001-2012 Bitrix
 */

namespace Bitrix\Sender\Integration\Crm;

use Bitrix\Crm\Activity\BindingSelector;
use Bitrix\Crm\Integrity\ActualEntitySelector;
use Bitrix\Main\Application;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
use Bitrix\Sender\Entity;
use Bitrix\Sender\Recipient;
use Bitrix\Sender\Runtime\TimeLineJob;
use Bitrix\Sender\TimeLineQueueTable;

Loc::loadMessages(__FILE__);

/**
 * Class EventHandler
 * @package Bitrix\Sender\Integration\Crm
 */
class EventHandler
{
    private const TIME_LINE_COUNT_LIMIT = 500;

    /**
     * Handler of event sender/OnAfterPostingSendRecipient.
     *
     * @param array $eventData Event.
     * @param Entity\Letter $letter Letter.
     */
    public static function onAfterPostingSendRecipient(array $eventData, Entity\Letter $letter)
    {
        if (!$eventData['SEND_RESULT']) {
            return;
        }

        static $isModuleIncluded = null;
        if ($isModuleIncluded === null) {
            $isModuleIncluded = Loader::includeModule('crm');
        }

        if (!$isModuleIncluded) {
            return;
        }

        if ($letter->getMessage()->isReturnCustomer()) {
            return;
        }

        $recipient = $eventData['RECIPIENT'];
        $fields = $eventData['RECIPIENT']['FIELDS'];

        $entityId = $fields['CRM_ENTITY_ID'];

        TimeLineQueueTable::add(
            [
                'RECIPIENT_ID' => $recipient['ID'],
                'POSTING_ID' => $letter->getId(),
                'FIELDS' => Json::encode($fields),
                'ENTITY_ID' => $entityId,
                'CONTACT_TYPE_ID' => $recipient['CONTACT_TYPE_ID'],
                'CONTACT_CODE' => $recipient['CONTACT_CODE'],
            ]
        );
    }

    /**
     * Handler of event sender/onAfterPostingSendRecipientMultiple.
     *
     * @param array $eventDataArray Event[].
     * @param Entity\Letter $letter Letter.
     */
    public static function onAfterPostingSendRecipientMultiple(array $eventDataArray, Entity\Letter $letter)
    {
        static $isModuleIncluded = null;

        if ($isModuleIncluded === null) {
            $isModuleIncluded = Loader::includeModule('crm');
        }

        if (!$isModuleIncluded) {
            return;
        }

        if ($letter->getMessage()->isReturnCustomer()) {
            return;
        }

        $dataToInsert = [];
        foreach ($eventDataArray as $eventData) {
            if (!$eventData['SEND_RESULT']) {
                continue;
            }

            $recipient = $eventData['RECIPIENT'];
            $fields = $eventData['RECIPIENT']['FIELDS'];

            $entityId = $fields['CRM_ENTITY_ID'] ?? $recipient['CONTACT_ID'];

            if (!$entityId) {
                continue;
            }

            $dataToInsert[] = [
                'RECIPIENT_ID' => $recipient['ID'],
                'POSTING_ID' => $letter->getId(),
                'FIELDS' => Json::encode($fields),
                'ENTITY_ID' => $entityId,
                'CONTACT_TYPE_ID' => $recipient['CONTACT_TYPE_ID'],
                'CONTACT_CODE' => $recipient['CONTACT_CODE'],
            ];
        }

        if ($dataToInsert) {
            TimeLineQueueTable::addMulti($dataToInsert, true);
        }
    }

    /**
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function handleTimeLineEvents($letterId)
    {
        static $isModuleIncluded = null;
        if ($isModuleIncluded === null) {
            $isModuleIncluded = Loader::includeModule('crm');
        }

        if (!$isModuleIncluded) {
            return "";
        }

        $entityTypeId = $entityId = null;
        $batchData = [];
        $idsToDelete = [];
        self::lockTimelineQueue($letterId);
        $letter = Entity\Letter::createInstanceById($letterId);

        try {
            $queuedRows = TimeLineQueueTable::getList(
                [
                    'filter' => [
                        '=STATUS' => TimeLineQueueTable::STATUS_NEW,
                        '=POSTING_ID' => $letterId
                    ],
                    'limit' => self::TIME_LINE_COUNT_LIMIT
                ]
            )->fetchAll();

            if (empty($queuedRows)) {
                return "";
            }

            foreach ($queuedRows as $row) {
                $idsToDelete[] = $row['ID'];
                if (!$letter) {
                    continue;
                }

                $fields = Json::decode($row['FIELDS']);
                if (isset($fields['CRM_ENTITY_TYPE_ID']) && $fields['CRM_ENTITY_TYPE_ID']) {
                    $entityTypeId = $fields['CRM_ENTITY_TYPE_ID'];
                }
                if (isset($fields['CRM_ENTITY_ID']) && $fields['CRM_ENTITY_ID']) {
                    $entityId = $fields['CRM_ENTITY_ID'];
                }

                if (!$entityTypeId || !$entityId) {
                    $selector = self::getEntitySelectorByRecipient(
                        $row['CONTACT_TYPE_ID'],
                        $row['CONTACT_CODE']
                    );
                } else {
                    $selector = self::getEntitySelectorById($entityTypeId, $entityId);
                }


                if (!$selector) {
                    continue;
                }

                if (!$selector->search()->hasEntities()) {
                    continue;
                }

                $recipient = [
                    'ID' => $row['RECIPIENT_ID'],
                    'CONTACT_TYPE_ID' => $row['CONTACT_TYPE_ID'],
                    'CONTACT_CODE' => $row['CONTACT_CODE'],
                ];

                $batchData[] = static::buildTimeLineEvent($selector, $letter, $recipient);
            }

            if (!empty($batchData)) {
                Timeline\RecipientEntry::createMulti($batchData);
            }

            TimeLineQueueTable::deleteList(['=ID' => $idsToDelete]);
        } catch (\Exception $e) {
        }

        self::unlockTimelineQueue($letterId);

        return TimeLineJob::getAgentName($letterId);
    }

    protected static function addTimeLineEvent(ActualEntitySelector $selector, Entity\Base $letter, $recipient)
    {
        $parameters = static::buildTimeLineEvent($selector, $letter, $recipient);

        if (!empty($parameters)) {
            Timeline\RecipientEntry::create($parameters);
        }
    }

    protected static function buildTimeLineEvent(ActualEntitySelector $selector, Entity\Base $letter, $recipient)
    {
        $isAd = $letter instanceof Entity\Ad;
        $createdBy = $letter->get('CREATED_BY');
        if (!$createdBy) {
            return [];
        }

        // convert format to time line
        $bindings = [];
        $activityBindings = BindingSelector::findBindings($selector);
        foreach ($activityBindings as $binding) {
            $binding['ENTITY_ID'] = $binding['OWNER_ID'];
            $binding['ENTITY_TYPE_ID'] = $binding['OWNER_TYPE_ID'];
            $bindings[] = [
                'ENTITY_TYPE_ID' => $binding['OWNER_TYPE_ID'],
                'ENTITY_ID' => $binding['OWNER_ID'],
            ];
        }

        return [
            'ENTITY_TYPE_ID' => $selector->getPrimaryTypeId(),
            'ENTITY_ID' => $selector->getPrimaryId(),
            'TYPE_CATEGORY_ID' => $letter->getMessage()->getCode(),
            'AUTHOR_ID' => $createdBy,
            'SETTINGS' => [
                'letterId' => $letter->getId(),
                'isAds' => $isAd,
                'recipient' => [
                    'id' => $recipient['ID'],
                    'typeId' => $recipient['CONTACT_TYPE_ID'],
                    'code' => $recipient['CONTACT_ID'],
                ],
            ],
            'BINDINGS' => $bindings
        ];
    }

    protected static function getEntitySelector()
    {
        /** @var ActualEntitySelector $selector */
        static $selector = null;
        if (!$selector) {
            $selector = new ActualEntitySelector();
        } else {
            $selector->clear();
        }

        return $selector;
    }

    protected static function getEntitySelectorById($entityTypeId, $entityId)
    {
        return self::getEntitySelector()->setEntity($entityTypeId, $entityId);
    }

    protected static function getEntitySelectorByRecipient($recipientTypeId, $recipientCode)
    {
        $selector = self::getEntitySelector();

        switch ($recipientTypeId) {
            case Recipient\Type::EMAIL:
                $selector->appendEmailCriterion($recipientCode);
                break;

            case Recipient\Type::PHONE:
                $selector->appendPhoneCriterion($recipientCode);
                break;

            default:
                return null;
        }

        return $selector;
    }

    /**
     * lock table from selecting of the other agents
     * @return bool
     * @throws SqlQueryException
     * @throws \Bitrix\Main\SystemException
     */
    protected static function lockTimelineQueue($letterId)
    {
        $connection = Application::getInstance()->getConnection();
        if ($connection instanceof DB\MysqlCommonConnection) {
            $lockDb = $connection->query(
                "SELECT GET_LOCK('time_line_queue_{$letterId}', 0) as L",
                false,
                "File: " . __FILE__ . "<br>Line: " . __LINE__
            );
            $lock = $lockDb->fetch();
            if ($lock["L"] == "1") {
                return true;
            }
        }

        return false;
    }

    /**
     * unlock table for select
     * @return bool
     * @throws SqlQueryException
     * @throws \Bitrix\Main\SystemException
     */
    protected static function unlockTimelineQueue($letterId)
    {
        $connection = Application::getInstance()->getConnection();
        if ($connection instanceof DB\MysqlCommonConnection) {
            $lockDb = $connection->query(
                "SELECT RELEASE_LOCK('time_line_queue_{$letterId}') as L"
            );
            $lock = $lockDb->fetch();
            if ($lock["L"] != "0") {
                return true;
            }
        }

        return false;
    }
}