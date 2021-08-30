<?php

namespace Bitrix\Forum\Comments;

final class TimemanEntryEntity extends Entity
{
    const ENTITY_TYPE = 'tm';
    const MODULE_ID = 'timeman';
    const XML_ID_PREFIX = 'TIMEMAN_ENTRY_';

    /**
     * @return bool
     * @var integer $userId User Id.
     */
    public function canRead($userId)
    {
        return true;
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
}