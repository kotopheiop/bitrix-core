<?php

namespace Bitrix\Bizproc\BaseType;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Bizproc\FieldType;

/**
 * Class Select
 * @package Bitrix\Bizproc\BaseType
 */
class Select extends Base
{
    /**
     * @return string
     */
    public static function getType()
    {
        return FieldType::SELECT;
    }

    /**
     * @param FieldType $fieldType
     * @param $value
     * @return string
     */
    protected static function formatValuePrintable(FieldType $fieldType, $value)
    {
        $options = static::getFieldOptions($fieldType);
        if (isset($options[$value])) {
            return (string)$options[$value];
        }
        return '';
    }

    /**
     * @param FieldType $fieldType Document field type.
     * @param mixed $value Field value.
     * @param string $toTypeClass Type class name.
     * @return null|mixed
     */
    public static function convertTo(FieldType $fieldType, $value, $toTypeClass)
    {
        /** @var Base $toTypeClass */
        $type = $toTypeClass::getType();
        $options = static::getFieldOptions($fieldType);

        $key = $originalValue = $value;
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $key = $k;
                $originalValue = $v;
            }
        } elseif (isset($options[$key])) {
            $originalValue = $options[$value];
        }

        switch ($type) {
            case FieldType::BOOL:
                $value = mb_strtolower((string)$key);
                $value = in_array($value, array('y', 'yes', 'true', '1')) ? 'Y' : 'N';
                break;
            case FieldType::DOUBLE:
                $value = str_replace(' ', '', str_replace(',', '.', $key));
                $value = (float)$value;
                break;
            case FieldType::INT:
                $value = str_replace(' ', '', $key);
                $value = (int)$value;
                break;
            case FieldType::STRING:
            case FieldType::TEXT:
                $value = (string)$originalValue;
                break;
            case FieldType::SELECT:
                $value = (string)$key;
                break;
            case FieldType::USER:
                $value = trim($key);
                if (mb_strpos($value, 'user_') === false
                    && mb_strpos($value, 'group_') === false
                    && !preg_match('#^[0-9]+$#', $value)
                ) {
                    $value = null;
                }
                break;
            default:
                $value = null;
        }

        return $value;
    }

    /**
     * Return conversion map for current type.
     * @return array Map.
     */
    public static function getConversionMap()
    {
        return array(
            array(
                FieldType::BOOL,
                FieldType::DOUBLE,
                FieldType::INT,
                FieldType::STRING,
                FieldType::TEXT,
                FieldType::SELECT,
                FieldType::USER
            )
        );
    }

    /**
     * @param FieldType $fieldType
     * @param array $field
     * @param mixed $value
     * @param bool $allowSelection
     * @param int $renderMode
     * @return string
     */
    protected static function renderControl(FieldType $fieldType, array $field, $value, $allowSelection, $renderMode)
    {
        $selectorValue = null;
        $typeValue = [];
        if (!is_array($value)) {
            $value = (array)$value;
        }

        if (\CBPHelper::isAssociativeArray($value)) {
            $value = array_keys($value);
        }

        foreach ($value as $v) {
            if ($allowSelection && \CBPActivity::isExpression($v)) {
                $selectorValue = $v;
            } else {
                $typeValue[] = (string)$v;
            }
        }

        // need to show at least one control
        if (empty($typeValue)) {
            $typeValue[] = null;
        }

        $className = static::generateControlClassName($fieldType, $field);
        $selectorAttributes = '';

        $isPublicControl = $renderMode & FieldType::RENDER_MODE_PUBLIC;

        if ($allowSelection && $isPublicControl) {
            $selectorAttributes = sprintf(
                'data-role="inline-selector-target" data-property="%s" ',
                htmlspecialcharsbx(Main\Web\Json::encode($fieldType->getProperty()))
            );
        }

        if ($fieldType->isMultiple()) {
            $selectorAttributes .= 'size="5" multiple ';
        }

        $renderResult = sprintf(
            '<select id="%s" class="%s" name="%s%s" %s>',
            htmlspecialcharsbx(static::generateControlId($field)),
            ($isPublicControl ? htmlspecialcharsbx($className) : ''),
            htmlspecialcharsbx(static::generateControlName($field)),
            $fieldType->isMultiple() ? '[]' : '',
            $selectorAttributes
        );

        if (!$fieldType->isMultiple()) {
            $renderResult .= '<option value="">[' . Loc::getMessage('BPCGHLP_NOT_SET') . ']</option>';
        }

        $settings = static::getFieldSettings($fieldType);
        $groups = $settings['Groups'] ?: null;

        if (is_array($groups) && !empty($groups)) {
            foreach ($groups as $group) {
                if (!is_array($group)) {
                    continue;
                }

                $name = isset($group['name']) ? $group['name'] : '';

                if ($name !== '') {
                    $renderResult .= '<optgroup label="' . htmlspecialcharsbx($name) . '">';
                }

                $options = isset($group['items']) && is_array($group['items']) ? $group['items'] : array();
                foreach ($options as $k => $v) {
                    $renderResult .= '<option value="';
                    $renderResult .= htmlspecialcharsbx($k);
                    $renderResult .= '"';

                    if (in_array((string)$k, $typeValue, true)) {
                        $renderResult .= ' selected';
                    }

                    $renderResult .= '>';
                    $renderResult .= htmlspecialcharsbx($v);
                    $renderResult .= '</option>';
                }

                if ($name !== '') {
                    $renderResult .= '</optgroup>';
                }
            }
        } else {
            $options = static::getFieldOptions($fieldType);
            foreach ($options as $k => $v) {
                $renderResult .= '<option value="' . htmlspecialcharsbx($k) . '"' . (in_array(
                        (string)$k,
                        $typeValue
                    ) ? ' selected' : '') . '>' . htmlspecialcharsbx(htmlspecialcharsback($v)) . '</option>';
            }
        }

        if ($allowSelection && $selectorValue && $isPublicControl) {
            $renderResult .= sprintf(
                '<option value="%s" selected data-role="expression">%s</option>',
                htmlspecialcharsbx($selectorValue),
                htmlspecialcharsbx($selectorValue)
            );
        }

        $renderResult .= '</select>';

        if ($allowSelection && !$isPublicControl) {
            $renderResult .= static::renderControlSelector($field, $selectorValue, true, '', $fieldType);
        }

        return $renderResult;
    }

    /**
     * @param int $renderMode Control render mode.
     * @return bool
     */
    public static function canRenderControl($renderMode)
    {
        return true;
    }

    /**
     * @param FieldType $fieldType Document field type.
     * @param array $field Form field.
     * @param mixed $value Field value.
     * @param bool $allowSelection Allow selection flag.
     * @param int $renderMode Control render mode.
     * @return string
     */
    public static function renderControlSingle(FieldType $fieldType, array $field, $value, $allowSelection, $renderMode)
    {
        if ($renderMode & FieldType::RENDER_MODE_PUBLIC) {
            $allowSelection = false;
        }

        return static::renderControl($fieldType, $field, $value, $allowSelection, $renderMode);
    }

    /**
     * @param FieldType $fieldType Document field type.
     * @param array $field Form field.
     * @param mixed $value Field value.
     * @param bool $allowSelection Allow selection flag.
     * @param int $renderMode Control render mode.
     * @return string
     */
    public static function renderControlMultiple(
        FieldType $fieldType,
        array $field,
        $value,
        $allowSelection,
        $renderMode
    ) {
        if ($renderMode & FieldType::RENDER_MODE_PUBLIC) {
            $allowSelection = false;
        }

        return static::renderControl($fieldType, $field, $value, $allowSelection, $renderMode);
    }

    /**
     * @param FieldType $fieldType Document field type.
     * @param string $callbackFunctionName Client callback function name.
     * @param mixed $value Field value.
     * @return string
     */
    public static function renderControlOptions(FieldType $fieldType, $callbackFunctionName, $value)
    {
        $options = static::getFieldOptions($fieldType);

        $str = '';
        foreach ($options as $k => $v) {
            if ((string)$k !== (string)$v) {
                $str .= '[' . $k . ']' . $v;
            } else {
                $str .= $v;
            }

            $str .= "\n";
        }

        $rnd = randString();
        $renderResult = '<textarea id="WFSFormOptionsX' . $rnd . '" rows="5" cols="30">' . htmlspecialcharsbx(
                $str
            ) . '</textarea><br />';
        $renderResult .= Loc::getMessage('BPDT_SELECT_OPTIONS1') . '<br />';
        $renderResult .= Loc::getMessage('BPDT_SELECT_OPTIONS2') . '<br />';
        $renderResult .= '<script type="text/javascript">
				function WFSFormOptionsXFunction' . $rnd . '()
				{
					var result = {};
					var i, id, val, str = document.getElementById("WFSFormOptionsX' . $rnd . '").value;

					var arr = str.split(/[\r\n]+/);
					var p, re = /\[([^\]]+)\].+/;
					for (i in arr)
					{
						str = arr[i].replace(/^\s+|\s+$/g, \'\');
						if (str.length > 0)
						{
							id = str.match(re);
							if (id)
							{
								p = str.indexOf(\']\');
								id = id[1];
								val = str.substr(p + 1);
							}
							else
							{
								val = str;
								id = val;
							}
							result[id] = val;
						}
					}

					return result;
				}
				</script>';
        $renderResult .= '<input type="button" onclick="' . htmlspecialcharsbx($callbackFunctionName)
            . '(WFSFormOptionsXFunction' . $rnd . '())" value="' . Loc::getMessage('BPDT_SELECT_OPTIONS3') . '">';

        return $renderResult;
    }

    /**
     * @param FieldType $fieldType
     * @param array $field
     * @param array $request
     * @return null|mixed
     */
    protected static function extractValue(FieldType $fieldType, array $field, array $request)
    {
        $value = parent::extractValue($fieldType, $field, $request);
        $options = static::getFieldOptions($fieldType);
        $showError = false;

        if ($value === '' || sizeof($options) <= 0) {
            $value = null;
        } elseif ($value !== null && !isset($options[$value])) {
            $value = null;
            $showError = true;
        }

        if ($showError) {
            static::addError(
                array(
                    'code' => 'ErrorValue',
                    'message' => Loc::getMessage('BPDT_SELECT_INVALID'),
                    'parameter' => static::generateControlName($field),
                )
            );
        }

        return $value;
    }

    /**
     * @param FieldType $fieldType Document field type.
     * @param array $field Form field.
     * @param array $request Request data.
     * @return array
     */
    public static function extractValueMultiple(FieldType $fieldType, array $field, array $request)
    {
        $name = $field['Field'];
        $value = isset($request[$name]) ? $request[$name] : array();

        if (!is_array($value) || is_array($value) && \CBPHelper::isAssociativeArray($value)) {
            $value = array($value);
        }
        $value = array_unique($value);
        $request[$name] = $value;
        return parent::extractValueMultiple($fieldType, $field, $request);
    }

    /**
     * @param FieldType $fieldType Document field type.
     * @param mixed $value Field value.
     * @param string $format Format name.
     * @return string
     */
    public static function formatValueMultiple(FieldType $fieldType, $value, $format = 'printable')
    {
        if (\CBPHelper::isAssociativeArray($value)) {
            $value = array_keys($value);
        }
        return parent::formatValueMultiple($fieldType, $value, $format);
    }

    /**
     * @param FieldType $fieldType Document field type.
     * @param mixed $value Field value.
     * @param string $format Format name.
     * @return mixed|null
     */
    public static function formatValueSingle(FieldType $fieldType, $value, $format = 'printable')
    {
        if (\CBPHelper::isAssociativeArray($value)) {
            $keys = array_keys($value);
            $value = isset($keys[0]) ? $keys[0] : null;
        }
        return parent::formatValueSingle($fieldType, $value, $format);
    }

    /**
     * @param FieldType $fieldType Document field type.
     * @param mixed $value Field value.
     * @param string $toTypeClass Type class name.
     * @return array
     */
    public static function convertValueMultiple(FieldType $fieldType, $value, $toTypeClass)
    {
        if (\CBPHelper::isAssociativeArray($value)) {
            $value = array_keys($value);
        }
        return parent::convertValueMultiple($fieldType, $value, $toTypeClass);
    }


    /**
     * @param FieldType $fieldType
     * @return array
     */
    protected static function getFieldOptions(FieldType $fieldType)
    {
        $options = $fieldType->getOptions();
        return self::normalizeOptions($options);
    }

    /**
     * Get field settings
     * @param FieldType $fieldType
     * @return array
     */
    protected static function getFieldSettings(FieldType $fieldType)
    {
        return $fieldType->getSettings();
    }

    /**
     * @param mixed $options
     * @return array
     */
    protected static function normalizeOptions($options)
    {
        $normalized = array();
        if (is_array($options)) {
            foreach ($options as $key => $value) {
                if (is_array($value) && sizeof($value) == 2) {
                    $v = array_values($value);
                    $key = $v[0];
                    $value = $v[1];
                }
                $normalized[$key] = $value;
            }
        } else {
            $normalized[$options] = $options;
        }
        return $normalized;
    }

    public static function externalizeValue(FieldType $fieldType, $context, $value)
    {
        $map = $fieldType->getSettings()['ExternalValues'] ?? null;
        if ($map && isset($map[$value])) {
            return $map[$value];
        }

        return parent::externalizeValue($fieldType, $context, $value);
    }

    public static function mergeValue(FieldType $fieldType, array $baseValue, $appendValue): array
    {
        if (\CBPHelper::isAssociativeArray($baseValue)) {
            $baseValue = array_keys($baseValue);
        }
        if (\CBPHelper::isAssociativeArray($appendValue)) {
            $appendValue = array_keys($appendValue);
        }

        return parent::mergeValue($fieldType, $baseValue, $appendValue);
    }
}
