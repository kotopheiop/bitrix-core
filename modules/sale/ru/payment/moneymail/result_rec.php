<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?><?
// ���� ��������� ���������, ���������� ������� GET � ������ � ������ PAYMENT
if ($mode == "PAYMENT") {
    if (intval($issuer_id) > 0) {
        $bCorrectPayment = true;
        if (!($arOrder = CSaleOrder::GetByID(intval($issuer_id)))) {
            $bCorrectPayment = false;
        }

        if ($bCorrectPayment) {
            CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"]);
        }

        $PASS = CSalePaySystemAction::GetParamValue("PASS");

        if ($PASS == '') {
            $bCorrectPayment = false;
        } else {
            $strCheck = md5(
                $PASS . "PAYMENT" . $invoice . $issuer_id . $payment_id . $payer . $currency . $value . $date . $confirmed
            );
        }

        if ($bCorrectPayment && $CHECKSUM != $strCheck) {
            $bCorrectPayment = false;
        }

        if ($bCorrectPayment) {
            $strPS_STATUS_DESCRIPTION = "";
            $strPS_STATUS_DESCRIPTION .= "����� ����� - " . $invoice . "; ";
            $strPS_STATUS_DESCRIPTION .= "����� ������� - " . $payment_id . "; ";
            $strPS_STATUS_DESCRIPTION .= "���� ������� - " . $date . "";
            $strPS_STATUS_DESCRIPTION .= "��� ������������� ������� - " . $confirmed . "";

            $strPS_STATUS_MESSAGE = "";
            if (isset($payer) && $payer <> '') {
                $strPS_STATUS_MESSAGE .= "e-mail ���������� - " . $payer . "; ";
            }

            $arFields = array(
                "PS_STATUS" => "Y",
                "PS_STATUS_CODE" => "-",
                "PS_STATUS_DESCRIPTION" => $strPS_STATUS_DESCRIPTION,
                "PS_STATUS_MESSAGE" => $strPS_STATUS_MESSAGE,
                "PS_SUM" => $value,
                "PS_CURRENCY" => $currency,
                "PS_RESPONSE_DATE" => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
                "USER_ID" => $arOrder["USER_ID"]
            );

            // You can comment this code if you want PAYED flag not to be set automatically
            if ($arOrder["PRICE"] == $value
                && intval($confirmed) == 1) {
                CSaleOrder::PayOrder($arOrder["ID"], "Y");
            }

            if (CSaleOrder::Update($arOrder["ID"], $arFields)) {
                echo "OK";
            }
        }
    } else {
        echo "��� ������ �� �����";
    }
} else {
    echo "��� �������� �� PAYMENT";
}
?>
