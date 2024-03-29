<?

namespace Bitrix\Pull\Protobuf;

/**
 * Generated by Protobuf protoc plugin.
 *
 * File descriptor : request.proto
 */


/**
 * Protobuf message : IncomingMessage
 */
class IncomingMessage extends \Protobuf\AbstractMessage
{

    /**
     * @var \Protobuf\UnknownFieldSet
     */
    protected $unknownFieldSet = null;

    /**
     * @var \Protobuf\Extension\ExtensionFieldMap
     */
    protected $extensions = null;

    /**
     * receivers repeated message = 1
     *
     * @var \Protobuf\Collection<\Bitrix\Pull\Protobuf\Receiver>
     */
    protected $receivers = null;

    /**
     * sender optional message = 2
     *
     * @var \Bitrix\Pull\Protobuf\Sender
     */
    protected $sender = null;

    /**
     * body optional string = 3
     *
     * @var string
     */
    protected $body = null;

    /**
     * expiry optional uint32 = 4
     *
     * @var int
     */
    protected $expiry = null;

    /**
     * type optional string = 5
     *
     * @var string
     */
    protected $type = null;

    /**
     * Check if 'receivers' has a value
     *
     * @return bool
     */
    public function hasReceiversList()
    {
        return $this->receivers !== null;
    }

    /**
     * Get 'receivers' value
     *
     * @return \Protobuf\Collection<\Bitrix\Pull\Protobuf\Receiver>
     */
    public function getReceiversList()
    {
        return $this->receivers;
    }

    /**
     * Set 'receivers' value
     *
     * @param \Protobuf\Collection<\Bitrix\Pull\Protobuf\Receiver> $value
     */
    public function setReceiversList(\Protobuf\Collection $value = null)
    {
        $this->receivers = $value;
    }

    /**
     * Add a new element to 'receivers'
     *
     * @param \Bitrix\Pull\Protobuf\Receiver $value
     */
    public function addReceivers(\Bitrix\Pull\Protobuf\Receiver $value)
    {
        if ($this->receivers === null) {
            $this->receivers = new \Protobuf\MessageCollection();
        }

        $this->receivers->add($value);
    }

    /**
     * Check if 'sender' has a value
     *
     * @return bool
     */
    public function hasSender()
    {
        return $this->sender !== null;
    }

    /**
     * Get 'sender' value
     *
     * @return \Bitrix\Pull\Protobuf\Sender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set 'sender' value
     *
     * @param \Bitrix\Pull\Protobuf\Sender $value
     */
    public function setSender(\Bitrix\Pull\Protobuf\Sender $value = null)
    {
        $this->sender = $value;
    }

    /**
     * Check if 'body' has a value
     *
     * @return bool
     */
    public function hasBody()
    {
        return $this->body !== null;
    }

    /**
     * Get 'body' value
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set 'body' value
     *
     * @param string $value
     */
    public function setBody($value = null)
    {
        $this->body = $value;
    }

    /**
     * Check if 'expiry' has a value
     *
     * @return bool
     */
    public function hasExpiry()
    {
        return $this->expiry !== null;
    }

    /**
     * Get 'expiry' value
     *
     * @return int
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Set 'expiry' value
     *
     * @param int $value
     */
    public function setExpiry($value = null)
    {
        $this->expiry = $value;
    }

    /**
     * Check if 'type' has a value
     *
     * @return bool
     */
    public function hasType()
    {
        return $this->type !== null;
    }

    /**
     * Get 'type' value
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set 'type' value
     *
     * @param string $value
     */
    public function setType($value = null)
    {
        $this->type = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function extensions()
    {
        if ($this->extensions !== null) {
            return $this->extensions;
        }

        return $this->extensions = new \Protobuf\Extension\ExtensionFieldMap(__CLASS__);
    }

    /**
     * {@inheritdoc}
     */
    public function unknownFieldSet()
    {
        return $this->unknownFieldSet;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromStream($stream, \Protobuf\Configuration $configuration = null)
    {
        return new self($stream, $configuration);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromArray(array $values)
    {
        $message = new self();
        $values = array_merge(
            [
                'receivers' => [],
                'sender' => null,
                'body' => null,
                'expiry' => null,
                'type' => null
            ],
            $values
        );

        $message->setSender($values['sender']);
        $message->setBody($values['body']);
        $message->setExpiry($values['expiry']);
        $message->setType($values['type']);

        foreach ($values['receivers'] as $item) {
            $message->addReceivers($item);
        }

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public static function descriptor()
    {
        return \google\protobuf\DescriptorProto::fromArray(
            [
                'name' => 'IncomingMessage',
                'field' => [
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 1,
                            'name' => 'receivers',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_MESSAGE(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_REPEATED(),
                            'type_name' => '.Receiver'
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 2,
                            'name' => 'sender',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_MESSAGE(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL(),
                            'type_name' => '.Sender'
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 3,
                            'name' => 'body',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_STRING(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL()
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 4,
                            'name' => 'expiry',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_UINT32(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL()
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 5,
                            'name' => 'type',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_STRING(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL()
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toStream(\Protobuf\Configuration $configuration = null)
    {
        $config = $configuration ?: \Protobuf\Configuration::getInstance();
        $context = $config->createWriteContext();
        $stream = $context->getStream();

        $this->writeTo($context);
        $stream->seek(0);

        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function writeTo(\Protobuf\WriteContext $context)
    {
        $stream = $context->getStream();
        $writer = $context->getWriter();
        $sizeContext = $context->getComputeSizeContext();

        if ($this->receivers !== null) {
            foreach ($this->receivers as $val) {
                $writer->writeVarint($stream, 10);
                $writer->writeVarint($stream, $val->serializedSize($sizeContext));
                $val->writeTo($context);
            }
        }

        if ($this->sender !== null) {
            $writer->writeVarint($stream, 18);
            $writer->writeVarint($stream, $this->sender->serializedSize($sizeContext));
            $this->sender->writeTo($context);
        }

        if ($this->body !== null) {
            $writer->writeVarint($stream, 26);
            $writer->writeString($stream, $this->body);
        }

        if ($this->expiry !== null) {
            $writer->writeVarint($stream, 32);
            $writer->writeVarint($stream, $this->expiry);
        }

        if ($this->type !== null) {
            $writer->writeVarint($stream, 42);
            $writer->writeString($stream, $this->type);
        }

        if ($this->extensions !== null) {
            $this->extensions->writeTo($context);
        }

        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function readFrom(\Protobuf\ReadContext $context)
    {
        $reader = $context->getReader();
        $length = $context->getLength();
        $stream = $context->getStream();

        $limit = ($length !== null)
            ? ($stream->tell() + $length)
            : null;

        while ($limit === null || $stream->tell() < $limit) {
            if ($stream->eof()) {
                break;
            }

            $key = $reader->readVarint($stream);
            $wire = \Protobuf\WireFormat::getTagWireType($key);
            $tag = \Protobuf\WireFormat::getTagFieldNumber($key);

            if ($stream->eof()) {
                break;
            }

            if ($tag === 1) {
                \Protobuf\WireFormat::assertWireType($wire, 11);

                $innerSize = $reader->readVarint($stream);
                $innerMessage = new \Bitrix\Pull\Protobuf\Receiver();

                if ($this->receivers === null) {
                    $this->receivers = new \Protobuf\MessageCollection();
                }

                $this->receivers->add($innerMessage);

                $context->setLength($innerSize);
                $innerMessage->readFrom($context);
                $context->setLength($length);

                continue;
            }

            if ($tag === 2) {
                \Protobuf\WireFormat::assertWireType($wire, 11);

                $innerSize = $reader->readVarint($stream);
                $innerMessage = new \Bitrix\Pull\Protobuf\Sender();

                $this->sender = $innerMessage;

                $context->setLength($innerSize);
                $innerMessage->readFrom($context);
                $context->setLength($length);

                continue;
            }

            if ($tag === 3) {
                \Protobuf\WireFormat::assertWireType($wire, 9);

                $this->body = $reader->readString($stream);

                continue;
            }

            if ($tag === 4) {
                \Protobuf\WireFormat::assertWireType($wire, 13);

                $this->expiry = $reader->readVarint($stream);

                continue;
            }

            if ($tag === 5) {
                \Protobuf\WireFormat::assertWireType($wire, 9);

                $this->type = $reader->readString($stream);

                continue;
            }

            $extensions = $context->getExtensionRegistry();
            $extension = $extensions ? $extensions->findByNumber(__CLASS__, $tag) : null;

            if ($extension !== null) {
                $this->extensions()->add($extension, $extension->readFrom($context, $wire));

                continue;
            }

            if ($this->unknownFieldSet === null) {
                $this->unknownFieldSet = new \Protobuf\UnknownFieldSet();
            }

            $data = $reader->readUnknown($stream, $wire);
            $unknown = new \Protobuf\Unknown($tag, $wire, $data);

            $this->unknownFieldSet->add($unknown);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serializedSize(\Protobuf\ComputeSizeContext $context)
    {
        $calculator = $context->getSizeCalculator();
        $size = 0;

        if ($this->receivers !== null) {
            foreach ($this->receivers as $val) {
                $innerSize = $val->serializedSize($context);

                $size += 1;
                $size += $innerSize;
                $size += $calculator->computeVarintSize($innerSize);
            }
        }

        if ($this->sender !== null) {
            $innerSize = $this->sender->serializedSize($context);

            $size += 1;
            $size += $innerSize;
            $size += $calculator->computeVarintSize($innerSize);
        }

        if ($this->body !== null) {
            $size += 1;
            $size += $calculator->computeStringSize($this->body);
        }

        if ($this->expiry !== null) {
            $size += 1;
            $size += $calculator->computeVarintSize($this->expiry);
        }

        if ($this->type !== null) {
            $size += 1;
            $size += $calculator->computeStringSize($this->type);
        }

        if ($this->extensions !== null) {
            $size += $this->extensions->serializedSize($context);
        }

        return $size;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->receivers = null;
        $this->sender = null;
        $this->body = null;
        $this->expiry = null;
        $this->type = null;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(\Protobuf\Message $message)
    {
        if (!$message instanceof \Bitrix\Pull\Protobuf\IncomingMessage) {
            throw new \InvalidArgumentException(
                sprintf('Argument 1 passed to %s must be a %s, %s given', __METHOD__, __CLASS__, get_class($message))
            );
        }

        $this->receivers = ($message->receivers !== null) ? $message->receivers : $this->receivers;
        $this->sender = ($message->sender !== null) ? $message->sender : $this->sender;
        $this->body = ($message->body !== null) ? $message->body : $this->body;
        $this->expiry = ($message->expiry !== null) ? $message->expiry : $this->expiry;
        $this->type = ($message->type !== null) ? $message->type : $this->type;
    }


}

