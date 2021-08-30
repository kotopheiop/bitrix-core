<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?><?
$ORDER_ID = intval($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);
?>
<form ACTION="http://127.0.0.1:8129/wallet" METHOD="POST" target="_blank">
    <input type="hidden" NAME="currency" value="643">
    <input type="hidden" NAME="PayManner" value="paycash">
    <input type="hidden" NAME="invoice" value="<?= $ORDER_ID ?>">
    <p>�� ������ �������� ����� ������� <strong>�Money</strong>.</p>
    <p>C��� � <?= htmlspecialcharsEx($ORDER_ID . " �� " . $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_INSERT"]) ?></p>
    <p>����� � ������ �� �����: <strong><? echo SaleFormatCurrency(
                $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"],
                $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"]
            ) ?></strong></p>
    <input type="hidden" name="InvoiceArticlesNames" value="Order &nbsp;<?= $ORDER_ID ?>&nbsp(<?= htmlspecialcharsEx(
        $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_INSERT"]
    ) ?>)">
    <input type="hidden" name="sum"
           value="<?= htmlspecialcharsbx($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"]) ?>">
    <input type="hidden" name="ShopID"
           value="<?= htmlspecialcharsbx(CSalePaySystemAction::GetParamValue("SHOP_ACCOUNT")) ?>">
    <input type="hidden" name="wbp_InactivityPeriod" value="2">
    <input type="hidden" name="wbp_ShopAddress" value="195.239.63.41:8128">
    <input type="hidden" name="wbp_ShopKeyID"
           value="<?= htmlspecialcharsbx(CSalePaySystemAction::GetParamValue("SHOP_KEY_ID")) ?>">
    <input type="hidden" name="wbp_ShopEncryptionKey"
           value="<?= htmlspecialcharsbx(CSalePaySystemAction::GetParamValue("SHOP_KEY")) ?>">
    <input type="hidden" name="wbp_ShopErrorInfo" value="">
    <input type="hidden" name="wbp_Version" value="1.0">
    <label for="OrderDetails">������ ������:</label>
    <textarea rows="5" name="OrderDetails" id="OrderDetails" cols="60" class="form-control mb-2">
		����� No <?= $ORDER_ID . " �� " . htmlspecialcharsbx($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_INSERT"]) ?>
	</textarea>
    <input type="Submit" name="Ok" value="��������� ������" class="btn btn-primary">
</form>

<div class="alert alert-warning mt-4"><strong>��������!</strong> ������� ������� �� ��������� ������� �Money -
    ����������, ����������, ������ ����������� ��� ������ ������.
</div>

<h4>��������� ������</h4>

<p>����� �������� ������ "��������", ��������� ��� <i>������� "�Money" � ��� �������</i>. ����� ������� ������
    "��������" ������� �������� ������ �������� "�Money" ���������� �� ������, ���������� �������� ������. ���������� ��
    ������ ��������� ����������� �������� �������� ��������.<p>

<p>��� ������� ����������� ��� ���������� ������. ���� �� ��������, � � ��� ���������� ����� �� �����, �� ��� �������
    �������� �������� ������ �������� ����������� ������ � ����������� ����� ����������� �������� ����. ����� ����, ���
    �� �������� ���� � ������� �Money, �� ������ ������, ����� ����� ��������� � ��������� �������� �������� ��������.
    ��������, ������ ������� �������� � ���������������� ������ � ������� ����� ������ ������: � 10.00 �� 18.00, ��
    ������.</p>
