<?php

namespace Bitrix\Main\Type\Contract;

interface Jsonable
{
    /**
     * @param int $options
     * @return array
     */
	public function toJson($options = 0);
}
