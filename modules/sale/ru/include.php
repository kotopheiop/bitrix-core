<?
function Number2Word_Rus($source, $IS_MONEY = "Y", $currency = "")
{
    $result = '';

    $IS_MONEY = ((string)($IS_MONEY) == 'Y' ? 'Y' : 'N');
    $currency = (string)$currency;
    if ($currency == '' || $currency == 'RUR')
        $currency = 'RUB';
    else if ($currency == 'BYR')
        $currency = 'BYN';
    if ($IS_MONEY == 'Y') {
        if ($currency != 'RUB' && $currency != 'UAH' && $currency != 'KZT' && $currency != 'BYN')
            return $result;
    }

    $arNumericLang = array(
        "RUB" => array(
            "zero" => "����",
            "1c" => "��� ",
            "2c" => "������ ",
            "3c" => "������ ",
            "4c" => "��������� ",
            "5c" => "������� ",
            "6c" => "�������� ",
            "7c" => "������� ",
            "8c" => "��������� ",
            "9c" => "��������� ",
            "1d0e" => "������ ",
            "1d1e" => "����������� ",
            "1d2e" => "���������� ",
            "1d3e" => "���������� ",
            "1d4e" => "������������ ",
            "1d5e" => "���������� ",
            "1d6e" => "����������� ",
            "1d7e" => "���������� ",
            "1d8e" => "������������ ",
            "1d9e" => "������������ ",
            "2d" => "�������� ",
            "3d" => "�������� ",
            "4d" => "����� ",
            "5d" => "��������� ",
            "6d" => "���������� ",
            "7d" => "��������� ",
            "8d" => "����������� ",
            "9d" => "��������� ",
            "5e" => "���� ",
            "6e" => "����� ",
            "7e" => "���� ",
            "8e" => "������ ",
            "9e" => "������ ",
            "1et" => "���� ������ ",
            "2et" => "��� ������ ",
            "3et" => "��� ������ ",
            "4et" => "������ ������ ",
            "1em" => "���� ������� ",
            "2em" => "��� �������� ",
            "3em" => "��� �������� ",
            "4em" => "������ �������� ",
            "1eb" => "���� �������� ",
            "2eb" => "��� ��������� ",
            "3eb" => "��� ��������� ",
            "4eb" => "������ ��������� ",
            "1e." => "���� ����� ",
            "2e." => "��� ����� ",
            "3e." => "��� ����� ",
            "4e." => "������ ����� ",
            "1e" => "���� ",
            "2e" => "��� ",
            "3e" => "��� ",
            "4e" => "������ ",
            "11k" => "11 ������",
            "12k" => "12 ������",
            "13k" => "13 ������",
            "14k" => "14 ������",
            "1k" => "1 �������",
            "2k" => "2 �������",
            "3k" => "3 �������",
            "4k" => "4 �������",
            "." => "������ ",
            "t" => "����� ",
            "m" => "��������� ",
            "b" => "���������� ",
            "k" => " ������",
        ),
        "BYN" => array(
            "zero" => "����",
            "1c" => "��� ",
            "2c" => "������ ",
            "3c" => "������ ",
            "4c" => "��������� ",
            "5c" => "������� ",
            "6c" => "�������� ",
            "7c" => "������� ",
            "8c" => "��������� ",
            "9c" => "��������� ",
            "1d0e" => "������ ",
            "1d1e" => "����������� ",
            "1d2e" => "���������� ",
            "1d3e" => "���������� ",
            "1d4e" => "������������ ",
            "1d5e" => "���������� ",
            "1d6e" => "����������� ",
            "1d7e" => "���������� ",
            "1d8e" => "������������ ",
            "1d9e" => "������������ ",
            "2d" => "�������� ",
            "3d" => "�������� ",
            "4d" => "����� ",
            "5d" => "��������� ",
            "6d" => "���������� ",
            "7d" => "��������� ",
            "8d" => "����������� ",
            "9d" => "��������� ",
            "5e" => "���� ",
            "6e" => "����� ",
            "7e" => "���� ",
            "8e" => "������ ",
            "9e" => "������ ",
            "1et" => "���� ������ ",
            "2et" => "��� ������ ",
            "3et" => "��� ������ ",
            "4et" => "������ ������ ",
            "1em" => "���� ������� ",
            "2em" => "��� �������� ",
            "3em" => "��� �������� ",
            "4em" => "������ �������� ",
            "1eb" => "���� �������� ",
            "2eb" => "��� ��������� ",
            "3eb" => "��� ��������� ",
            "4eb" => "������ ��������� ",
            "1e." => "���� ����������� ����� ",
            "2e." => "��� ����������� ����� ",
            "3e." => "��� ����������� ����� ",
            "4e." => "������ ����������� ����� ",
            "1e" => "���� ",
            "2e" => "��� ",
            "3e" => "��� ",
            "4e" => "������ ",
            "11k" => "11 ������",
            "12k" => "12 ������",
            "13k" => "13 ������",
            "14k" => "14 ������",
            "1k" => "1 �������",
            "2k" => "2 �������",
            "3k" => "3 �������",
            "4k" => "4 �������",
            "." => "����������� ������ ",
            "t" => "����� ",
            "m" => "��������� ",
            "b" => "���������� ",
            "k" => " ������",
        ),
        "UAH" => array(
            "zero" => "�y��",
            "1c" => "��� ",
            "2c" => "���� ",
            "3c" => "������ ",
            "4c" => "��������� ",
            "5c" => "�'����� ",
            "6c" => "������� ",
            "7c" => "����� ",
            "8c" => "������ ",
            "9c" => "���'����� ",
            "1d0e" => "������ ",
            "1d1e" => "���������� ",
            "1d2e" => "���������� ",
            "1d3e" => "���������� ",
            "1d4e" => "������������ ",
            "1d5e" => "�'��������� ",
            "1d6e" => "����������� ",
            "1d7e" => "��������� ",
            "1d8e" => "���������� ",
            "1d9e" => "���'��������� ",
            "2d" => "�������� ",
            "3d" => "�������� ",
            "4d" => "����� ",
            "5d" => "�'������� ",
            "6d" => "��������� ",
            "7d" => "������� ",
            "8d" => "�������� ",
            "9d" => "���'������ ",
            "5e" => "�'��� ",
            "6e" => "����� ",
            "7e" => "�� ",
            "8e" => "��� ",
            "9e" => "���'��� ",
            "1e." => "���� ������ ",
            "2e." => "��� ����� ",
            "3e." => "��� ����� ",
            "4e." => "������ ����� ",
            "1e" => "���� ",
            "2e" => "��� ",
            "3e" => "��� ",
            "4e" => "������ ",
            "1et" => "���� ������ ",
            "2et" => "�� ������ ",
            "3et" => "��� ������ ",
            "4et" => "������ ������ ",
            "1em" => "���� ������ ",
            "2em" => "��� ������� ",
            "3em" => "��� ������� ",
            "4em" => "������ ������� ",
            "1eb" => "���� ������ ",
            "2eb" => "��� ������� ",
            "3eb" => "��� ������� ",
            "4eb" => "������ ������� ",
            "11k" => "11 ������",
            "12k" => "12 ������",
            "13k" => "13 ������",
            "14k" => "14 ������",
            "1k" => "1 ������",
            "2k" => "2 ������",
            "3k" => "3 ������",
            "4k" => "4 ������",
            "." => "������� ",
            "t" => "����� ",
            "m" => "������� ",
            "b" => "������� ",
            "k" => " ������",
        ),
        "KZT" => array(
            "zero" => "����",
            "1c" => "��� ",
            "2c" => "������ ",
            "3c" => "������ ",
            "4c" => "��������� ",
            "5c" => "������� ",
            "6c" => "�������� ",
            "7c" => "������� ",
            "8c" => "��������� ",
            "9c" => "��������� ",
            "1d0e" => "������ ",
            "1d1e" => "����������� ",
            "1d2e" => "���������� ",
            "1d3e" => "���������� ",
            "1d4e" => "������������ ",
            "1d5e" => "���������� ",
            "1d6e" => "����������� ",
            "1d7e" => "���������� ",
            "1d8e" => "������������ ",
            "1d9e" => "������������ ",
            "2d" => "�������� ",
            "3d" => "�������� ",
            "4d" => "����� ",
            "5d" => "��������� ",
            "6d" => "���������� ",
            "7d" => "��������� ",
            "8d" => "����������� ",
            "9d" => "��������� ",
            "5e" => "���� ",
            "6e" => "����� ",
            "7e" => "���� ",
            "8e" => "������ ",
            "9e" => "������ ",
            "1et" => "���� ������ ",
            "2et" => "��� ������ ",
            "3et" => "��� ������ ",
            "4et" => "������ ������ ",
            "1em" => "���� ������� ",
            "2em" => "��� �������� ",
            "3em" => "��� �������� ",
            "4em" => "������ �������� ",
            "1eb" => "���� �������� ",
            "2eb" => "��� ��������� ",
            "3eb" => "��� ��������� ",
            "4eb" => "������ ��������� ",
            "1e." => "���� ����� ",
            "2e." => "��� ����� ",
            "3e." => "��� ����� ",
            "4e." => "������ ����� ",
            "1e" => "���� ",
            "2e" => "��� ",
            "3e" => "��� ",
            "4e" => "������ ",
            "11k" => "11 ����",
            "12k" => "12 ����",
            "13k" => "13 ����",
            "14k" => "14 ����",
            "1k" => "1 ����",
            "2k" => "2 ����",
            "3k" => "3 ����",
            "4k" => "4 ����",
            "." => "����� ",
            "t" => "����� ",
            "m" => "��������� ",
            "b" => "���������� ",
            "k" => " ����",
        )
    );

    // k - penny
    if ($IS_MONEY == "Y") {
        $source = (string)((float)$source);
        $dotpos = strpos($source, ".");
        if ($dotpos === false) {
            $ipart = $source;
            $fpart = '';
        } else {
            $ipart = substr($source, 0, $dotpos);
            $fpart = substr($source, $dotpos + 1);
            if ($fpart === false)
                $fpart = '';
        };
        if (strlen($fpart) > 2) {
            $fpart = substr($fpart, 0, 2);
            if ($fpart === false)
                $fpart = '';
        }
        $fillLen = 2 - strlen($fpart);
        if ($fillLen > 0)
            $fpart .= str_repeat('0', $fillLen);
        unset($fillLen);
    } else {
        $ipart = (string)((int)$source);
        $fpart = '';
    }

    if (is_string($ipart)) {
        $ipart = preg_replace('/^[0]+/', '', $ipart);
    }

    $ipart1 = strrev($ipart);
    $ipart1Len = strlen($ipart1);
    $ipart = "";
    $i = 0;
    while ($i < $ipart1Len) {
        $ipart_tmp = substr($ipart1, $i, 1);
        // t - thousands; m - millions; b - billions;
        // e - units; d - scores; c - hundreds;
        if ($i % 3 == 0) {
            if ($i == 0) $ipart_tmp .= "e";
            elseif ($i == 3) $ipart_tmp .= "et";
            elseif ($i == 6) $ipart_tmp .= "em";
            elseif ($i == 9) $ipart_tmp .= "eb";
            else $ipart_tmp .= "x";
        } elseif ($i % 3 == 1) $ipart_tmp .= "d";
        elseif ($i % 3 == 2) $ipart_tmp .= "c";
        $ipart = $ipart_tmp . $ipart;
        $i++;
    }

    if ($IS_MONEY == "Y") {
        $result = $ipart . "." . $fpart . "k";
    } else {
        $result = $ipart;
        if ($result == '')
            $result = $arNumericLang[$currency]['zero'];
    }

    if (substr($result, 0, 1) == ".")
        $result = $arNumericLang[$currency]['zero'] . " " . $result;

    $result = str_replace("0c0d0et", "", $result);
    $result = str_replace("0c0d0em", "", $result);
    $result = str_replace("0c0d0eb", "", $result);

    $result = str_replace("0c", "", $result);
    $result = str_replace("1c", $arNumericLang[$currency]["1c"], $result);
    $result = str_replace("2c", $arNumericLang[$currency]["2c"], $result);
    $result = str_replace("3c", $arNumericLang[$currency]["3c"], $result);
    $result = str_replace("4c", $arNumericLang[$currency]["4c"], $result);
    $result = str_replace("5c", $arNumericLang[$currency]["5c"], $result);
    $result = str_replace("6c", $arNumericLang[$currency]["6c"], $result);
    $result = str_replace("7c", $arNumericLang[$currency]["7c"], $result);
    $result = str_replace("8c", $arNumericLang[$currency]["8c"], $result);
    $result = str_replace("9c", $arNumericLang[$currency]["9c"], $result);

    $result = str_replace("1d0e", $arNumericLang[$currency]["1d0e"], $result);
    $result = str_replace("1d1e", $arNumericLang[$currency]["1d1e"], $result);
    $result = str_replace("1d2e", $arNumericLang[$currency]["1d2e"], $result);
    $result = str_replace("1d3e", $arNumericLang[$currency]["1d3e"], $result);
    $result = str_replace("1d4e", $arNumericLang[$currency]["1d4e"], $result);
    $result = str_replace("1d5e", $arNumericLang[$currency]["1d5e"], $result);
    $result = str_replace("1d6e", $arNumericLang[$currency]["1d6e"], $result);
    $result = str_replace("1d7e", $arNumericLang[$currency]["1d7e"], $result);
    $result = str_replace("1d8e", $arNumericLang[$currency]["1d8e"], $result);
    $result = str_replace("1d9e", $arNumericLang[$currency]["1d9e"], $result);

    $result = str_replace("0d", "", $result);
    $result = str_replace("2d", $arNumericLang[$currency]["2d"], $result);
    $result = str_replace("3d", $arNumericLang[$currency]["3d"], $result);
    $result = str_replace("4d", $arNumericLang[$currency]["4d"], $result);
    $result = str_replace("5d", $arNumericLang[$currency]["5d"], $result);
    $result = str_replace("6d", $arNumericLang[$currency]["6d"], $result);
    $result = str_replace("7d", $arNumericLang[$currency]["7d"], $result);
    $result = str_replace("8d", $arNumericLang[$currency]["8d"], $result);
    $result = str_replace("9d", $arNumericLang[$currency]["9d"], $result);

    $result = str_replace("0e", "", $result);
    $result = str_replace("5e", $arNumericLang[$currency]["5e"], $result);
    $result = str_replace("6e", $arNumericLang[$currency]["6e"], $result);
    $result = str_replace("7e", $arNumericLang[$currency]["7e"], $result);
    $result = str_replace("8e", $arNumericLang[$currency]["8e"], $result);
    $result = str_replace("9e", $arNumericLang[$currency]["9e"], $result);

    $result = str_replace("1et", $arNumericLang[$currency]["1et"], $result);
    $result = str_replace("2et", $arNumericLang[$currency]["2et"], $result);
    $result = str_replace("3et", $arNumericLang[$currency]["3et"], $result);
    $result = str_replace("4et", $arNumericLang[$currency]["4et"], $result);
    $result = str_replace("1em", $arNumericLang[$currency]["1em"], $result);
    $result = str_replace("2em", $arNumericLang[$currency]["2em"], $result);
    $result = str_replace("3em", $arNumericLang[$currency]["3em"], $result);
    $result = str_replace("4em", $arNumericLang[$currency]["4em"], $result);
    $result = str_replace("1eb", $arNumericLang[$currency]["1eb"], $result);
    $result = str_replace("2eb", $arNumericLang[$currency]["2eb"], $result);
    $result = str_replace("3eb", $arNumericLang[$currency]["3eb"], $result);
    $result = str_replace("4eb", $arNumericLang[$currency]["4eb"], $result);

    if ($IS_MONEY == "Y") {
        $result = str_replace("1e.", $arNumericLang[$currency]["1e."], $result);
        $result = str_replace("2e.", $arNumericLang[$currency]["2e."], $result);
        $result = str_replace("3e.", $arNumericLang[$currency]["3e."], $result);
        $result = str_replace("4e.", $arNumericLang[$currency]["4e."], $result);
    } else {
        $result = str_replace("1e", $arNumericLang[$currency]["1e"], $result);
        $result = str_replace("2e", $arNumericLang[$currency]["2e"], $result);
        $result = str_replace("3e", $arNumericLang[$currency]["3e"], $result);
        $result = str_replace("4e", $arNumericLang[$currency]["4e"], $result);
    }

    if ($IS_MONEY == "Y") {
        $result = str_replace("11k", $arNumericLang[$currency]["11k"], $result);
        $result = str_replace("12k", $arNumericLang[$currency]["12k"], $result);
        $result = str_replace("13k", $arNumericLang[$currency]["13k"], $result);
        $result = str_replace("14k", $arNumericLang[$currency]["14k"], $result);
        $result = str_replace("1k", $arNumericLang[$currency]["1k"], $result);
        $result = str_replace("2k", $arNumericLang[$currency]["2k"], $result);
        $result = str_replace("3k", $arNumericLang[$currency]["3k"], $result);
        $result = str_replace("4k", $arNumericLang[$currency]["4k"], $result);
    }

    if ($IS_MONEY == "Y") {
        if (substr($result, 0, 1) == ".")
            $result = $arNumericLang[$currency]['zero'] . " " . $result;

        $result = str_replace(".", $arNumericLang[$currency]["."], $result);
    }

    $result = str_replace("t", $arNumericLang[$currency]["t"], $result);
    $result = str_replace("m", $arNumericLang[$currency]["m"], $result);
    $result = str_replace("b", $arNumericLang[$currency]["b"], $result);

    if ($IS_MONEY == "Y")
        $result = str_replace("k", $arNumericLang[$currency]["k"], $result);

    return (ToUpper(substr($result, 0, 1)) . substr($result, 1));
}