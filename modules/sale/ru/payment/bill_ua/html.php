<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?><?
$ORDER_ID = IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);
if (!is_array($arOrder))
    $arOrder = CSaleOrder::GetByID($ORDER_ID);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>�������</title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= LANG_CHARSET ?>">
    <style>
        table {
            border-collapse: collapse;
        }

        table.acc td {
            padding: 0pt;
            vertical-align: top;
        }

        table.it td {
            border: 1pt solid #000000;
            padding: 0pt 3pt;
        }

        table.sign td {
            font-weight: bold;
            vertical-align: bottom;
        }
    </style>
</head>

<?

if ($_REQUEST['BLANK'] == 'Y')
    $blank = true;

$pageWidth = 595.28;
$pageHeight = 841.89;

$background = '#ffffff';
if (CSalePaySystemAction::GetParamValue('BACKGROUND', false)) {
    $path = CSalePaySystemAction::GetParamValue('BACKGROUND', false);
    if (intval($path) > 0) {
        if ($arFile = CFile::GetFileArray($path))
            $path = $arFile['SRC'];
    }

    $backgroundStyle = CSalePaySystemAction::GetParamValue('BACKGROUND_STYLE', false);
    if (!in_array($backgroundStyle, array('none', 'tile', 'stretch')))
        $backgroundStyle = 'none';

    if ($path) {
        switch ($backgroundStyle) {
            case 'none':
                $background = "url('" . $path . "') 0 0 no-repeat";
                break;
            case 'tile':
                $background = "url('" . $path . "') 0 0 repeat";
                break;
            case 'stretch':
                $background = sprintf(
                    "url('%s') 0 0 repeat-y; background-size: %.02fpt %.02fpt",
                    $path, $pageWidth, $pageHeight
                );
                break;
        }
    }
}

$margin = array(
    'top' => intval(CSalePaySystemAction::GetParamValue('MARGIN_TOP', false) ?: 15) * 72 / 25.4,
    'right' => intval(CSalePaySystemAction::GetParamValue('MARGIN_RIGHT', false) ?: 15) * 72 / 25.4,
    'bottom' => intval(CSalePaySystemAction::GetParamValue('MARGIN_BOTTOM', false) ?: 15) * 72 / 25.4,
    'left' => intval(CSalePaySystemAction::GetParamValue('MARGIN_LEFT', false) ?: 20) * 72 / 25.4
);

$width = $pageWidth - $margin['left'] - $margin['right'];

?>

<body style="margin: 0pt; padding: 0pt;"<? if ($_REQUEST['PRINT'] == 'Y') { ?> onload="setTimeout(window.print, 0);"<? } ?>>

<div style="margin: 0pt; padding: <?= join('pt ', $margin); ?>pt; width: <?= $width; ?>pt; background: <?= $background; ?>">

    <b><?= sprintf(
            "������� �� ������ �%s �� %s",
            htmlspecialcharsbx($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ACCOUNT_NUMBER"]),
            CSalePaySystemAction::GetParamValue("DATE_INSERT", false)
        ); ?></b>
    <br>
    <br>

    <?

    $buyerPhone = CSalePaySystemAction::GetParamValue("BUYER_PHONE", false);
    $buyerFax = CSalePaySystemAction::GetParamValue("BUYER_FAX", false);

    ?>

    <table class="acc">
        <tr>
            <td>������������:</td>
            <td style="padding-left: 4pt; ">
                <?= CSalePaySystemAction::GetParamValue("SELLER_NAME", false); ?>
                <br>
                �/� <?= CSalePaySystemAction::GetParamValue("SELLER_RS", false); ?>,
                ���� <?= CSalePaySystemAction::GetParamValue("SELLER_BANK", false); ?>,
                ��� <?= CSalePaySystemAction::GetParamValue("SELLER_MFO", false); ?>
                <br>
                �������� ������: <?= CSalePaySystemAction::GetParamValue("SELLER_ADDRESS", false); ?>,
                ���.: <?= CSalePaySystemAction::GetParamValue("SELLER_PHONE", false); ?>
                <br>
                ������: <?= CSalePaySystemAction::GetParamValue("SELLER_EDRPOY", false); ?>,
                ���: <?= CSalePaySystemAction::GetParamValue("SELLER_IPN", false); ?>,
                � ���. ���: <?= CSalePaySystemAction::GetParamValue("SELLER_PDV", false); ?>
                <? if (CSalePaySystemAction::GetParamValue("SELLER_SYS", false)) { ?>
                    <br>
                    <?= CSalePaySystemAction::GetParamValue("SELLER_SYS", false); ?>
                <? } ?>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>��������:</td>
            <td style="padding-left: 4pt; ">
                <?= CSalePaySystemAction::GetParamValue("BUYER_NAME", false); ?>
                <? if ($buyerPhone || $buyerFax) { ?>
                    <br>
                    <? if ($buyerPhone) { ?>���.: <?= $buyerPhone; ?><? if ($buyerFax) { ?>, <? } ?><? } ?>
                    <? if ($buyerFax) { ?>����: <?= $buyerFax; ?><? } ?>
                <? } ?>
                <? if (CSalePaySystemAction::GetParamValue("BUYER_ADDRESS", false)) { ?>
                    <br>
                    ������: <?= CSalePaySystemAction::GetParamValue("BUYER_ADDRESS", false); ?>
                <? } ?>
            </td>
        </tr>
    </table>
    <br>

    <? if (CSalePaySystemAction::GetParamValue("BUYER_DOGOVOR", false)) { ?>
        ������: <?= CSalePaySystemAction::GetParamValue("BUYER_DOGOVOR", false); ?>
        <br>
    <? } ?>
    <br>

    <?

    $dbBasket = CSaleBasket::GetList(
        array("DATE_INSERT" => "ASC", "NAME" => "ASC"),
        array("ORDER_ID" => $ORDER_ID),
        false, false,
        array("ID", "PRICE", "CURRENCY", "QUANTITY", "NAME", "VAT_RATE", "MEASURE_NAME")
    );
    if ($arBasket = $dbBasket->Fetch()) {
        $arCells = array();
        $arProps = array();

        $n = 0;
        $sum = 0.00;
        $vat = 0;
        do {
            // props in product basket
            $arProdProps = array();
            $dbBasketProps = CSaleBasket::GetPropsList(
                array("SORT" => "ASC", "ID" => "DESC"),
                array(
                    "BASKET_ID" => $arBasket["ID"],
                    "!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID")
                ),
                false,
                false,
                array("ID", "BASKET_ID", "NAME", "VALUE", "CODE", "SORT")
            );
            while ($arBasketProps = $dbBasketProps->GetNext()) {
                if (!empty($arBasketProps) && $arBasketProps["VALUE"] != "")
                    $arProdProps[] = $arBasketProps;
            }
            $arBasket["PROPS"] = $arProdProps;

            $productName = $arBasket["NAME"];
            if ($productName == "OrderDelivery")
                $productName = "��������";
            else if ($productName == "OrderDiscount")
                $productName = "������";

            $arCells[++$n] = array(
                1 => $n,
                htmlspecialcharsbx($productName),
                roundEx($arBasket["QUANTITY"], SALE_VALUE_PRECISION),
                $arBasket["MEASURE_NAME"] ? htmlspecialcharsbx($arBasket["MEASURE_NAME"]) : '��.',
                SaleFormatCurrency($arBasket["PRICE"], $arBasket["CURRENCY"], true),
                roundEx($arBasket["VAT_RATE"] * 100, SALE_VALUE_PRECISION) . "%",
                SaleFormatCurrency(
                    $arBasket["PRICE"] * $arBasket["QUANTITY"],
                    $arBasket["CURRENCY"],
                    true
                )
            );

            $arProps[$n] = array();
            foreach ($arBasket["PROPS"] as $vv)
                $arProps[$n][] = htmlspecialcharsbx(sprintf("%s: %s", $vv["NAME"], $vv["VALUE"]));

            $sum += doubleval($arBasket["PRICE"] * $arBasket["QUANTITY"]);
            $vat = max($vat, $arBasket["VAT_RATE"]);
        } while ($arBasket = $dbBasket->Fetch());

        if (DoubleVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE_DELIVERY"]) > 0) {
            $arDelivery_tmp = CSaleDelivery::GetByID($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DELIVERY_ID"]);

            $sDeliveryItem = "��������";
            if (strlen($arDelivery_tmp["NAME"]) > 0)
                $sDeliveryItem .= sprintf(" (%s)", $arDelivery_tmp["NAME"]);
            $arCells[++$n] = array(
                1 => $n,
                htmlspecialcharsbx($sDeliveryItem),
                1,
                '',
                SaleFormatCurrency(
                    $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE_DELIVERY"],
                    $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
                    true
                ),
                roundEx($vat * 100, SALE_VALUE_PRECISION) . "%",
                SaleFormatCurrency(
                    $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE_DELIVERY"],
                    $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
                    true
                )
            );

            $sum += doubleval($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE_DELIVERY"]);
        }

        $items = $n;
        /*
            if ($sum < $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE"])
            {
                $arCells[++$n] = array(
                    1 => null,
                    null,
                    null,
                    null,
                    null,
                    "�������:",
                    SaleFormatCurrency($sum, $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"], true)
                );
            }
        */
        $orderTax = 0;
        $dbTaxList = CSaleOrderTax::GetList(
            array("APPLY_ORDER" => "ASC"),
            array("ORDER_ID" => $ORDER_ID)
        );

        while ($arTaxList = $dbTaxList->Fetch()) {
            $arCells[++$n] = array(
                1 => null,
                null,
                null,
                null,
                null,
                htmlspecialcharsbx(sprintf(
                    "%s%s%s:",
                    ($arTaxList["IS_IN_PRICE"] == "Y") ? "� ���� ���� " : "",
                    ($vat <= 0) ? $arTaxList["TAX_NAME"] : "���",
                    ($vat <= 0 && $arTaxList["IS_PERCENT"] == "Y")
                        ? sprintf(' (%s%%)', roundEx($arTaxList["VALUE"], SALE_VALUE_PRECISION))
                        : ""
                )),
                SaleFormatCurrency(
                    $arTaxList["VALUE_MONEY"],
                    $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
                    true
                )
            );

            $orderTax += $arTaxList["VALUE_MONEY"];
        }

        if (DoubleVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SUM_PAID"]) > 0) {
            $arCells[++$n] = array(
                1 => null,
                null,
                null,
                null,
                null,
                "��� ��������:",
                SaleFormatCurrency(
                    $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SUM_PAID"],
                    $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
                    true
                )
            );
        }

        if (DoubleVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DISCOUNT_VALUE"]) > 0) {
            $arCells[++$n] = array(
                1 => null,
                null,
                null,
                null,
                null,
                "������:",
                SaleFormatCurrency(
                    $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DISCOUNT_VALUE"],
                    $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
                    true
                )
            );
        }

        $arCells[++$n] = array(
            1 => null,
            null,
            null,
            null,
            null,
            $vat <= 0 ? "������ ��� ���:" : "������:",
            SaleFormatCurrency(
                $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"],
                $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
                true
            )
        );

        $showVat = false;
    }

    $arCurFormat = CCurrencyLang::GetCurrencyFormat($GLOBALS['SALE_INPUT_PARAMS']['ORDER']['CURRENCY']);
    $currency = trim(str_replace('#', '', $arCurFormat['FORMAT_STRING']));
    ?>
    <table class="it" width="100%">
        <tr>
            <td>
                <nobr>�</nobr>
            </td>
            <td>
                <nobr>�����/�������</nobr>
            </td>
            <td>
                <nobr>ʳ�-���</nobr>
            </td>
            <td>
                <nobr>��.</nobr>
            </td>
            <td>
                <nobr><? if ($vat <= 0) { ?>ֳ�� ��� ���<? } else { ?>ֳ�� � ���<? } ?>, <?= $currency; ?></nobr>
            </td>
            <? if ($showVat) { ?>
                <td>
                    <nobr>������ ���</nobr>
                </td>
            <? } ?>
            <td>
                <nobr><? if ($vat <= 0) { ?>���� ��� ���<? } else { ?>���� � ���<? } ?>, <?= $currency; ?></nobr>
            </td>
        </tr>
        <?

        $rowsCnt = count($arCells);
        for ($n = 1; $n <= $rowsCnt; $n++) {
            $accumulated = 0;

            ?>
            <tr valign="top">
                <? if (!is_null($arCells[$n][1])) { ?>
                    <td align="center"><?= $arCells[$n][1]; ?></td>
                <? } else {
                    $accumulated++;
                } ?>
                <? if (!is_null($arCells[$n][2])) { ?>
                    <td align="left"
                        style="word-break: break-word; word-wrap: break-word; <? if ($accumulated) { ?>border-width: 0pt 1pt 0pt 0pt; <? } ?>"
                        <? if ($accumulated) { ?>colspan="<?= ($accumulated + 1); ?>"<? $accumulated = 0;
                    } ?>>
                        <?= $arCells[$n][2]; ?>
                        <? if (isset($arProps[$n]) && is_array($arProps[$n])) { ?>
                            <? foreach ($arProps[$n] as $property) { ?>
                                <br>
                                <small><?= $property; ?></small>
                            <? } ?>
                        <? } ?>
                    </td>
                <? } else {
                    $accumulated++;
                } ?>
                <? for ($i = 3; $i <= 7; $i++) { ?>
                    <? if (!is_null($arCells[$n][$i])) { ?>
                        <? if ($i != 6 || $showVat || is_null($arCells[$n][2])) { ?>
                            <td align="right"
                                <? if ($accumulated) { ?>
                                    style="border-width: 0pt 1pt 0pt 0pt"
                                    colspan="<?= (($i == 6 && !$showVat) ? $accumulated : $accumulated + 1); ?>"
                                    <? $accumulated = 0;
                                } ?>>
                                <nobr><?= $arCells[$n][$i]; ?></nobr>
                            </td>
                        <? }
                    } else {
                        $accumulated++;
                    }
                } ?>
            </tr>
            <?

        }

        ?>
    </table>
    <br>

    <b><?= sprintf(
            "������ �����������: %s, �� ���� %s",
            $items,
            ($arOrder["CURRENCY"] == "UAH")
                ? Number2Word_Rus(
                $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"],
                "Y",
                $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"]
            )
                : SaleFormatCurrency(
                $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"],
                $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
                false
            )
        ); ?></b>
    <br>

    <? if ($vat > 0) { ?>
        <b><?= sprintf(
                "� �.�. ���: %s",
                ($arOrder["CURRENCY"] == "UAH")
                    ? Number2Word_Rus($orderTax, "Y", $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"])
                    : SaleFormatCurrency($orderTax, $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"], false)
            ); ?></b>
    <? } else { ?>
        <b>��� ���</b>
    <? } ?>
    <br>
    <br>

    <? if (CSalePaySystemAction::GetParamValue("COMMENT1", false) || CSalePaySystemAction::GetParamValue("COMMENT2", false)) { ?>
        <b>����� �� ��������</b>
        <br>
        <? if (CSalePaySystemAction::GetParamValue("COMMENT1", false)) { ?>
            <?= nl2br(HTMLToTxt(preg_replace(
                array('#</div>\s*<div[^>]*>#i', '#</?div>#i'), array('<br>', '<br>'),
                htmlspecialcharsback(CSalePaySystemAction::GetParamValue("COMMENT1", false))
            ), '', array(), 0)); ?>
            <br>
            <br>
        <? } ?>
        <? if (CSalePaySystemAction::GetParamValue("COMMENT2", false)) { ?>
            <?= nl2br(HTMLToTxt(preg_replace(
                array('#</div>\s*<div[^>]*>#i', '#</?div>#i'), array('<br>', '<br>'),
                htmlspecialcharsback(CSalePaySystemAction::GetParamValue("COMMENT2", false))
            ), '', array(), 0)); ?>
            <br>
            <br>
        <? } ?>
    <? } ?>

    <div style="border-bottom: 1pt solid #000000; width:100%; ">&nbsp;</div>

    <? if (!$blank) { ?>
        <div style="position: relative; "><?= CFile::ShowImage(
                CSalePaySystemAction::GetParamValue("PATH_TO_STAMP", false),
                160, 160,
                'style="position: absolute; left: 40pt; "'
            ); ?></div>
    <? } ?>

    <br>

    <div style="position: relative">
        <table class="sign">
            <tr>
                <td>�������(��):&nbsp;</td>
                <td style="width: 160pt; border: 1pt solid #000000; border-width: 0pt 0pt 1pt 0pt; text-align: center; ">
                    <? if (!$blank) { ?>
                        <?= CFile::ShowImage(CSalePaySystemAction::GetParamValue("SELLER_ACC_SIGN", false), 200, 50); ?>
                    <? } ?>
                </td>
                <td style="width: 160pt; ">
                    <input
                            style="border: none; background: none; width: 100%; "
                            type="text"
                            value="<?= CSalePaySystemAction::GetParamValue("SELLER_ACC", false); ?>"
                    >
                </td>
                <td style="width: 20pt; ">&nbsp;</td>
                <td>������:&nbsp;</td>
                <td style="width: 160pt; border: 1pt solid #000000; border-width: 0pt 0pt 1pt 0pt; ">
                    <input
                            style="border: none; background: none; width: 100%; text-align: center; "
                            type="text"
                            value="<?= CSalePaySystemAction::GetParamValue("SELLER_ACC_POS", false); ?>"
                    >
                </td>
            </tr>
        </table>
    </div>

    <br>
    <br>

    <? if (CSalePaySystemAction::GetParamValue("DATE_PAY_BEFORE", false)) { ?>
        <div style="text-align: right; "><b><?= sprintf(
                    "������� ������ �� ������ �� %s",
                    ConvertDateTime(CSalePaySystemAction::GetParamValue("DATE_PAY_BEFORE", false), FORMAT_DATE)
                        ?: CSalePaySystemAction::GetParamValue("DATE_PAY_BEFORE", false)
                ); ?></b></div>
    <? } ?>

</div>

</body>
</html>