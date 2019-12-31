<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<p><strong>����� ��������:</strong></p>
<p><?= htmlspecialcharsbx(CSalePaySystemAction::GetParamValue("POST_ADDRESS")) ?></p>
<p><strong>���� � <?= IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]) ?>
        �� <?= htmlspecialcharsbx($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_UPDATE"]) ?></strong></p>

<p><strong>����������:</strong> <?= htmlspecialcharsEx(CSalePaySystemAction::GetParamValue("PAYER_NAME")) ?></p>
<p>����� � ������:
    <strong><?= SaleFormatCurrency($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"], $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"]) ?></strong>
</p>

<p>���� ������������ � ������� ���� ����.</p>
