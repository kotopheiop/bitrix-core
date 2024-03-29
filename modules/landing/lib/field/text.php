<?php

namespace Bitrix\Landing\Field;

class Text extends \Bitrix\Landing\Field
{
    /**
     * Max length of the field.
     * @var int
     */
    protected $maxlength;

    /**
     * Placeholder for input.
     * @var string
     */
    protected $placeholder;

    /**
     * Class constructor.
     * @param string $code Field code.
     * @param array $params Field params.
     */
    public function __construct($code, array $params = array())
    {
        $this->code = mb_strtoupper($code);
        $this->value = null;
        $this->id = isset($params['id']) ? $params['id'] : '';
        $this->title = isset($params['title']) ? $params['title'] : '';
        $this->default = isset($params['default']) ? $params['default'] : null;
        $this->help = isset($params['help']) ? $params['help'] : '';
        $this->searchable = isset($params['searchable']) && $params['searchable'] === true;
        $this->placeholder = isset($params['placeholder']) ? $params['placeholder'] : '';
        $this->maxlength = isset($params['maxlength']) ? (int)$params['maxlength'] : 0;
    }

    /**
     * Gets true, if current value is empty.
     * @return bool
     */
    public function isEmptyValue()
    {
        return $this->value === '';
    }

    /**
     * Vew field.
     * @param array $params Array params:
     * name - field name
     * class - css-class for this element
     * additional - some additional params as is.
     * @return void
     */
    public function viewForm(array $params = array())
    {
        ?>
        <input type="text" <?
        ?><?= isset($params['additional']) ? $params['additional'] . ' ' : '' ?><?
        ?><?= isset($params['id']) ? 'id="' . \htmlspecialcharsbx($params['id']) . '" ' : '' ?><?
        ?><?= $this->maxlength > 0 ? 'maxlength="' . $this->maxlength . '" ' : '' ?><?
        ?><?= $this->placeholder != '' ? 'placeholder="' . \htmlspecialcharsbx($this->placeholder) . '" ' : '' ?><?
        ?>class="<?= isset($params['class']) ? \htmlspecialcharsbx($params['class']) : '' ?>" <?
               ?>data-code="<?= \htmlspecialcharsbx($this->code) ?>" <?
               ?>name="<?= \htmlspecialcharsbx(
            isset($params['name_format'])
                ? str_replace('#field_code#', $this->code, $params['name_format'])
                : $this->code
        ) ?>" <?
               ?>value="<?= \htmlspecialcharsbx($this->value ? $this->value : $this->default) ?>" <?
        ?> />
        <?
    }

    /**
     * Set value to the field.
     * @param string $value Value.
     * @return void
     */
    public function setValue($value)
    {
        if ($this->maxlength > 0) {
            $this->value = mb_substr($value, 0, $this->maxlength);
        } else {
            $this->value = $value;
        }
    }
}
