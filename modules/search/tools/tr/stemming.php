<?php
// http://www.i18nguy.com/unicode/turkish-i18n.html

function stemming_letter_tr()
{
    return "abc�defg�h�ijklmno�prs�tu�vyz" . "ABC�DEFG�HIIJKLMNO�PRS�TU�VYZ" . "����iI�";
}

function stemming_upper_tr($sText)
{
    return str_replace(array("�"), array("I"), ToUpper($sText));
}
