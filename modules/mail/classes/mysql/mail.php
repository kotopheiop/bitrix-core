<?php

/*
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002 - 2007 Bitrix           #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################
*/

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/mail/classes/general/mail.php");

class CMailbox extends CAllMailBox
{
    public static function CleanUp()
    {
        global $DB;
        $days = COption::GetOptionInt("mail", "time_keep_log", B_MAIL_KEEP_LOG);

        $strSql = "DELETE FROM b_mail_log WHERE DATE_INSERT < DATE_ADD(now(), INTERVAL -" . intval($days) . " DAY)";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        $mt = GetMicroTime();
        $dbr = $DB->Query(
            "SELECT MS.ID FROM b_mail_message MS, b_mail_mailbox MB WHERE MS.MAILBOX_ID=MB.ID AND MB.MAX_KEEP_DAYS>0 AND MS.DATE_INSERT < DATE_ADD(now(), INTERVAL -MB.MAX_KEEP_DAYS DAY)"
        );
        while ($ar = $dbr->Fetch()) {
            CMailMessage::Delete($ar["ID"]);
            if (GetMicroTime() - $mt > 10 * 1000) {
                break;
            }
        }

        return "CMailbox::CleanUp();";
    }
}

class CMailUtil extends CAllMailUtil
{
    public static function IsSizeAllowed($size)
    {
        global $B_MAIL_MAX_ALLOWED;

        $dbConnection = \Bitrix\Main\Application::getConnection();

        $B_MAIL_MAX_ALLOWED = $dbConnection->getMaxAllowedPacket();

        return $B_MAIL_MAX_ALLOWED > $size;
    }
}

class CMailMessage extends CAllMailMessage
{
}
