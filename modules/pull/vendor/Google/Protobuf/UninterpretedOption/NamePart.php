<?php
/**
 * Generated by Protobuf protoc plugin.
 *
 * File descriptor : descriptor.proto
 */


namespace google\protobuf\UninterpretedOption;

/**
 * Protobuf message : google.protobuf.UninterpretedOption.NamePart
 */
class NamePart extends \Protobuf\AbstractMessage
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
     * name_part required string = 1
     *
     * @var string
     */
    protected $name_part = null;

    /**
     * is_extension required bool = 2
     *
     * @var bool
     */
    protected $is_extension = null;

    /**
     * Check if 'name_part' has a value
     *
     * @return bool
     */
    public function hasNamePart()
    {
        return $this->name_part !== null;
    }

    /**
     * Get 'name_part' value
     *
     * @return string
     */
    public function getNamePart()
    {
        return $this->name_part;
    }

    /**
     * Set 'name_part' value
     *
     * @param string $value
     */
    public function setNamePart($value)
    {
        $this->name_part = $value;
    }

    /**
     * Check if 'is_extension' has a value
     *
     * @return bool
     */
    public function hasIsExtension()
    {
        return $this->is_extension !== null;
    }

    /**
     * Get 'is_extension' value
     *
     * @return bool
     */
    public function getIsExtension()
    {
        return $this->is_extension;
    }

    /**
     * Set 'is_extension' value
     *
     * @param bool $value
     */
    public function setIsExtension($value)
    {
        $this->is_extension = $value;
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
        if (!isset($values['name_part'])) {
            throw new \InvalidArgumentException('Field "name_part" (tag 1) is required but has no value.');
        }

        if (!isset($values['is_extension'])) {
            throw new \InvalidArgumentException('Field "is_extension" (tag 2) is required but has no value.');
        }

        $message = new self();
        $values = array_merge([
        ], $values);

        $message->setNamePart($values['name_part']);
        $message->setIsExtension($values['is_extension']);

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public static function descriptor()
    {
        return \google\protobuf\DescriptorProto::fromArray([
            'name' => 'NamePart',
            'field' => [
                \google\protobuf\FieldDescriptorProto::fromArray([
                    'number' => 1,
                    'name' => 'name_part',
                    'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_STRING(),
                    'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_REQUIRED()
                ]),
                \google\protobuf\FieldDescriptorProto::fromArray([
                    'number' => 2,
                    'name' => 'is_extension',
                    'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_BOOL(),
                    'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_REQUIRED()
                ]),
            ],
        ]);
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

        if ($this->name_part === null) {
            throw new \UnexpectedValueException('Field "\\google\\protobuf\\UninterpretedOption\\NamePart#name_part" (tag 1) is required but has no value.');
        }

        if ($this->is_extension === null) {
            throw new \UnexpectedValueException('Field "\\google\\protobuf\\UninterpretedOption\\NamePart#is_extension" (tag 2) is required but has no value.');
        }

        if ($this->name_part !== null) {
            $writer->writeVarint($stream, 10);
            $writer->writeString($stream, $this->name_part);
        }

        if ($this->is_extension !== null) {
            $writer->writeVarint($stream, 16);
            $writer->writeBool($stream, $this->is_extension);
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
                \Protobuf\WireFormat::assertWireType($wire, 9);

                $this->name_part = $reader->readString($stream);

                continue;
            }

            if ($tag === 2) {
                \Protobuf\WireFormat::assertWireType($wire, 8);

                $this->is_extension = $reader->readBool($stream);

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

        if ($this->name_part !== null) {
            $size += 1;
            $size += $calculator->computeStringSize($this->name_part);
        }

        if ($this->is_extension !== null) {
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
        $this->name_part = null;
        $this->is_extension = null;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(\Protobuf\Message $message)
    {
        if (!$message instanceof \google\protobuf\UninterpretedOption\NamePart) {
            throw new \InvalidArgumentException(sprintf('Argument 1 passed to %s must be a %s, %s given', __METHOD__, __CLASS__, get_class($message)));
        }

        $this->name_part = ($message->name_part !== null) ? $message->name_part : $this->name_part;
        $this->is_extension = ($message->is_extension !== null) ? $message->is_extension : $this->is_extension;
    }


}

