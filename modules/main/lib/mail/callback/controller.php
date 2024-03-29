<?php

namespace Bitrix\Main\Mail\Callback;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Context;
use Bitrix\Main\Mail\Address;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Mail\Internal;

/**
 * Class Controller
 *
 * @package Bitrix\Main\Mail\Callback
 */
class Controller
{
    const STATUS_DEFERED = 'defered';
    const STATUS_BOUNCED = 'bounced';
    const STATUS_DELIVERED = 'delivered';

    const DESC_AUTH = 'AUTH_ERROR';
    const DESC_UNKNOWN_USER = 'UNKNOWN_USER';
    const DESC_UNROUTEABLE = 'UNROUTEABLE';

    /** @var  string $id ID of mail. */
    protected $id;

    /** @var  Result $result Result instance. */
    protected $result;

    /** @var  Config $config Config instance. */
    protected $config;

    /** @var  Address $address Address instance. */
    protected $address;

    /** @var string[] $blacklist Black list of emails. */
    protected $blacklist = [];

    /** @var bool $answerExceptions Flush exceptions in answer. */
    protected static $answerExceptions = true;

    /** @var bool $enableItemErrors Ignore item errors. */
    protected $enableItemErrors = false;

    /** @var int $countItems . */
    protected $countItems = 0;
    /** @var int $countItems . */
    protected $countItemsProcessed = 0;
    /** @var int $countItems . */
    protected $countItemsError = 0;

    /**
     * Run controller.
     *
     * @param string $data Data.
     * @param array $parameters Parameters.
     * @return void
     */
    public static function run($data = null, array $parameters = [])
    {
        $request = Context::getCurrent()->getRequest();
        if ($data === null) {
            $data = $request->getPostList()->getRaw('data');
        }
        if (!isset($parameters['IGNORE_ITEM_ERRORS'])) {
            $parameters['ENABLE_ITEM_ERRORS'] = mb_strtoupper($request->get('enableItemErrors')) === 'Y';
        }

        $instance = new self();
        if ($parameters['ENABLE_ITEM_ERRORS']) {
            $instance->enableItemErrors();
        }

        try {
            if (empty($data)) {
                self::giveAnswer(true, 'No input data.');
            }

            try {
                $data = Json::decode($data);
            } catch (\Exception $exception) {
            }

            if (!is_array($data)) {
                self::giveAnswer(true, 'Wrong data.');
            }

            if (!isset($data['list']) || !is_array($data['list'])) {
                self::giveAnswer(true, 'Parameter `list` required.');
            }

            $instance->processList($data['list']);

            self::giveAnswer(false, ['list' => $instance->getCounters()]);
        } catch (SystemException $exception) {
            self::giveAnswer(
                true,
                [
                    'text' => self::$answerExceptions ? $exception->getMessage() : null,
                    'list' => $instance->getCounters()
                ]
            );
        }
    }

    /**
     * Give answer.
     *
     * @param bool $isError Data.
     * @param string|array|null $answer Answer.
     * @return void
     */
    public static function giveAnswer($isError = false, $answer = null)
    {
        $response = Context::getCurrent()->getResponse();
        $response->addHeader('Status', $isError ? '422' : '200');
        $response->addHeader('Content-Type', 'application/json');

        if (!is_array($answer)) {
            $answer = [
                'text' => $answer ?: null
            ];
        }
        $answer['error'] = $isError;
        if (empty($answer['text'])) {
            $answer['text'] = $isError ? 'Unknown error' : 'Success';
        }
        $answer = Json::encode($answer);

        \CMain::FinalActions($answer);
        exit;
    }

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->config = new Config();
        $this->result = new Result();
        $this->address = new Address();
    }

    /**
     * Enable item errors.
     *
     * @return $this
     */
    public function enableItemErrors()
    {
        $this->enableItemErrors = true;
        return $this;
    }

    protected function validateItem($item)
    {
        if (empty($item['id'])) {
            throw new ArgumentException('Field `id` is required for item.');
        }
        if (!preg_match("/[a-zA-Z0-1=]{3,}/", $item['id'])) {
            throw new ArgumentException('Field `id` has disallowed chars.');
        }

        if (empty($item['sign'])) {
            throw new ArgumentException("Field `sign` is required for item with id `{$item['id']}`.");
        }

        if (empty($item['status'])) {
            throw new ArgumentException("Field `status` is required for item with id `{$item['id']}`.");
        }

        if (empty($item['email'])) {
            throw new ArgumentException("Field `email` is required for item with id `{$item['id']}`.");
        }
    }

    /**
     * Process list.
     *
     * @param array $list List of items.
     * @return void
     * @throws SystemException
     */
    public function processList($list)
    {
        $this->countItems = count($list);

        $this->blacklist = [];
        foreach ($list as $index => $item) {
            $this->countItemsProcessed++;
            try {
                $result = $this->processItem($item);
                if (!$result) {
                    $this->countItemsError++;
                }
            } catch (SystemException $exception) {
                $this->countItemsError++;

                if ($this->enableItemErrors) {
                    throw $exception;
                }
            }
        }

        Internal\BlacklistTable::insertBatch($this->blacklist);
    }

    /**
     * Process item.
     *
     * @param array $item Item data.
     * @return bool
     * @throws SystemException
     */
    public function processItem($item)
    {
        $this->validateItem($item);

        $this->config->unpackId($item['id']);
        if (!$this->config->verifySignature($item['sign'])) {
            throw new SystemException('Item parameter `sign` is invalid.');
        }

        if (!$this->config->getEntityId()) {
            return false;
        }

        $email = $this->address->set($item['email'])->getEmail();
        if (!$email) {
            return false;
        }

        $this->result
            ->setModuleId($this->config->getModuleId())
            ->setEntityType($this->config->getEntityType())
            ->setEntityId($this->config->getEntityId())
            ->setEmail($email)
            ->setDateSent((int)$item['completedAt'])
            ->setError(self::isStatusError($item['status']))
            ->setPermanentError(self::isStatusPermanentError($item['status']))
            ->setBlacklistable(self::isBlacklistable($item['statusDescription']))
            ->setDescription($item['statusDescription'])
            ->setMessage($item['message']);

        if ($this->result->isPermanentError() && $this->result->isBlacklistable()) {
            $this->blacklist[] = $this->result->getEmail();
        }

        $this->result->sendEvent();

        return true;
    }

    /**
     * Get counters
     *
     * @return array
     */
    public function getCounters()
    {
        return [
            'all' => $this->countItems,
            'processed' => $this->countItemsProcessed,
            'errors' => $this->countItemsError,
        ];
    }

    /**
     * Return true if status is error.
     *
     * @param string $status Status.
     * @return bool
     */
    public static function isStatusError($status)
    {
        return in_array($status, [self::STATUS_DEFERED, self::STATUS_BOUNCED]);
    }

    /**
     * Return true if status is permanent error.
     *
     * @param string $status Status.
     * @return bool
     */
    public static function isStatusPermanentError($status)
    {
        return $status === self::STATUS_BOUNCED;
    }

    /**
     * Return true if status descriptions is blacklistable.
     *
     * @param string $description Description.
     * @return bool
     */
    public static function isBlacklistable($description)
    {
        return $description && in_array($description, [self::DESC_UNKNOWN_USER, self::DESC_UNROUTEABLE]);
    }
}
