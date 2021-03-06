<?php
/**
 * Generated by Protobuf protoc plugin.
 *
 * File descriptor : descriptor.proto
 */


namespace google\protobuf\FieldOptions;

/**
 * Protobuf enum : google.protobuf.FieldOptions.CType
 */
class CType extends \Protobuf\Enum
{

    /**
     * STRING = 0
     */
    const STRING_VALUE = 0;

    /**
     * CORD = 1
     */
    const CORD_VALUE = 1;

    /**
     * STRING_PIECE = 2
     */
    const STRING_PIECE_VALUE = 2;

    /**
     * @var \google\protobuf\FieldOptions\CType
     */
    protected static $STRING = null;

    /**
     * @var \google\protobuf\FieldOptions\CType
     */
    protected static $CORD = null;

    /**
     * @var \google\protobuf\FieldOptions\CType
     */
    protected static $STRING_PIECE = null;

    /**
     * @return \google\protobuf\FieldOptions\CType
     */
    public static function STRING()
    {
        if (self::$STRING !== null) {
            return self::$STRING;
        }

        return self::$STRING = new self('STRING', self::STRING_VALUE);
    }

    /**
     * @return \google\protobuf\FieldOptions\CType
     */
    public static function CORD()
    {
        if (self::$CORD !== null) {
            return self::$CORD;
        }

        return self::$CORD = new self('CORD', self::CORD_VALUE);
    }

    /**
     * @return \google\protobuf\FieldOptions\CType
     */
    public static function STRING_PIECE()
    {
        if (self::$STRING_PIECE !== null) {
            return self::$STRING_PIECE;
        }

        return self::$STRING_PIECE = new self('STRING_PIECE', self::STRING_PIECE_VALUE);
    }

    /**
     * @param int $value
     * @return \google\protobuf\FieldOptions\CType
     */
    public static function valueOf($value)
    {
        switch ($value) {
            case 0:
                return self::STRING();
            case 1:
                return self::CORD();
            case 2:
                return self::STRING_PIECE();
            default:
                return null;
        }
    }


}

