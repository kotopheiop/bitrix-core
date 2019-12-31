<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?><?

$orderIsPaid = $_POST["orderIsPaid"];
$orderSumAmount = $_POST["orderSumAmount"];
$orderSumCurrencyPaycash = $_POST["orderSumCurrencyPaycash"];
$orderSumBankPaycash = $_POST["orderSumBankPaycash"];
$action = $_POST["action"];
$orderCreatedDatetime = $_POST["orderCreatedDatetime"];
$paymentType = $_POST["paymentType"];
$customerNumber = IntVal($_POST["customerNumber"]);
$invoiceId = $_POST["invoiceId"];
$md5 = $_POST["md5"];
$paymentDateTime = $_POST["paymentDateTime"];

$bCorrectPayment = True;
if (!($arOrder = CSaleOrder::GetByID($customerNumber))) {
    $bCorrectPayment = False;
    $code = "200"; //�������� ���������
    $techMessage = "ID ������ ����������.";
}

if ($bCorrectPayment)
    CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"]);

$Sum = CSalePaySystemAction::GetParamValue("SHOULD_PAY");
$Sum = number_format($Sum, 2, ',', '');
$shopId = CSalePaySystemAction::GetParamValue("SHOP_ID");
$scid = CSalePaySystemAction::GetParamValue("SCID");
$customerNumber = CSalePaySystemAction::GetParamValue("ORDER_ID");
$changePayStatus = trim(CSalePaySystemAction::GetParamValue("CHANGE_STATUS_PAY"));
$shopPassword = CSalePaySystemAction::GetParamValue("SHOP_KEY");

if (strlen($shopPassword) <= 0)
    $bCorrectPayment = False;

$strCheck = md5(implode(";", array($orderIsPaid, $orderSumAmount, $orderSumCurrencyPaycash, $orderSumBankPaycash, $shopId, $invoiceId, $customerNumber, $shopPassword)));

if ($bCorrectPayment && ToUpper($md5) != ToUpper($strCheck)) {
    $bCorrectPayment = False;
    $code = "1"; // ������ �����������
}

if ($bCorrectPayment) {
    if ($action == "Check") {
        if (DoubleVal($arOrder["PRICE"]) == DoubleVal($orderSumAmount))
            $code = "0";
        else {
            $code = "100"; //�������� ���������	
            $techMessage = "����� ������ �� �����.";
        }
    } elseif ($action == "PaymentSuccess") {
        $strPS_STATUS_DESCRIPTION = "";
        $strPS_STATUS_DESCRIPTION .= "����� ����������� - " . $customerNumber . "; ";
        $strPS_STATUS_DESCRIPTION .= "���� ������� - " . $paymentDateTime . "; ";
        $strPS_STATUS_DESCRIPTION .= "��� ������������� ������� - " . $orderIsPaid . "; ";
        $strPS_STATUS_MESSAGE = "";

        $arFields = array(
            "PS_STATUS" => "Y",
            "PS_STATUS_CODE" => substr($action, 0, 5),
            "PS_STATUS_DESCRIPTION" => $strPS_STATUS_DESCRIPTION,
            "PS_STATUS_MESSAGE" => $strPS_STATUS_MESSAGE,
            "PS_SUM" => $orderSumAmount,
            "PS_CURRENCY" => $orderSumCurrencyPaycash,
            "PS_RESPONSE_DATE" => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
        );

        // You can comment this code if you want PAYED flag not to be set automatically
        if (FloatVal($arOrder["PRICE"]) == FloatVal($orderSumAmount) && IntVal($orderIsPaid) == 1) {
            if ($changePayStatus == "Y") {
                if ($arOrder["PAYED"] == "Y")
                    $code = "0";
                else {
                    if (!CSaleOrder::PayOrder($arOrder["ID"], "Y", true, true)) {
                        $code = "1000";
                        $techMessage = "������ ������ ������.";
                    } else
                        $code = "0";
                }
            }
        } else {
            $code = "200"; //�������� ���������
            $techMessage = "����� ������ �� �����.";
        }

        if (CSaleOrder::Update($arOrder["ID"], $arFields))
            if (strlen($techMessage) <= 0 && strlen($code) <= 0)
                $code = "0";
    } else {
        $code = "200"; //�������� ���������
        $techMessage = "�� �������� ��� �������.";
    }
}

$APPLICATION->RestartBuffer();
$dateISO = date("Y-m-d\TH:i:s") . substr(date("O"), 0, 3) . ":" . substr(date("O"), -2, 2);
header("Content-Type: text/xml");
header("Pragma: no-cache");
$text = "<" . "?xml version=\"1.0\" encoding=\"windows-1251\"?" . ">\n";
$text .= "<response performedDatetime=\"" . $dateISO . "\">";
if (strlen($techMessage) > 0)
    $text .= "<result code=\"" . $code . "\" action=\"" . htmlspecialcharsbx($action) . "\" shopId=\"" . $shopId . "\" invoiceId=\"" . htmlspecialcharsbx($invoiceId) . "\" techMessage=\"" . $techMessage . "\"/>";
else
    $text .= "<result code=\"" . $code . "\" action=\"" . htmlspecialcharsbx($action) . "\" shopId=\"" . $shopId . "\" invoiceId=\"" . htmlspecialcharsbx($invoiceId) . "\"/>";
$text .= "</response>";
echo $text;
die();
?>