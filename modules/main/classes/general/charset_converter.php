<?
/**
 * @deprecated Use \Bitrix\Main\Text\Encoding
 */
class CharsetConverter
{
	private static $instance;

	/**
	 * @deprecated
	 */
	public static function GetInstance()
	{
		if (!isset(self::$instance))
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;
	}

    /**
     * @deprecated
     * @param $string
     * @param $charset_in
     * @param $charset_out
     * @param string $errorMessage
     * @param bool $ignoreErrors
     * @return array|bool|SplFixedArray|string
     */
	public static function ConvertCharset($string, $charset_in, $charset_out, &$errorMessage = "", $ignoreErrors = false)
	{
		$string = strval($string);

		return \Bitrix\Main\Text\Encoding::convertEncoding($string, $charset_in, $charset_out, $errorMessage);
	}

    /**
     * @deprecated
     * @param $sourceString
     * @param $charsetFrom
     * @param $charsetTo
     * @return array|bool|SplFixedArray|string
     */
	public function Convert($sourceString, $charsetFrom, $charsetTo)
	{
		return self::ConvertCharset($sourceString, $charsetFrom, $charsetTo);
	}
}
