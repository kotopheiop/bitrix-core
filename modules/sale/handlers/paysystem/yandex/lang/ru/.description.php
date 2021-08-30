<?php

$MESS['SALE_HPS_YANDEX'] = '�Kassa (���������� ������)';
$MESS["SALE_HPS_YANDEX_SHOP_ID"] = "������������� �������� � ��� (ShopID)";
$MESS["SALE_HPS_YANDEX_SHOP_ID_DESC"] = "��� ��������, ������� ������� �� �Kassa";
$MESS["SALE_HPS_YANDEX_SCID"] = "����� ������� �������� � ��� (scid)";
$MESS["SALE_HPS_YANDEX_SCID_DESC"] = "����� ������� �������� � ��� (scid)";
$MESS["SALE_HPS_YANDEX_PAYMENT_ID"] = "����� ������";
$MESS["SALE_HPS_YANDEX_SHOP_KEY"] = "������ ��������";
$MESS["SALE_HPS_YANDEX_SHOP_KEY_DESC"] = "������ �������� �� �Kassa";
$MESS["SALE_HPS_YANDEX_SHOULD_PAY"] = "����� � ������";
$MESS["SALE_HPS_YANDEX_PAYMENT_DATE"] = "���� �������� ������";
$MESS["SALE_HPS_YANDEX_IS_TEST"] = "�������� �����";
$MESS["SALE_HPS_YANDEX_CHANGE_STATUS_PAY"] = "������������� ���������� ����� ��� ��������� ��������� ������� ������";
$MESS["SALE_HPS_YANDEX_PAYMENT_TYPE"] = "��� �������� �������";
$MESS["SALE_HPS_YANDEX_BUYER_ID"] = "��� ����������";

$MESS["SALE_HPS_YANDEX_RETURN"] = "�������� �������� �� ��������������";
$MESS["SALE_HPS_YANDEX_RESTRICTION"] = "����������� �� ����� �������� ������� �� ������� ������, ������� ������� ����������";
$MESS["SALE_HPS_YANDEX_COMMISSION"] = "��� ������� ��� ����������";
$MESS["SALE_HPS_YANDEX_REFERRER"] = "<a href=\"https://money.yandex.ru/joinups/?source=bitrix24\" target=\"_blank\">������� �����������</a>";

$MESS["SALE_HPS_YANDEX_DESCRIPTION"] = "������ ����� ����� ������ �������� <a href=\"https://yookassa.ru\" target=\"_blank\">https://yookassa.ru</a>
<br/>������������ �������� commonHTTP-3.0
<br/><br/>
<input
	id=\"https_check_button\"
	type=\"button\"
	value=\"�������� HTTPS\"
	title=\"�������� ����������� ����� �� ��������� HTTPS. ���������� ��� ���������� ������ ��������� �������\"
	onclick=\"
		var checkHTTPS = function(){
			BX.showWait()
			var postData = {
				action: 'checkHttps',
				https_check: 'Y',
				lang: BX.message('LANGUAGE_ID'),
				sessid: BX.bitrix_sessid()
			};

			BX.ajax({
				timeout: 30,
				method: 'POST',
				dataType: 'json',
				url: '/bitrix/admin/sale_pay_system_ajax.php',
				data: postData,

				onsuccess: function (result)
				{
					BX.closeWait();
					BX.removeClass(BX('https_check_result'), 'https_check_success');
					BX.removeClass(BX('https_check_result'), 'https_check_fail');

					BX('https_check_result').innerHTML = '&nbsp;' + result.CHECK_MESSAGE;
					if (result.CHECK_STATUS == 'OK')
						BX.addClass(BX('https_check_result'), 'https_check_success');
					else
						BX.addClass(BX('https_check_result'), 'https_check_fail');
				},
				onfailure : function()
				{
					BX.closeWait();
					BX.removeClass(BX('https_check_result'), 'https_check_success');

					BX('https_check_result').innerHTML = '&nbsp;' + BX.message('SALE_PS_YANDEX_ERROR');
					BX.addClass(BX('https_check_result'), 'https_check_fail');
				}
			});
		};
		checkHTTPS();\"
	/>
<span id=\"https_check_result\"></span>
<br/>
<br/>";