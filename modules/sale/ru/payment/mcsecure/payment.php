<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
$ORDER_ID = IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);
?>
<p>¬ы хотите оплатить по кредитной карте через процессинговый центр платежной системы <strong>»ћѕЁ —Ѕанка</strong>.</p>
<p>CчЄт є <?echo $ORDER_ID." от ".CSalePaySystemAction::GetParamValue("DATE_INSERT")?></p>
<p>—умма к оплате по счету: <strong><?echo SaleFormatCurrency(CSalePaySystemAction::GetParamValue("SHOULD_PAY"), CSalePaySystemAction::GetParamValue("CURRENCY"))?></strong></p>

<!-- START IMPEXBANK SMART-CARD PAY FORM -->
<form method=post action="https://e-commerce.impexbank.ru/vsmc3ds/3dsproxy_init.jsp" target="_blank" class="mb-3">
	<input type="hidden" name="AcquirerBin" value="<?echo CSalePaySystemAction::GetParamValue("AcquirerBin")?>">
	<input type="hidden" name="PurchaseAmt" value="<?echo CSalePaySystemAction::GetParamValue("SHOULD_PAY")?>">
	<input type="hidden" name="PurchaseDesc" value="<?echo $ORDER_ID?>">
	<input type="hidden" name="CountryCode" value="643">
	<input type="hidden" name="CurrencyCode" value="810">
	<input type="hidden" name="MerchantName" value="<?echo CSalePaySystemAction::GetParamValue("MerchantName")?>">
	<input type="hidden" name="MerchantURL" value="<?echo CSalePaySystemAction::GetParamValue("MerchantURL")?>">
	<input type="hidden" name="MerchantCity" value="<?echo CSalePaySystemAction::GetParamValue("MerchantCity")?>">
	<input type="hidden" name="MerchantID" value="<?echo CSalePaySystemAction::GetParamValue("MerchantID")?>">
	<input type="submit" value="ќплатить" class="btn btn-primary">
</form>
<!-- END IMPEXBANK SMART-CARD PAY FORM -->

<div class="alert alert-warning" role="alert">
	<p class="mb-1"><strong>ќбратите внимание!</strong></p>
	<p class="mb-1">¬се финансовые операции осуществл€ютс€ в процессинговом центре платежной системы »ћѕЁ —Ѕанка.</p>
	<p class="mb-0">¬се данные, необходимые дл€ осуществлени€ платежа, гарантированно защищены платежной системой »ћѕЁ —Ѕанка.</p>
</div>