<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>���������</title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= LANG_CHARSET ?>">
    <style type="text/css">
        H1 {
            font-size: 14pt;
        }
    </style>
</head>
<body bgColor="#ffffff">
<TABLE height="538" bgColor="#ffffff" border=1 borderColor="#000000" cellPadding=3 cellSpacing=0 width="515">
    <!-- ��������� -->
    <TR>
        <TD width="170" rowspan="4" valign="top"><B>&nbsp; <font size="-1">���������</font></B></TD>
        <TD valign="top" colspan="2">
            <div align="right"><b><font size="-1">����� � ��-4</font></b></div>
            <font size="-1">
                <?= htmlspecialcharsEx(CSalePaySystemAction::GetParamValue("SELLER_PARAMS")) ?>
            </font>
            <hr size="1" color="black">
            <font size="-1">
                <?= htmlspecialcharsEx(CSalePaySystemAction::GetParamValue("PAYER_NAME")) ?>
            </font>
            <hr size="1" color="black">
            <center>
                <font style="font-size: 7pt;">(�.�.�. �����������)</font>
            </center>
        </TD>
    </TR>
    <TR>
        <TD width=270 height="27"><font size="-1">������������ �������</font></TD>
        <TD width=100 align="center" height="27"><font size="-1">�����</font></TD>
    </TR>
    <!-- ���������� ������ (������) -->
    <TR>
        <TD vAlign=middle width=270 height="53"><font size="-1"><STRONG>������ ������ �
                    <?= intval($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]) ?>
                    ��
                    <?= htmlspecialcharsbx($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_INSERT_DATE"]) ?></STRONG>
            </font></TD>
        <TD valign="middle" align="center"><b> <font size="-1">
                    <?= SaleFormatCurrency(
                        $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"],
                        $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"]
                    ) ?>
                </font></b></TD>
    </TR>
    <!-- ���������� ������ (�����) -->
    <TR>
        <TD colSpan="2"><font style="font-size: 9pt">
                <br>� ��������� ������ ��������� � ��������� ��������� �����, � �.�. � ������ ��������� ����� �� ������
                �����, ���������� � ��������.
                <br><br>_______________ "____" __________ 201__ �.</font></TD>
    </TR>
    <!-- ��������� -->
    <TR>
        <TD width="170" rowspan="4" valign="bottom"><B>&nbsp; <font size="-1">���������</font></B></TD>
        <TD valign="top" colspan="2">
            <div align="right"><b><font size="-1">����� � ��-4</font></b></div>
            <font size="-1">
                <?= htmlspecialcharsEx(CSalePaySystemAction::GetParamValue("SELLER_PARAMS")) ?>
            </font>
            <hr size="1" color="black">
            <font size="-1">
                <?= htmlspecialcharsEx(CSalePaySystemAction::GetParamValue("PAYER_NAME")) ?>
            </font>
            <hr size="1" color="black">
            <center>
                <font style="font-size: 7pt;">(�.�.�. �����������)</font>
            </center>
        </TD>
    </TR>
    <TR>
        <TD height=27 width=270><font size="-1">������������ �������</font></TD>
        <TD height=27 width=100 align="center"><font size="-1"> ����� </font></TD>
    </TR>
    <!-- ���������� ������ (������) -->
    <TR>
        <TD height=53 vAlign=middle width=270><font size="-1"><strong>������ ������ �
                    <?= intval($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]) ?>
                    ��
                    <?= htmlspecialcharsEx(
                        $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_INSERT_DATE"]
                    ) ?></strong></font></TD>
        <TD>
            <center>
                <font size="-1"><b> <font size="-1">
                            <?= SaleFormatCurrency(
                                $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"],
                                $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"]
                            ) ?>
                        </font></b> </font>
            </center>
        </TD>
    </TR>
    <!-- ���������� ������ (�����) -->
    <TR>
        <TD colSpan="2"><font style="font-size: 9pt">
                <br>� ��������� ������ ��������� � ��������� ��������� �����, � �.�. � ������ ��������� ����� �� ������
                �����, ���������� � ��������.
                <br><br>_______________ "____" __________ 201__ �.</font></TD>
    </TR>
</TABLE>

<!-- ������� �������� -->
<h1><br>
    <b>����� ������:</b></h1>
<ol>
    <li>������������ ���������. ���� � ��� ��� ��������, ���������� ������� ����� ��������� � ��������� �� ����� �������
        ����������� ����� ��������� � ����� �����.
    </li>
    <li>�������� �� ������� ���������.</li>
    <li>�������� ��������� � ����� ��������� �����, ������������ ������� �� ������� ���.</li>
    <li>��������� ��������� �� ������������� ���������� ������.</li>
</ol>
<h1><b>������� ��������:</b></h1>
<ul>
    <li>�������� ����������� ������ ������������ ����� ������������� ����� �������.</li>
    <li>������������� ������� ������������ �� ���������, ����������� � ��� ����.</li>
</ul>
<h1 align="justify"><b>����� ��������:</b></h1>
<p align="justify">� ������� 72 ����� ����� ����� ������������� �������.</p>
</body>
</html>