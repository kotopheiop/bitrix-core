<?

global $MESS;
$MESS["SPCP_DTITLE"] = "�Kassa";
$MESS["SHOP_ID"] = "������������� �������� � ��� (ShopID)";
$MESS["SHOP_ID_DESCR"] = "��� ��������, ������� ������� �� �Kassa";
$MESS["SCID"] = "����� ������� �������� � ��� (scid)";
$MESS["SCID_DESCR"] = "";
$MESS["SCID_DESCT"] = "";
$MESS["ORDER_ID"] = "����� ������";
$MESS["ORDER_ID_DESCR"] = "";
$MESS["SHOP_KEY"] = "������ ��������";
$MESS["SHOP_KEY_DESCR"] = "������ �������� �� �Kassa";
$MESS["SHOULD_PAY"] = "����� ������";
$MESS["SHOULD_PAY_DESCR"] = "����� � ������";
$MESS["ORDER_DATE"] = "���� �������� ������";
$MESS["ORDER_DATE_DESCR"] = "";
$MESS["IS_TEST"] = "�������� �����";
$MESS["IS_TEST_DESCR"] = "���� ������ �������� - ������� ����� �������� � ������� ������";
$MESS["PYM_CHANGE_STATUS_PAY"] = "������������� ���������� ����� ��� ��������� ��������� ������� ������";
$MESS["PYM_CHANGE_STATUS_PAY_DESC"] = "Y - ����������, N - �� ����������.";
$MESS["SALE_TYPE_PAYMENT"] = "��� �������� �������";
$MESS["SALE_YMoney"] = "�Money";
$MESS["SALE_YCards"] = "���������� �����";
$MESS["SALE_YTerminals"] = "���������";
$MESS["SALE_YMobile"] = "��������� �������";
$MESS["SALE_YSberbank"] = "�������� ������";
$MESS["SALE_YmPOS"] = "��������� �������� (mPOS)";
$MESS["SALE_YAlphaClick"] = "�����-����";
$MESS["SALE_YMasterPass"] = "MasterPass";
$MESS["SALE_YPromsvyazbank"] = "�������������";
$MESS["SALE_YQiwi"] = "QIWI Wallet";
$MESS["SALE_YTinkoffBank"] = "�����������";
$MESS["SALE_YKuppiRu"] = "������������� ������";

$MESS["SPCP_DDESCR"] = "������ ����� ����� ������ �������� <a href=\"https://yookassa.ru\" target=\"_blank\">https://yookassa.ru</a>
<br/>������������ �������� commonHTTP-3.0
<br/><br/>
<input
	id=\"https_check_button\"
	type=\"button\"
	value=\"�������� HTTPS\"
	title=\"�������� ����������� ����� �� ��������� HTTPS. ���������� ��� ���������� ������ ��������� �������\"
	onclick=\"
		var checkHTTPS = function(){
			BX.showWait();
			BX.ajax.post('/bitrix/admin/sale_pay_system_edit.php', '" . CUtil::JSEscape(bitrix_sessid_get()) . "&https_check=Y', function (result)
			{
				BX.closeWait();
				var res = eval( '('+result+')' );
				BX('https_check_result').innerHTML = '&nbsp;' + res['text'];

				BX.removeClass(BX('https_check_result'), 'https_check_success');
				BX.removeClass(BX('https_check_result'), 'https_check_fail');

				if (res['status'] == 'ok')
					BX.addClass(BX('https_check_result'), 'https_check_success');
				else
					BX.addClass(BX('https_check_result'), 'https_check_fail');
			});
		};
		checkHTTPS();\"
	/>
<span id=\"https_check_result\"></span>
<br/>";
?>
