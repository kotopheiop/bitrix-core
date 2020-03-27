<?php

namespace Bitrix\MessageService\Sender\Result;

use Bitrix\Main\Result;
use Bitrix\MessageService\MessageStatus;

class SendMessage extends Result
{
    protected $id;
    protected $externalId;
    protected $status;

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = (string)$id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param mixed $externalId
     * @return SendMessage
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return int Status id relative to MessageStatus constants.
     * @see \Bitrix\MessageService\MessageStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status Status id relative to MessageStatus constants.
     * @return SendMessage
     * @see \Bitrix\MessageService\MessageStatus
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Helps us to set most used message status
     * @return $this
     * @see MessageStatus::ACCEPTED
     */
    public function setAccepted()
    {
        $this->setStatus(MessageStatus::ACCEPTED);
        return $this;
    }
}