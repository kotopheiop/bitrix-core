<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?><?
$Sum = CSalePaySystemAction::GetParamValue("SHOULD_PAY");
$ShopID = CSalePaySystemAction::GetParamValue("SHOP_ID");
$scid = CSalePaySystemAction::GetParamValue("SCID");
$customerNumber = CSalePaySystemAction::GetParamValue("ORDER_ID");
$orderDate = CSalePaySystemAction::GetParamValue("ORDER_DATE");
$orderNumber = CSalePaySystemAction::GetParamValue("ORDER_ID");
$Sum = number_format($Sum, 2, ',', '');
?>
<p>�� ������ �������� ����� ������� <strong>�Money</strong>.</p>
<p>����� � ������ �� �����: <strong><?= $Sum ?> �.</strong></p>
<? if (CSalePaySystemAction::GetParamValue("IS_TEST") <> ''):
?>
<form name="ShopForm" action="https://demomoney.yandex.ru/eshop.xml" method="post" target="_blank">
    <? else:
    ?>
    <form name="ShopForm" action="http://money.yandex.ru/eshop.xml" method="post">
        <? endif; ?>

        <input name="ShopID" value="<?= $ShopID ?>" type="hidden">
        <input name="scid" value="<?= $scid ?>" type="hidden">
        <input name="customerNumber" value="<?= $customerNumber ?>" type="hidden">
        <input name="orderNumber" value="<?= $orderNumber ?>" type="hidden">
        <input name="Sum" value="<?= $Sum ?>" type="hidden">
        <input name="cms_name" value="1C-Bitrix" type="hidden">
        <p>������ ������:</p>
        <input name="OrderDetails" value="����� �<?= $orderNumber ?> (<?= $orderDate ?>)" type="hidden">
        <input name="BuyButton" value="��������" type="submit" class="btn btn-primary">

        <div class="alert alert-info mt-4"><strong>��������!</strong> ������� ������� �� ��������� ������� �Money -
            ����������, ����������, ������ ����������� ��� ������ ������.
        </div>
    </form>