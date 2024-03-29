<?php
/**
 * Generated by Protobuf protoc plugin.
 *
 * File descriptor : descriptor.proto
 */


namespace google\protobuf;

/**
 * Protobuf message : google.protobuf.FileOptions
 */
class FileOptions extends \Protobuf\AbstractMessage
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
     * java_package optional string = 1
     *
     * @var string
     */
    protected $java_package = null;

    /**
     * java_outer_classname optional string = 8
     *
     * @var string
     */
    protected $java_outer_classname = null;

    /**
     * java_multiple_files optional bool = 10
     *
     * @var bool
     */
    protected $java_multiple_files = null;

    /**
     * java_generate_equals_and_hash optional bool = 20
     *
     * @var bool
     */
    protected $java_generate_equals_and_hash = null;

    /**
     * java_string_check_utf8 optional bool = 27
     *
     * @var bool
     */
    protected $java_string_check_utf8 = null;

    /**
     * optimize_for optional enum = 9
     *
     * @var \google\protobuf\FileOptions\OptimizeMode
     */
    protected $optimize_for = null;

    /**
     * go_package optional string = 11
     *
     * @var string
     */
    protected $go_package = null;

    /**
     * cc_generic_services optional bool = 16
     *
     * @var bool
     */
    protected $cc_generic_services = null;

    /**
     * java_generic_services optional bool = 17
     *
     * @var bool
     */
    protected $java_generic_services = null;

    /**
     * py_generic_services optional bool = 18
     *
     * @var bool
     */
    protected $py_generic_services = null;

    /**
     * deprecated optional bool = 23
     *
     * @var bool
     */
    protected $deprecated = null;

    /**
     * cc_enable_arenas optional bool = 31
     *
     * @var bool
     */
    protected $cc_enable_arenas = null;

    /**
     * objc_class_prefix optional string = 36
     *
     * @var string
     */
    protected $objc_class_prefix = null;

    /**
     * csharp_namespace optional string = 37
     *
     * @var string
     */
    protected $csharp_namespace = null;

    /**
     * javanano_use_deprecated_package optional bool = 38
     *
     * @var bool
     */
    protected $javanano_use_deprecated_package = null;

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
        $this->java_multiple_files = false;
        $this->java_generate_equals_and_hash = false;
        $this->java_string_check_utf8 = false;
        $this->optimize_for = \google\protobuf\FileOptions\OptimizeMode::SPEED();
        $this->cc_generic_services = false;
        $this->java_generic_services = false;
        $this->py_generic_services = false;
        $this->deprecated = false;
        $this->cc_enable_arenas = false;

        parent::__construct($stream, $configuration);
    }

    /**
     * Check if 'java_package' has a value
     *
     * @return bool
     */
    public function hasJavaPackage()
    {
        return $this->java_package !== null;
    }

    /**
     * Get 'java_package' value
     *
     * @return string
     */
    public function getJavaPackage()
    {
        return $this->java_package;
    }

    /**
     * Set 'java_package' value
     *
     * @param string $value
     */
    public function setJavaPackage($value = null)
    {
        $this->java_package = $value;
    }

    /**
     * Check if 'java_outer_classname' has a value
     *
     * @return bool
     */
    public function hasJavaOuterClassname()
    {
        return $this->java_outer_classname !== null;
    }

    /**
     * Get 'java_outer_classname' value
     *
     * @return string
     */
    public function getJavaOuterClassname()
    {
        return $this->java_outer_classname;
    }

    /**
     * Set 'java_outer_classname' value
     *
     * @param string $value
     */
    public function setJavaOuterClassname($value = null)
    {
        $this->java_outer_classname = $value;
    }

    /**
     * Check if 'java_multiple_files' has a value
     *
     * @return bool
     */
    public function hasJavaMultipleFiles()
    {
        return $this->java_multiple_files !== null;
    }

    /**
     * Get 'java_multiple_files' value
     *
     * @return bool
     */
    public function getJavaMultipleFiles()
    {
        return $this->java_multiple_files;
    }

    /**
     * Set 'java_multiple_files' value
     *
     * @param bool $value
     */
    public function setJavaMultipleFiles($value = null)
    {
        $this->java_multiple_files = $value;
    }

    /**
     * Check if 'java_generate_equals_and_hash' has a value
     *
     * @return bool
     */
    public function hasJavaGenerateEqualsAndHash()
    {
        return $this->java_generate_equals_and_hash !== null;
    }

    /**
     * Get 'java_generate_equals_and_hash' value
     *
     * @return bool
     */
    public function getJavaGenerateEqualsAndHash()
    {
        return $this->java_generate_equals_and_hash;
    }

    /**
     * Set 'java_generate_equals_and_hash' value
     *
     * @param bool $value
     */
    public function setJavaGenerateEqualsAndHash($value = null)
    {
        $this->java_generate_equals_and_hash = $value;
    }

    /**
     * Check if 'java_string_check_utf8' has a value
     *
     * @return bool
     */
    public function hasJavaStringCheckUtf8()
    {
        return $this->java_string_check_utf8 !== null;
    }

    /**
     * Get 'java_string_check_utf8' value
     *
     * @return bool
     */
    public function getJavaStringCheckUtf8()
    {
        return $this->java_string_check_utf8;
    }

    /**
     * Set 'java_string_check_utf8' value
     *
     * @param bool $value
     */
    public function setJavaStringCheckUtf8($value = null)
    {
        $this->java_string_check_utf8 = $value;
    }

    /**
     * Check if 'optimize_for' has a value
     *
     * @return bool
     */
    public function hasOptimizeFor()
    {
        return $this->optimize_for !== null;
    }

    /**
     * Get 'optimize_for' value
     *
     * @return \google\protobuf\FileOptions\OptimizeMode
     */
    public function getOptimizeFor()
    {
        return $this->optimize_for;
    }

    /**
     * Set 'optimize_for' value
     *
     * @param \google\protobuf\FileOptions\OptimizeMode $value
     */
    public function setOptimizeFor(\google\protobuf\FileOptions\OptimizeMode $value = null)
    {
        $this->optimize_for = $value;
    }

    /**
     * Check if 'go_package' has a value
     *
     * @return bool
     */
    public function hasGoPackage()
    {
        return $this->go_package !== null;
    }

    /**
     * Get 'go_package' value
     *
     * @return string
     */
    public function getGoPackage()
    {
        return $this->go_package;
    }

    /**
     * Set 'go_package' value
     *
     * @param string $value
     */
    public function setGoPackage($value = null)
    {
        $this->go_package = $value;
    }

    /**
     * Check if 'cc_generic_services' has a value
     *
     * @return bool
     */
    public function hasCcGenericServices()
    {
        return $this->cc_generic_services !== null;
    }

    /**
     * Get 'cc_generic_services' value
     *
     * @return bool
     */
    public function getCcGenericServices()
    {
        return $this->cc_generic_services;
    }

    /**
     * Set 'cc_generic_services' value
     *
     * @param bool $value
     */
    public function setCcGenericServices($value = null)
    {
        $this->cc_generic_services = $value;
    }

    /**
     * Check if 'java_generic_services' has a value
     *
     * @return bool
     */
    public function hasJavaGenericServices()
    {
        return $this->java_generic_services !== null;
    }

    /**
     * Get 'java_generic_services' value
     *
     * @return bool
     */
    public function getJavaGenericServices()
    {
        return $this->java_generic_services;
    }

    /**
     * Set 'java_generic_services' value
     *
     * @param bool $value
     */
    public function setJavaGenericServices($value = null)
    {
        $this->java_generic_services = $value;
    }

    /**
     * Check if 'py_generic_services' has a value
     *
     * @return bool
     */
    public function hasPyGenericServices()
    {
        return $this->py_generic_services !== null;
    }

    /**
     * Get 'py_generic_services' value
     *
     * @return bool
     */
    public function getPyGenericServices()
    {
        return $this->py_generic_services;
    }

    /**
     * Set 'py_generic_services' value
     *
     * @param bool $value
     */
    public function setPyGenericServices($value = null)
    {
        $this->py_generic_services = $value;
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
     * Check if 'cc_enable_arenas' has a value
     *
     * @return bool
     */
    public function hasCcEnableArenas()
    {
        return $this->cc_enable_arenas !== null;
    }

    /**
     * Get 'cc_enable_arenas' value
     *
     * @return bool
     */
    public function getCcEnableArenas()
    {
        return $this->cc_enable_arenas;
    }

    /**
     * Set 'cc_enable_arenas' value
     *
     * @param bool $value
     */
    public function setCcEnableArenas($value = null)
    {
        $this->cc_enable_arenas = $value;
    }

    /**
     * Check if 'objc_class_prefix' has a value
     *
     * @return bool
     */
    public function hasObjcClassPrefix()
    {
        return $this->objc_class_prefix !== null;
    }

    /**
     * Get 'objc_class_prefix' value
     *
     * @return string
     */
    public function getObjcClassPrefix()
    {
        return $this->objc_class_prefix;
    }

    /**
     * Set 'objc_class_prefix' value
     *
     * @param string $value
     */
    public function setObjcClassPrefix($value = null)
    {
        $this->objc_class_prefix = $value;
    }

    /**
     * Check if 'csharp_namespace' has a value
     *
     * @return bool
     */
    public function hasCsharpNamespace()
    {
        return $this->csharp_namespace !== null;
    }

    /**
     * Get 'csharp_namespace' value
     *
     * @return string
     */
    public function getCsharpNamespace()
    {
        return $this->csharp_namespace;
    }

    /**
     * Set 'csharp_namespace' value
     *
     * @param string $value
     */
    public function setCsharpNamespace($value = null)
    {
        $this->csharp_namespace = $value;
    }

    /**
     * Check if 'javanano_use_deprecated_package' has a value
     *
     * @return bool
     */
    public function hasJavananoUseDeprecatedPackage()
    {
        return $this->javanano_use_deprecated_package !== null;
    }

    /**
     * Get 'javanano_use_deprecated_package' value
     *
     * @return bool
     */
    public function getJavananoUseDeprecatedPackage()
    {
        return $this->javanano_use_deprecated_package;
    }

    /**
     * Set 'javanano_use_deprecated_package' value
     *
     * @param bool $value
     */
    public function setJavananoUseDeprecatedPackage($value = null)
    {
        $this->javanano_use_deprecated_package = $value;
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
     * @param \Protobuf\Collection<\google\protobuf\UninterpretedOption> $value
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
                'java_package' => null,
                'java_outer_classname' => null,
                'java_multiple_files' => false,
                'java_generate_equals_and_hash' => false,
                'java_string_check_utf8' => false,
                'optimize_for' => \google\protobuf\FileOptions\OptimizeMode::SPEED(),
                'go_package' => null,
                'cc_generic_services' => false,
                'java_generic_services' => false,
                'py_generic_services' => false,
                'deprecated' => false,
                'cc_enable_arenas' => false,
                'objc_class_prefix' => null,
                'csharp_namespace' => null,
                'javanano_use_deprecated_package' => null,
                'uninterpreted_option' => []
            ],
            $values
        );

        $message->setJavaPackage($values['java_package']);
        $message->setJavaOuterClassname($values['java_outer_classname']);
        $message->setJavaMultipleFiles($values['java_multiple_files']);
        $message->setJavaGenerateEqualsAndHash($values['java_generate_equals_and_hash']);
        $message->setJavaStringCheckUtf8($values['java_string_check_utf8']);
        $message->setOptimizeFor($values['optimize_for']);
        $message->setGoPackage($values['go_package']);
        $message->setCcGenericServices($values['cc_generic_services']);
        $message->setJavaGenericServices($values['java_generic_services']);
        $message->setPyGenericServices($values['py_generic_services']);
        $message->setDeprecated($values['deprecated']);
        $message->setCcEnableArenas($values['cc_enable_arenas']);
        $message->setObjcClassPrefix($values['objc_class_prefix']);
        $message->setCsharpNamespace($values['csharp_namespace']);
        $message->setJavananoUseDeprecatedPackage($values['javanano_use_deprecated_package']);

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
        return \google\protobuf\DescriptorProto::fromArray(
            [
                'name' => 'FileOptions',
                'field' => [
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 1,
                            'name' => 'java_package',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_STRING(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL()
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 8,
                            'name' => 'java_outer_classname',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_STRING(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL()
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 10,
                            'name' => 'java_multiple_files',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_BOOL(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL(),
                            'default_value' => false
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 20,
                            'name' => 'java_generate_equals_and_hash',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_BOOL(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL(),
                            'default_value' => false
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 27,
                            'name' => 'java_string_check_utf8',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_BOOL(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL(),
                            'default_value' => false
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 9,
                            'name' => 'optimize_for',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_ENUM(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL(),
                            'type_name' => '.google.protobuf.FileOptions.OptimizeMode',
                            'default_value' => \google\protobuf\FileOptions\OptimizeMode::SPEED()
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 11,
                            'name' => 'go_package',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_STRING(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL()
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 16,
                            'name' => 'cc_generic_services',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_BOOL(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL(),
                            'default_value' => false
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 17,
                            'name' => 'java_generic_services',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_BOOL(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL(),
                            'default_value' => false
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 18,
                            'name' => 'py_generic_services',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_BOOL(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL(),
                            'default_value' => false
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 23,
                            'name' => 'deprecated',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_BOOL(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL(),
                            'default_value' => false
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 31,
                            'name' => 'cc_enable_arenas',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_BOOL(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL(),
                            'default_value' => false
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 36,
                            'name' => 'objc_class_prefix',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_STRING(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL()
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 37,
                            'name' => 'csharp_namespace',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_STRING(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL()
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 38,
                            'name' => 'javanano_use_deprecated_package',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_BOOL(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_OPTIONAL()
                        ]
                    ),
                    \google\protobuf\FieldDescriptorProto::fromArray(
                        [
                            'number' => 999,
                            'name' => 'uninterpreted_option',
                            'type' => \google\protobuf\FieldDescriptorProto\Type::TYPE_MESSAGE(),
                            'label' => \google\protobuf\FieldDescriptorProto\Label::LABEL_REPEATED(),
                            'type_name' => '.google.protobuf.UninterpretedOption'
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

        if ($this->java_package !== null) {
            $writer->writeVarint($stream, 10);
            $writer->writeString($stream, $this->java_package);
        }

        if ($this->java_outer_classname !== null) {
            $writer->writeVarint($stream, 66);
            $writer->writeString($stream, $this->java_outer_classname);
        }

        if ($this->java_multiple_files !== null) {
            $writer->writeVarint($stream, 80);
            $writer->writeBool($stream, $this->java_multiple_files);
        }

        if ($this->java_generate_equals_and_hash !== null) {
            $writer->writeVarint($stream, 160);
            $writer->writeBool($stream, $this->java_generate_equals_and_hash);
        }

        if ($this->java_string_check_utf8 !== null) {
            $writer->writeVarint($stream, 216);
            $writer->writeBool($stream, $this->java_string_check_utf8);
        }

        if ($this->optimize_for !== null) {
            $writer->writeVarint($stream, 72);
            $writer->writeVarint($stream, $this->optimize_for->value());
        }

        if ($this->go_package !== null) {
            $writer->writeVarint($stream, 90);
            $writer->writeString($stream, $this->go_package);
        }

        if ($this->cc_generic_services !== null) {
            $writer->writeVarint($stream, 128);
            $writer->writeBool($stream, $this->cc_generic_services);
        }

        if ($this->java_generic_services !== null) {
            $writer->writeVarint($stream, 136);
            $writer->writeBool($stream, $this->java_generic_services);
        }

        if ($this->py_generic_services !== null) {
            $writer->writeVarint($stream, 144);
            $writer->writeBool($stream, $this->py_generic_services);
        }

        if ($this->deprecated !== null) {
            $writer->writeVarint($stream, 184);
            $writer->writeBool($stream, $this->deprecated);
        }

        if ($this->cc_enable_arenas !== null) {
            $writer->writeVarint($stream, 248);
            $writer->writeBool($stream, $this->cc_enable_arenas);
        }

        if ($this->objc_class_prefix !== null) {
            $writer->writeVarint($stream, 290);
            $writer->writeString($stream, $this->objc_class_prefix);
        }

        if ($this->csharp_namespace !== null) {
            $writer->writeVarint($stream, 298);
            $writer->writeString($stream, $this->csharp_namespace);
        }

        if ($this->javanano_use_deprecated_package !== null) {
            $writer->writeVarint($stream, 304);
            $writer->writeBool($stream, $this->javanano_use_deprecated_package);
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

            $key = $reader->readVarint($stream);
            $wire = \Protobuf\WireFormat::getTagWireType($key);
            $tag = \Protobuf\WireFormat::getTagFieldNumber($key);

            if ($stream->eof()) {
                break;
            }

            if ($tag === 1) {
                \Protobuf\WireFormat::assertWireType($wire, 9);

                $this->java_package = $reader->readString($stream);

                continue;
            }

            if ($tag === 8) {
                \Protobuf\WireFormat::assertWireType($wire, 9);

                $this->java_outer_classname = $reader->readString($stream);

                continue;
            }

            if ($tag === 10) {
                \Protobuf\WireFormat::assertWireType($wire, 8);

                $this->java_multiple_files = $reader->readBool($stream);

                continue;
            }

            if ($tag === 20) {
                \Protobuf\WireFormat::assertWireType($wire, 8);

                $this->java_generate_equals_and_hash = $reader->readBool($stream);

                continue;
            }

            if ($tag === 27) {
                \Protobuf\WireFormat::assertWireType($wire, 8);

                $this->java_string_check_utf8 = $reader->readBool($stream);

                continue;
            }

            if ($tag === 9) {
                \Protobuf\WireFormat::assertWireType($wire, 14);

                $this->optimize_for = \google\protobuf\FileOptions\OptimizeMode::valueOf($reader->readVarint($stream));

                continue;
            }

            if ($tag === 11) {
                \Protobuf\WireFormat::assertWireType($wire, 9);

                $this->go_package = $reader->readString($stream);

                continue;
            }

            if ($tag === 16) {
                \Protobuf\WireFormat::assertWireType($wire, 8);

                $this->cc_generic_services = $reader->readBool($stream);

                continue;
            }

            if ($tag === 17) {
                \Protobuf\WireFormat::assertWireType($wire, 8);

                $this->java_generic_services = $reader->readBool($stream);

                continue;
            }

            if ($tag === 18) {
                \Protobuf\WireFormat::assertWireType($wire, 8);

                $this->py_generic_services = $reader->readBool($stream);

                continue;
            }

            if ($tag === 23) {
                \Protobuf\WireFormat::assertWireType($wire, 8);

                $this->deprecated = $reader->readBool($stream);

                continue;
            }

            if ($tag === 31) {
                \Protobuf\WireFormat::assertWireType($wire, 8);

                $this->cc_enable_arenas = $reader->readBool($stream);

                continue;
            }

            if ($tag === 36) {
                \Protobuf\WireFormat::assertWireType($wire, 9);

                $this->objc_class_prefix = $reader->readString($stream);

                continue;
            }

            if ($tag === 37) {
                \Protobuf\WireFormat::assertWireType($wire, 9);

                $this->csharp_namespace = $reader->readString($stream);

                continue;
            }

            if ($tag === 38) {
                \Protobuf\WireFormat::assertWireType($wire, 8);

                $this->javanano_use_deprecated_package = $reader->readBool($stream);

                continue;
            }

            if ($tag === 999) {
                \Protobuf\WireFormat::assertWireType($wire, 11);

                $innerSize = $reader->readVarint($stream);
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

        if ($this->java_package !== null) {
            $size += 1;
            $size += $calculator->computeStringSize($this->java_package);
        }

        if ($this->java_outer_classname !== null) {
            $size += 1;
            $size += $calculator->computeStringSize($this->java_outer_classname);
        }

        if ($this->java_multiple_files !== null) {
            $size += 1;
            $size += 1;
        }

        if ($this->java_generate_equals_and_hash !== null) {
            $size += 2;
            $size += 1;
        }

        if ($this->java_string_check_utf8 !== null) {
            $size += 2;
            $size += 1;
        }

        if ($this->optimize_for !== null) {
            $size += 1;
            $size += $calculator->computeVarintSize($this->optimize_for->value());
        }

        if ($this->go_package !== null) {
            $size += 1;
            $size += $calculator->computeStringSize($this->go_package);
        }

        if ($this->cc_generic_services !== null) {
            $size += 2;
            $size += 1;
        }

        if ($this->java_generic_services !== null) {
            $size += 2;
            $size += 1;
        }

        if ($this->py_generic_services !== null) {
            $size += 2;
            $size += 1;
        }

        if ($this->deprecated !== null) {
            $size += 2;
            $size += 1;
        }

        if ($this->cc_enable_arenas !== null) {
            $size += 2;
            $size += 1;
        }

        if ($this->objc_class_prefix !== null) {
            $size += 2;
            $size += $calculator->computeStringSize($this->objc_class_prefix);
        }

        if ($this->csharp_namespace !== null) {
            $size += 2;
            $size += $calculator->computeStringSize($this->csharp_namespace);
        }

        if ($this->javanano_use_deprecated_package !== null) {
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
        $this->java_package = null;
        $this->java_outer_classname = null;
        $this->java_multiple_files = false;
        $this->java_generate_equals_and_hash = false;
        $this->java_string_check_utf8 = false;
        $this->optimize_for = \google\protobuf\FileOptions\OptimizeMode::SPEED();
        $this->go_package = null;
        $this->cc_generic_services = false;
        $this->java_generic_services = false;
        $this->py_generic_services = false;
        $this->deprecated = false;
        $this->cc_enable_arenas = false;
        $this->objc_class_prefix = null;
        $this->csharp_namespace = null;
        $this->javanano_use_deprecated_package = null;
        $this->uninterpreted_option = null;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(\Protobuf\Message $message)
    {
        if (!$message instanceof \google\protobuf\FileOptions) {
            throw new \InvalidArgumentException(
                sprintf('Argument 1 passed to %s must be a %s, %s given', __METHOD__, __CLASS__, get_class($message))
            );
        }

        $this->java_package = ($message->java_package !== null) ? $message->java_package : $this->java_package;
        $this->java_outer_classname = ($message->java_outer_classname !== null) ? $message->java_outer_classname : $this->java_outer_classname;
        $this->java_multiple_files = ($message->java_multiple_files !== null) ? $message->java_multiple_files : $this->java_multiple_files;
        $this->java_generate_equals_and_hash = ($message->java_generate_equals_and_hash !== null) ? $message->java_generate_equals_and_hash : $this->java_generate_equals_and_hash;
        $this->java_string_check_utf8 = ($message->java_string_check_utf8 !== null) ? $message->java_string_check_utf8 : $this->java_string_check_utf8;
        $this->optimize_for = ($message->optimize_for !== null) ? $message->optimize_for : $this->optimize_for;
        $this->go_package = ($message->go_package !== null) ? $message->go_package : $this->go_package;
        $this->cc_generic_services = ($message->cc_generic_services !== null) ? $message->cc_generic_services : $this->cc_generic_services;
        $this->java_generic_services = ($message->java_generic_services !== null) ? $message->java_generic_services : $this->java_generic_services;
        $this->py_generic_services = ($message->py_generic_services !== null) ? $message->py_generic_services : $this->py_generic_services;
        $this->deprecated = ($message->deprecated !== null) ? $message->deprecated : $this->deprecated;
        $this->cc_enable_arenas = ($message->cc_enable_arenas !== null) ? $message->cc_enable_arenas : $this->cc_enable_arenas;
        $this->objc_class_prefix = ($message->objc_class_prefix !== null) ? $message->objc_class_prefix : $this->objc_class_prefix;
        $this->csharp_namespace = ($message->csharp_namespace !== null) ? $message->csharp_namespace : $this->csharp_namespace;
        $this->javanano_use_deprecated_package = ($message->javanano_use_deprecated_package !== null) ? $message->javanano_use_deprecated_package : $this->javanano_use_deprecated_package;
        $this->uninterpreted_option = ($message->uninterpreted_option !== null) ? $message->uninterpreted_option : $this->uninterpreted_option;
    }


}

