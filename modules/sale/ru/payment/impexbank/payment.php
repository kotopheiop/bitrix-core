<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?><?
$strMerchantID = CSalePaySystemAction::GetParamValue("SHOP_ACCOUNT");
$strMerchantName = CSalePaySystemAction::GetParamValue("SHOP_NAME");

$ORDER_ID = IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);
?>
<p>�� ������ �������� �� �����-����� &quot;�����������&quot; ����� �������������� ����� ��������� ������� <strong>�����������</strong>.
</p>
<p>C��� � <?= htmlspecialcharsEx($ORDER_ID . " �� " . $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_INSERT"]) ?></p>
<p>����� � ������ �� �����:
    <strong><?= SaleFormatCurrency($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"], $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"]) . "&nbsp;" ?></strong>
</p>

<form method="post" action="https://www.impexbank.ru/servlets/SPCardPaymentServlet" class="mb-3">
    <input type="hidden" name="Order_ID" value="<?= $ORDER_ID ?>">
    <input type="hidden" name="Amount"
           value="<?= htmlspecialcharsbx($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"]) ?>"><br>
    <input type="hidden" name="Formtype" value="AuthForm">
    <input type="hidden" name="Merchant_ID" value="<?= htmlspecialcharsbx($strMerchantID) ?>">
    <input type="hidden" name="Merchant_Name" value="<?= htmlspecialcharsbx($strMerchantName) ?>">
    <input type="hidden" name="Currency"
           value="<?= htmlspecialcharsbx($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"]) ?>">
    <input type="submit" value="��������" class="btn btn-primary">
</form>

<div class="alert alert-warning" role="alert">
    <p class="mb-1"><strong>�������� ��������!</strong></p>
    <p class="mb-1">��� ���������� �������� �������������� � �������������� ������ ��������� ������� �����������.</p>
    <p class="mb-0">��� ������, ����������� ��� ������������� �������, �������������� �������� ��������� ��������
        �����������.</p>
</div>