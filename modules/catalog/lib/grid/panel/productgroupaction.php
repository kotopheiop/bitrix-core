<?php

namespace Bitrix\Catalog\Grid\Panel;

use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Iblock,
    Bitrix\Catalog,
    Bitrix\Currency;

class ProductGroupAction extends Iblock\Grid\Panel\GroupAction
{
    private const FIELD_ID_PREFIX = 'product_';

    private const FIELD_NAME_PREFIX = 'PRODUCT_';

    private const PRODUCT_FIELD_NAME = 'PRODUCT_FIELD_NAME';

    /** @var bool */
    protected $catalogIncluded = null;

    protected $catalogOptions = [];

    /** @var array */
    protected $catalogConfig = null;

    protected $productFieldHandlers = [];

    public function __construct(array $options)
    {
        parent::__construct($options);

        $this->initProductFieldHandlers();
    }

    /**
     * @return void
     */
    protected function initConfig()
    {
        parent::initConfig();

        $this->catalogIncluded = Loader::includeModule('catalog');
        if ($this->catalogIncluded) {
            $this->catalogConfig = \CCatalogSku::GetInfoByIBlock($this->iblockId);
            $this->catalogOptions['SEPARATE_MODE'] = (string)Main\Config\Option::get(
                    'catalog',
                    'show_catalog_tab_with_offers'
                ) === 'Y';
            $this->catalogOptions['STORE_MODE'] = Catalog\Config\State::isUsedInventoryManagement();
        }
    }

    /**
     * @return void
     */
    protected function initProductFieldHandlers()
    {
        $this->productFieldHandlers = [
            'WEIGHT' => 'Weight',
            'QUANTITY_TRACE' => 'QuantityTrace',
            'CAN_BUY_ZERO' => 'CanBuyZero',
            'QUANTITY' => 'Quantity',
            'PURCHASING_PRICE' => 'PurchasingPrice',
            'VAT_INCLUDED' => 'VatIncluded',
            'VAT_ID' => 'VatId',
            'SUBSCRIBE' => 'Subscribe',
            'MEASURE' => 'Measure'
        ];
    }

    /**
     * @return array
     */
    protected function getActionHandlers()
    {
        $result = parent::getActionHandlers();
        $result[Catalog\Grid\ProductAction::SET_FIELD] = 'ProductField';
        $result[Catalog\Grid\ProductAction::CHANGE_PRICE] = 'ProductChangePrice';
        return $result;
    }

    /**
     * @param array $params
     * @return array|null
     */
    protected function actionProductFieldPanel(array $params = [])
    {
        if (!$this->isAllowedProductActions()) {
            return null;
        }

        $result = [];
        $items = [];
        foreach ($this->productFieldHandlers as $handler) {
            $handler = 'getProductField' . $handler . 'Row';
            if (is_callable([$this, $handler])) {
                $row = call_user_func_array([$this, $handler], []);
                if (!empty($row)) {
                    $items[] = $row;
                }
            }
        }
        unset($row, $handler);

        $userFields = Catalog\Product\SystemField::getGroupActions();
        if (!empty($userFields) && is_array($userFields)) {
            $items = array_merge($items, $userFields);
        }
        unset($userFields);

        if (!empty($items)) {
            $name = (isset($params['NAME']) && $params['NAME'] != ''
                ? $params['NAME']
                : Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_SET_PRODUCT_FIELD')
            );

            $data = [];
            $data[] = [
                'TYPE' => Main\Grid\Panel\Types::DROPDOWN,
                'ID' => $this->getFormProductFieldId(),
                'NAME' => $this->getFormProductFieldName(),
                'ITEMS' => $items
            ];
            if ($this->isUiGrid()) {
                $data[] = $this->getApplyButtonWithConfirm(
                    [
                        'APPLY_BUTTON_ID' => 'send_product'
                    ]
                );
            }

            $result = [
                'name' => $name,
                'type' => 'multicontrol',
                'action' => [
                    [
                        'ACTION' => Main\Grid\Panel\Actions::RESET_CONTROLS
                    ],
                    [
                        'ACTION' => Main\Grid\Panel\Actions::CREATE,
                        'DATA' => $data
                    ]
                ]
            ];

            unset($data);
        }
        unset($items);

        return $result;
    }

    /**
     * @return array|null
     */
    protected function actionProductFieldRequest()
    {
        $fieldId = $this->request->get($this->getFormProductFieldName());
        if (empty($fieldId)) {
            return null;
        }

        if (strncmp($fieldId, 'UF_', 3) === 0) {
            return Catalog\Product\SystemField::getGroupActionRequest($fieldId);
        }

        if (!isset($this->productFieldHandlers[$fieldId])) {
            return null;
        }

        $handler = 'getProductField' . $this->productFieldHandlers[$fieldId] . 'Request';
        if (is_callable([$this, $handler])) {
            return call_user_func_array([$this, $handler], []);
        }

        return null;
    }

    /**
     * @param array $params
     * @return array|null
     */
    protected function actionProductChangePricePanel(array $params = [])
    {
        if (!$this->isAllowedProductActions()) {
            return null;
        }

        $name = (isset($params['NAME']) && $params['NAME'] != ''
            ? $params['NAME']
            : Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_PRODUCT_CHANGE_PRICE')
        );

        return [
            'name' => $name,
            'type' => 'customJs',
            'js' => 'CreateDialogChPrice()'
        ];
    }

    /**
     * @return array|null
     */
    protected function actionProductChangePriceRequest()
    {
        $result = [];
        $result['PRICE_TYPE'] = $this->request->get('chprice_id_price_type');
        $result['UNITS'] = $this->request->get('chprice_units');
        $result['FORMAT_RESULTS'] = $this->request->get('chprice_format_result');
        $result['INITIAL_PRICE_TYPE'] = $this->request->get('chprice_initial_price_type');
        $result['RESULT_MASK'] = $this->request->get('chprice_result_mask');
        $result['DIFFERENCE_VALUE'] = $this->request->get('chprice_difference_value');
        $result['VALUE_CHANGING'] = $this->request->get('chprice_value_changing_price');
        return (!empty($result['VALUE_CHANGING']) ? $result : null);
    }

    /**
     * @return string
     */
    protected function getFormProductFieldName()
    {
        return self::PRODUCT_FIELD_NAME;
    }

    /**
     * @return string
     */
    protected function getFormProductFieldId()
    {
        return mb_strtolower($this->getFormProductFieldName() . '_ID');
    }

    /**
     * @param string $field
     * @return string
     */
    protected function getFormRowFieldName(string $field)
    {
        return self::FIELD_NAME_PREFIX . mb_strtoupper($field);
    }

    /**
     * @param string $field
     * @return string
     */
    protected function getFormRowFieldId(string $field)
    {
        return self::FIELD_ID_PREFIX . mb_strtolower($field) . '_id';
    }

    /**
     * @param bool $defaultState
     * @return array
     */
    protected static function getStatusList(bool $defaultState)
    {
        $result = [];

        $result[] = [
            'NAME' => Loc::getMessage(
                'IBLOCK_GRID_PANEL_ACTION_MESS_STATUS_DEFAULT',
                [
                    '#VALUE#' => $defaultState
                        ? Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_MESS_STATUS_YES')
                        : Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_MESS_STATUS_NO')
                ]
            ),
            'VALUE' => Catalog\ProductTable::STATUS_DEFAULT
        ];
        $result[] = [
            'NAME' => Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_MESS_STATUS_YES'),
            'VALUE' => Catalog\ProductTable::STATUS_YES
        ];
        $result[] = [
            'NAME' => Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_MESS_STATUS_NO'),
            'VALUE' => Catalog\ProductTable::STATUS_NO
        ];

        return $result;
    }

    /**
     * @return array
     */
    protected static function getBinaryList()
    {
        $result = [];

        $result[] = [
            'NAME' => Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_MESS_STATUS_YES'),
            'VALUE' => Catalog\ProductTable::STATUS_YES
        ];
        $result[] = [
            'NAME' => Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_MESS_STATUS_NO'),
            'VALUE' => Catalog\ProductTable::STATUS_NO
        ];

        return $result;
    }

    /**
     * @return bool
     */
    protected function isAllowedProductActions()
    {
        if (!$this->catalogIncluded) {
            return false;
        }
        if (empty($this->catalogConfig)) {
            return false;
        }
        if ($this->catalogConfig['CATALOG_TYPE'] == \CCatalogSku::TYPE_PRODUCT) {
            return false;
        }

        return true;
    }

    /**
     * @param string $fieldId
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\SystemException
     */
    protected function getInputField(string $fieldId)
    {
        $entity = Catalog\ProductTable::getEntity();
        $field = $entity->getField($fieldId);

        $action = [];
        $action[] = [
            'ACTION' => Main\Grid\Panel\Actions::RESET_CONTROLS
        ];
        $action[] = [
            'ACTION' => Main\Grid\Panel\Actions::CREATE,
            'DATA' => [
                [
                    'TYPE' => Main\Grid\Panel\Types::TEXT,
                    'ID' => $this->getFormRowFieldId($fieldId),
                    'NAME' => $this->getFormRowFieldName($fieldId),
                    'VALUE' => ''
                ]
            ]
        ];
        $result = [
            'NAME' => $field->getTitle(),
            'VALUE' => $fieldId,
            'ONCHANGE' => $action
        ];
        unset($action);
        unset($field, $entity);

        return $result;
    }

    /**
     * @param string $fieldId
     * @param array $list
     * @return array|null
     * @throws Main\ArgumentException
     * @throws Main\SystemException
     */
    protected function getDropdownField(string $fieldId, array $list)
    {
        $entity = Catalog\ProductTable::getEntity();
        $field = $entity->getField($fieldId);

        $action = [];
        $action[] = [
            'ACTION' => Main\Grid\Panel\Actions::RESET_CONTROLS
        ];
        $action[] = [
            'ACTION' => Main\Grid\Panel\Actions::CREATE,
            'DATA' => [
                [
                    'TYPE' => Main\Grid\Panel\Types::DROPDOWN,
                    'ID' => $this->getFormRowFieldId($fieldId),
                    'NAME' => $this->getFormRowFieldName($fieldId),
                    'ITEMS' => $list
                ],
            ]
        ];

        $result = [
            'NAME' => $field->getTitle(),
            'VALUE' => $fieldId,
            'ONCHANGE' => $action
        ];
        unset($action);
        unset($field, $entity);

        return $result;
    }

    /**
     * @return array|null
     */
    protected function getProductFieldWeightRow()
    {
        if (!$this->isAllowedProductActions()) {
            return null;
        }

        return $this->getInputField('WEIGHT');
    }

    /**
     * @return array|null
     */
    protected function getProductFieldWeightRequest()
    {
        $result = $this->request->get($this->getFormRowFieldName('WEIGHT'));
        $result = $this->checkFloatValue($result);
        if ($result === null) {
            return null;
        }
        return ['WEIGHT' => $result];
    }

    /**
     * @return array|null
     */
    protected function getProductFieldQuantityTraceRow()
    {
        if ($this->catalogOptions['STORE_MODE']) {
            return null;
        }

        return $this->getDropdownField(
            'QUANTITY_TRACE',
            $this->getStatusList(
                (string)Main\Config\Option::get('catalog', 'default_quantity_trace') === 'Y'
            )
        );
    }

    /**
     * @return array|null
     */
    protected function getProductFieldQuantityTraceRequest()
    {
        $result = $this->request->get($this->getFormRowFieldName('QUANTITY_TRACE'));
        if (!$this->checkStatusValue($result)) {
            return null;
        }
        return ['QUANTITY_TRACE' => $result];
    }

    /**
     * @return array|null
     */
    protected function getProductFieldCanBuyZeroRow()
    {
        if ($this->catalogOptions['STORE_MODE']) {
            return null;
        }

        return $this->getDropdownField(
            'CAN_BUY_ZERO',
            $this->getStatusList(
                (string)Main\Config\Option::get('catalog', 'default_can_buy_zero') === 'Y'
            )
        );
    }

    /**
     * @return array|null
     */
    protected function getProductFieldCanBuyZeroRequest()
    {
        $result = $this->request->get($this->getFormRowFieldName('CAN_BUY_ZERO'));
        if ($result === null || !is_string($result)) {
            return null;
        }
        if (!$this->checkStatusValue($result)) {
            return null;
        }
        return ['CAN_BUY_ZERO' => $result];
    }

    /**
     * @return array|null
     */
    protected function getProductFieldQuantityRow()
    {
        if ($this->catalogOptions['STORE_MODE']) {
            return null;
        }

        return $this->getInputField('QUANTITY');
    }

    /**
     * @return array|null
     */
    protected function getProductFieldQuantityRequest()
    {
        $result = $this->request->get($this->getFormRowFieldName('QUANTITY'));
        $result = $this->checkFloatValue($result);
        if ($result === null) {
            return null;
        }
        return ['QUANTITY' => $result];
    }

    /**
     * @return array|null
     */
    protected function getProductFieldPurchasingPriceRow()
    {
        global $USER;
        if ($this->catalogOptions['STORE_MODE']) {
            return null;
        }
        if (!$USER->CanDoOperation('catalog_purchas_info')) {
            return null;
        }

        $list = [];
        foreach (Currency\CurrencyManager::getCurrencyList() as $currencyId => $currencyName) {
            $list[] = [
                'VALUE' => $currencyId,
                'NAME' => $currencyName
            ];
        }
        if (empty($list)) {
            return null;
        }

        $entity = Catalog\ProductTable::getEntity();
        $field = $entity->getField('PURCHASING_PRICE');

        $action = [];
        $action[] = [
            'ACTION' => Main\Grid\Panel\Actions::RESET_CONTROLS
        ];
        $action[] = [
            'ACTION' => Main\Grid\Panel\Actions::CREATE,
            'DATA' => [
                [
                    'TYPE' => Main\Grid\Panel\Types::TEXT,
                    'ID' => $this->getFormRowFieldId('PURCHASING_PRICE'),
                    'NAME' => $this->getFormRowFieldName('PURCHASING_PRICE'),
                    'VALUE' => ''
                ],
                [
                    'TYPE' => Main\Grid\Panel\Types::DROPDOWN,
                    'ID' => $this->getFormRowFieldId('PURCHASING_CURRENCY'),
                    'NAME' => $this->getFormRowFieldName('PURCHASING_CURRENCY'),
                    'ITEMS' => $list
                ]
            ]
        ];

        $result = [
            'NAME' => $field->getTitle(),
            'VALUE' => 'PURCHASING_PRICE',
            'ONCHANGE' => $action
        ];
        unset($action);
        unset($field, $entity);

        return $result;
    }

    /**
     * @return array|null
     */
    protected function getProductFieldPurchasingPriceRequest()
    {
        $price = self::checkEmptyFloatValue($this->request->get($this->getFormRowFieldName('PURCHASING_PRICE')));
        if ($price === null) {
            return null;
        }
        if ($price === '') {
            $price = null;
            $currency = null;
        } else {
            $currency = $this->request->get($this->getFormRowFieldName('PURCHASING_CURRENCY'));
            if ($currency === null || !is_string($currency)) {
                return null;
            }
            $currency = trim($currency);
            if ($currency === '') {
                return null;
            }
        }
        return ['PURCHASING_PRICE' => $price, 'PURCHASING_CURRENCY' => $currency];
    }

    /**
     * @return array|null
     */
    protected function getProductFieldVatIncludedRow()
    {
        return $this->getDropdownField(
            'VAT_INCLUDED',
            $this->getBinaryList()
        );
    }

    /**
     * @return array|null
     */
    protected function getProductFieldVatIncludedRequest()
    {
        $result = $this->request->get($this->getFormRowFieldName('VAT_INCLUDED'));
        if (!$this->checkBinaryValue($result)) {
            return null;
        }
        return ['VAT_INCLUDED' => $result];
    }

    /**
     * @return array|null
     */
    protected function getProductFieldVatIdRow()
    {
        $list = [];
        $list[] = [
            'VALUE' => '0',
            'NAME' => Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_MESS_EMPTY_VALUE')
        ];
        $found = false;
        $iterator = Catalog\VatTable::getList(
            [
                'select' => ['ID', 'NAME', 'SORT'],
                'filter' => ['=ACTIVE' => 'Y'],
                'order' => ['SORT' => 'ASC', 'ID' => 'ASC']
            ]
        );
        while ($row = $iterator->fetch()) {
            $found = true;
            $list[] = [
                'VALUE' => $row['ID'],
                'NAME' => $row['NAME']
            ];
        }
        unset($row, $iterator);
        if (!$found) {
            return null;
        }

        return $this->getDropdownField(
            'VAT_ID',
            $list
        );
    }

    /**
     * @return array|null
     */
    protected function getProductFieldVatIdRequest()
    {
        $result = $this->checkIntValue($this->request->get($this->getFormRowFieldName('VAT_ID')));
        if ($result === null || $result < 0) {
            return null;
        }
        return ['VAT_ID' => $result];
    }

    /**
     * @return array|null
     */
    protected function getProductFieldSubscribeRow()
    {
        return $this->getDropdownField(
            'SUBSCRIBE',
            $this->getStatusList(
                (string)Main\Config\Option::get('catalog', 'default_subscribe') === 'Y'
            )
        );
    }

    /**
     * @return array|null
     */
    protected function getProductFieldSubscribeRequest()
    {
        $result = $this->request->get($this->getFormRowFieldName('SUBSCRIBE'));
        if ($result === null || !is_string($result)) {
            return null;
        }
        if (!$this->checkStatusValue($result)) {
            return null;
        }
        return ['SUBSCRIBE' => $result];
    }

    /**
     * @return array|null
     */
    protected function getProductFieldMeasureRow()
    {
        $list = [];
        $iterator = \CCatalogMeasure::getList(
            array(),
            array(),
            false,
            false,
            array('ID', 'CODE', 'MEASURE_TITLE', 'SYMBOL_INTL')
        );
        while ($row = $iterator->Fetch()) {
            $list[] = [
                'VALUE' => $row['ID'],
                'NAME' => $row['MEASURE_TITLE']
            ];
        }
        unset($row, $iterator);
        if (empty($list)) {
            return null;
        }

        return $this->getDropdownField(
            'MEASURE',
            $list
        );
    }

    /**
     * @return array|null
     */
    protected function getProductFieldMeasureRequest()
    {
        $result = $this->checkIntValue($this->request->get($this->getFormRowFieldName('MEASURE')));
        if ($result === null || $result <= 0) {
            return null;
        }
        return ['MEASURE' => $result];
    }

    /**
     * Returns float value or null for data entered in user form.
     * @param $value
     * @return float|null
     * @internal
     *
     */
    protected static function checkFloatValue($value)
    {
        if (is_array($value) || $value === null) {
            return null;
        }
        $value = (str_replace([',', ' '], ['.', ''], trim($value)));
        if ($value === '') {
            return null;
        }
        $value = (float)$value;
        return (is_finite($value) ? $value : null);
    }

    /**
     * Returns float value or null for price data entered in user form. Need for php7.
     * @param string|int|float $value Float value from form.
     * @return float|null|string
     * @internal
     *
     */
    protected static function checkEmptyFloatValue($value)
    {
        if (is_array($value) || $value === null) {
            return null;
        }
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        $value = (float)(str_replace(',', '.', $value));
        return (is_finite($value) ? $value : null);
    }

    /**
     * @param string $value
     * @return bool
     */
    protected static function checkStatusValue($value)
    {
        if ($value === null || !is_string($value)) {
            return null;
        }
        return (
            $value === Catalog\ProductTable::STATUS_DEFAULT
            || $value === Catalog\ProductTable::STATUS_YES
            || $value === Catalog\ProductTable::STATUS_NO
        );
    }

    /**
     * @param string $value
     * @return bool
     */
    protected static function checkBinaryValue($value)
    {
        if ($value === null || !is_string($value)) {
            return null;
        }
        return (
            $value === Catalog\ProductTable::STATUS_YES
            || $value === Catalog\ProductTable::STATUS_NO
        );
    }

    /**
     * Returns int value or null for data entered in user form.
     * @param string|int $value Integer value from form.
     * @return int|null
     * @internal
     *
     */
    protected static function checkIntValue($value)
    {
        if (is_array($value) || $value === null) {
            return null;
        }
        if (((int)$value) . '|' !== $value . '|') {
            return null;
        }
        return (int)$value;
    }
}