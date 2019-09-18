<?php
/**
 * Generated by Protobuf protoc plugin.
 *
 * File descriptor : descriptor.proto
 */


namespace google\protobuf;

/**
 * Protobuf message : google.protobuf.MethodOptions
 */
class MethodOptions extends \Protobuf\AbstractMessage
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
     * deprecated optional bool = 33
     *
     * @var bool
     */
    protected $deprecated = null;

    /**
     * uninterpreted_option repeated message = 999
     *
     * @var \Protobuf\Collection<\google\protobuf\UninterpretedOption>
     */
    protected $uninterpreted_option = null;

    /**
     * {@inheritdoc}
     */
    public function __construct($stream = null, \Protobuf\Configuration $configuration = null)
    {
        $this->deprecated = false;

        parent::__construct($stream, $configuration);
    }

    /**
     * Check if 'deprecated' has a value
     *
     * @return bool
     */
    public function hasDeprecated()
    {
        return $this->deprecated !== null;
    }

    /**
     * Get 'deprecated' value
     *
     * @return bool
     */
    public function getDeprecated()
    {
        return $this->deprecated;
    }

    /**
     * Set 'deprecated' value
     *
     * @param bool $value
     */
    public function setDeprecated($value = null)
    {
        $this->deprecated = $value;
    }

    /**
     * Check if 'uninterpreted_option' has a value
     *
     * @return bool
     */
    public function hasUninterpretedOptionList()
    {
        return $this->uninterpreted_option !== null;
    }

    /**
     * Get 'uninterpreted_option' value
     *
     * @return \Protobuf\Collection<\google\protobuf\UninterpretedOption>
     */
    public function getUninterpretedOptionList()
    {
        return $this->uninterpreted_option;
    }

    /**
     * Set 'uninterpreted_option' value
     *
     * @param \Protobuf\Collection|null $value
     * @internal param $ \Protobuf\Collection<\google\protobuf\UninterpretedOption> $value
     */
    public function setUninterpretedOptionList(\Protobuf\Collection $value = null)
    {
        $this->uninterpreted_option = $value;
    }

    /**
     * Add a new element to 'uninterpreted_option'
     *
     * @param \google\protobuf\UninterpretedOption $value
     */
    public function addUninterpretedOption(\google\protobuf\UninterpretedOption $value)
    {
        if ($this->uninterpreted_option === null) {
            $this->uninterpreted_option = new \Protobuf\MessageCollection();
        }

        $this->uninterpreted_option->add($value);
    }

    /**
     * {@inheritdoc}
     */
    public function extensions()
    {
        if ( $this->extensions !== null) {
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
        $values  = array_merge([
            'deprecated' => false,
            'uninterpreted_option' => []
        ], $values);

        $message->setDeprecated($values['deprecated']);

        foreach ($values['uninterpreted_option'] as $item) {
            $message->addUninterpretedOption($item);
        }

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public static function descriptor()
    {
        return \google\protobuf\DescriptorProto::fromArray([
            'name'      => 'MethodOptions',
            'field'     => [
                \google\protobuf\FieldDescriptorProto::fromArray([
                    'number' => 33,
                    'name' => 'deprecated',
                    'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_BOOL(),
                    'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL(),
                    'default_value' => false
                ]),
                \google\protobuf\FieldDescriptorProto::fromArray([
                    'number' => 999,
                    'name' => 'uninterpreted_option',
                    'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_MESSAGE(),
                    'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_REPEATED(),
                    'type_name' => '.google.protobuf.UninterpretedOption'
                ]),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toStream(\Protobuf\Configuration $configuration = null)
    {
        $config  = $configuration ?: \Protobuf\Configuration::getInstance();
        $context = $config->createWriteContext();
        $stream  = $context->getStream();

        $this->writeTo($context);
        $stream->seek(0);

        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function writeTo(\Protobuf\WriteContext $context)
    {
        $stream      = $context->getStream();
        $writer      = $context->getWriter();
        $sizeContext = $context->getComputeSizeContext();

        if ($this->deprecated !== null) {
            $writer->writeVarint($stream, 264);
            $writer->writeBool($stream, $this->deprecated);
        }

        if ($this->uninterpreted_option !== null) {
            foreach ($this->uninterpreted_option as $val) {
                $writer->writeVarint($stream, 7994);
                $writer->writeVarint($stream, $val->serializedSize($sizeContext));
                $val->writeTo($context);
            }
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

            $key  = $reader->readVarint($stream);
            $wire = \Protobuf\WireFormat::getTagWireType($key);
            $tag  = \Protobuf\WireFormat::getTagFieldNumber($key);

            if ($stream->eof()) {
                break;
            }

            if ($tag === 33) {
                \Protobuf\WireFormat::assertWireType($wire, 8);

                $this->deprecated = $reader->readBool($stream);

                continue;
            }

            if ($tag === 999) {
                \Protobuf\WireFormat::assertWireType($wire, 11);

                $innerSize    = $reader->readVarint($stream);
                $innerMessage = new \google\protobuf\UninterpretedOption();

                if ($this->uninterpreted_option === null) {
                    $this->uninterpreted_option = new \Protobuf\MessageCollection();
                }

                $this->uninterpreted_option->add($innerMessage);

                $context->setLength($innerSize);
                $innerMessage->readFrom($context);
                $context->setLength($length);

                continue;
            }

            $extensions = $context->getExtensionRegistry();
            $extension  = $extensions ? $extensions->findByNumber(__CLASS__, $tag) : null;

            if ($extension !== null) {
                $this->extensions()->add($extension, $extension->readFrom($context, $wire));

                continue;
            }

            if ($this->unknownFieldSet === null) {
                $this->unknownFieldSet = new \Protobuf\UnknownFieldSet();
            }

            $data    = $reader->readUnknown($stream, $wire);
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
        $size       = 0;

        if ($this->deprecated !== null) {
            $size += 2;
            $size += 1;
        }

        if ($this->uninterpreted_option !== null) {
            foreach ($this->uninterpreted_option as $val) {
                $innerSize = $val->serializedSize($context);

                $size += 2;
                $size += $innerSize;
                $size += $calculator->computeVarintSize($innerSize);
            }
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
        $this->deprecated = false;
        $this->uninterpreted_option = null;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(\Protobuf\Message $message)
    {
        if ( ! $message instanceof \google\protobuf\MethodOptions) {
            throw new \InvalidArgumentException(sprintf('Argument 1 passed to %s must be a %s, %s given', __METHOD__, __CLASS__, get_class($message)));
        }

        $this->deprecated = ($message->deprecated !== null) ? $message->deprecated : $this->deprecated;
        $this->uninterpreted_option = ($message->uninterpreted_option !== null) ? $message->uninterpreted_option : $this->uninterpreted_option;
    }


}

