<?php
/**
 * Bitrix Framework
 * @package    bitrix
 * @subpackage main
 * @copyright  2001-2017 Bitrix
 */

namespace Bitrix\Main\ORM\Query;

use Bitrix\Main\ORM\Query\Filter\ConditionTree as Filter;

/**
 * Class for nice description of join reference. Alias to Query::filter().
 * @package    bitrix
 * @subpackage main
 */
class Join
{
    /**
     * Short alias to init filter with whereColumn.
     * @see Filter::whereColumn()
     * @return Filter
     * @internal param array ...$condition
     *
     */
	public static function on()
	{
		$condition = func_get_args();
		return call_user_func_array(array(new Filter, 'whereColumn'), $condition);
	}
}
