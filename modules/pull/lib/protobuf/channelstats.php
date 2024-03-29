<?

namespace Bitrix\Pull\Protobuf;

/**
 * Generated by Protobuf protoc plugin.
 *
 * File descriptor : response.proto
 */


/**
 * Protobuf message : ChannelStats
 */
class ChannelStats extends \Protobuf\AbstractMessage
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
     * id optional bytes = 1
     *
     * @var \Protobuf\Stream
     */
    protected $id = null;

    /**
     * isPrivate optional bool = 2
     *
     * @var bool
     */
    protected $isPrivate = null;

    /**
     * isOnline optional bool = 3
     *
     * @var bool
     */
    protected $isOnline = null;

    /**
     * Check if 'id' has a value
     *
     * @return bool
     */
    public function hasId()
    {
        return $this->id !== null;
    }

    /**
     * Get 'id' value
     *
     * @return \Protobuf\Stream
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set 'id' value
     *
     * @param \Protobuf\Stream $value
     */
    public function setId($value = null)
    {
        if ($value !== null && !$value instanceof \Protobuf\Stream) {
            $value = \Protobuf\Stream::wrap($value);
        }

        $this->id = $value;
    }

    /**
     * Check if 'isPrivate' has a value
     *
     * @return bool
     */
    public function hasIsPrivate()
    {
        return $this->isPrivate !== null;
    }

    /**
     * Get 'isPrivate' value
     *
     * @return bool
     */
    public function getIsPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * Set 'isPrivate' value
     *
     * @param bool $value
     */
    public function setIsPrivate($value = null)
    {
        $this->isPrivate = $value;
    }

    /**
     * Check if 'isOnline' has a value
     *
     * @return bool
     */
    public function hasIsOnline()
    {
        return $this->isOnline !== null;
    }

    /**
     * Get 'isOnline' value
     *
     * @return bool
     */
    public function getIsOnline()
    {
        return $this->isOnline;
    }

    /**
     * Set 'isOnline' value
     *
     * @param bool $value
     */
    public function setIsOnline($value = null)
    {
        $this->isOnline = $value;
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
                'id' => null,
                'isPrivate' => null,
                'isOnline' => null
            ],
            $values
        );

        $message->setId($values['id']);
        $message->setIsPrivate($values['isPrivate']);
        $message->setIsOnline($values['isOnline']);

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public static function descriptor()
    {
        return \google\protobuf\DescriptorProto::fromArray(
            [
                'name' => 'ChannelStats',
                'field' => [
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 1,
                            'name' => 'id',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_BYTES(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL()
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 2,
                            'name' => 'isPrivate',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_BOOL(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL()
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 3,
                            'name' => 'isOnline',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_BOOL(),
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

        if ($this->id !== null) {
            $writer->writeVarint($stream, 10);
            $writer->writeByteStream($stream, $this->id);
        }

        if ($this->isPrivate !== null) {
            $writer->writeVarint($stream, 16);
            $writer->writeBool($stream, $this->isPrivate);
        }

        if ($this->isOnline !== null) {
            $writer->writeVarint($stream, 24);
            $writer->writeBool($stream, $this->isOnline);
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
                \Protobuf\WireFormat::assertWireType($wire, 12);

                $this->id = $reader->readByteStream($stream);

                continue;
            }

            if ($tag === 2) {
                \Protobuf\WireFormat::assertWireType($wire, 8);

                $this->isPrivate = $reader->readBool($stream);

                continue;
            }

            if ($tag === 3) {
                \Protobuf\WireFormat::assertWireType($wire, 8);

                $this->isOnline = $reader->readBool($stream);

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

        if ($this->id !== null) {
            $size += 1;
            $size += $calculator->computeByteStreamSize($this->id);
        }

        if ($this->isPrivate !== null) {
            $size += 1;
            $size += 1;
        }

        if ($this->isOnline !== null) {
            $size += 1;
            $size += 1;
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
        $this->id = null;
        $this->isPrivate = null;
        $this->isOnline = null;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(\Protobuf\Message $message)
    {
        if (!$message instanceof \Bitrix\Pull\Protobuf\ChannelStats) {
            throw new \InvalidArgumentException(
                sprintf('Argument 1 passed to %s must be a %s, %s given', __METHOD__, __CLASS__, get_class($message))
            );
        }

        $this->id = ($message->id !== null) ? $message->id : $this->id;
        $this->isPrivate = ($message->isPrivate !== null) ? $message->isPrivate : $this->isPrivate;
        $this->isOnline = ($message->isOnline !== null) ? $message->isOnline : $this->isOnline;
    }


}

