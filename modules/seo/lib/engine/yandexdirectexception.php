<?php

namespace Bitrix\Seo\Engine;

use Bitrix\Main\SystemException;

class YandexDirectException extends SystemException
{
    public function __construct(array $queryResult, \Exception $previous = null)
    {
        $errorMessage = $queryResult['error'];
        if ($errorMessage <> '' && $queryResult['error_description'] <> '') {
            $errorMessage .= ": ";
        }
        $errorMessage .= $queryResult['error_description'];

        if (intval($queryResult['error']) > 0) {
            parent::__construct($errorMessage, intval($queryResult['error']));
        } else {
            parent::__construct($errorMessage);
        }
    }
}
