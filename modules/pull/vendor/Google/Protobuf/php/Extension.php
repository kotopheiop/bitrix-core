<?php
/**
 * Generated by Protobuf protoc plugin.
 *
 * File descriptor : php.proto
 */


namespace google\protobuf\php;

/**
 * Protobuf extension : php.Extension
 */
class Extension implements \Protobuf\Extension
{

    /**
     * Extension field : package optional string = 50002
     *
     * @var \Protobuf\Extension
     */
    protected static $package = null;

    /**
     * Extension field : generic_services optional bool = 50003
     *
     * @var \Protobuf\Extension
     */
    protected static $generic_services = null;

    /**
     * Register all extensions
     *
     * @param \Protobuf\Extension\ExtensionRegistry
     */
    public static function registerAllExtensions(\Protobuf\Extension\ExtensionRegistry $registry)
    {
        $registry->add(self::package());
        $registry->add(self::genericServices());
    }

    /**
     * Extension field : package
     *
     * @return \Protobuf\Extension
     */
    public static function package()
    {
        if (self::$package !== null) {
            return self::$package;
        }

        $readCallback = function (\Protobuf\ReadContext $context, $wire) {
            $reader = $context->getReader();
            $length = $context->getLength();
            $stream = $context->getStream();

            \Protobuf\WireFormat::assertWireType($wire, 9);

            $value = $reader->readString($stream);

            return $value;
        };

        $writeCallback = function (\Protobuf\WriteContext $context, $value) {
            $stream = $context->getStream();
            $writer = $context->getWriter();
            $sizeContext = $context->getComputeSizeContext();

            $writer->writeVarint($stream, 400018);
            $writer->writeString($stream, $value);
        };

        $sizeCallback = function (\Protobuf\ComputeSizeContext $context, $value) {
            $calculator = $context->getSizeCalculator();
            $size = 0;

            $size += 3;
            $size += $calculator->computeStringSize($value);

            return $size;
        };

        return self::$package = new \Protobuf\Extension\ExtensionField(
            '\\google\\protobuf\\FileOptions',
            'package',
            50002,
            $readCallback,
            $writeCallback,
            $sizeCallback,
            __METHOD__
        );
    }

    /**
     * Extension field : generic_services
     *
     * @return \Protobuf\Extension
     */
    public static function genericServices()
    {
        if (self::$generic_services !== null) {
            return self::$generic_services;
        }

        $readCallback = function (\Protobuf\ReadContext $context, $wire) {
            $reader = $context->getReader();
            $length = $context->getLength();
            $stream = $context->getStream();

            \Protobuf\WireFormat::assertWireType($wire, 8);

            $value = $reader->readBool($stream);

            return $value;
        };

        $writeCallback = function (\Protobuf\WriteContext $context, $value) {
            $stream = $context->getStream();
            $writer = $context->getWriter();
            $sizeContext = $context->getComputeSizeContext();

            $writer->writeVarint($stream, 400024);
            $writer->writeBool($stream, $value);
        };

        $sizeCallback = function (\Protobuf\ComputeSizeContext $context, $value) {
            $calculator = $context->getSizeCalculator();
            $size = 0;

            $size += 3;
            $size += 1;

            return $size;
        };

        return self::$generic_services = new \Protobuf\Extension\ExtensionField(
            '\\google\\protobuf\\FileOptions',
            'generic_services',
            50003,
            $readCallback,
            $writeCallback,
            $sizeCallback,
            __METHOD__
        );
    }


}

