<table border="1">
    <tr>
        <td valign="top">ID</td>
        <td valign="top"><?= GetMessage("FORM_TIMESTAMP") ?></td>
        <td valign="top"><?= GetMessage("FORM_STATUS") ?></td>
        <? if ($F_RIGHT >= 25): ?>
            <td valign="top"><? echo GetMessage("FORM_USER") ?></td>
            <? if (CModule::IncludeModule("statistic")): ?>
                <td valign="top"><? echo GetMessage("FORM_GUEST_ID") ?></td>
                <td valign="top"><? echo GetMessage("FORM_SESSION_ID") ?></td>
            <? endif; ?>
        <? endif; ?>
        <?
        CForm::GetResultAnswerArray(
            $WEB_FORM_ID,
            $arrColumns,
            $arrAnswers,
            $arrAnswersSID,
            array("IN_EXCEL_TABLE" => "Y")
        );
        $colspan = 5;
        foreach ($arrColumns as $key => $arrCol) :

            if (!is_array($arrNOT_SHOW_TABLE) || !in_array($arrCol["SID"], $arrNOT_SHOW_TABLE)):

                if (($arrCol["ADDITIONAL"] == "Y" && $SHOW_ADDITIONAL == "Y") || $arrCol["ADDITIONAL"] != "Y") :
                    $colspan++;
                    if ($arrCol["RESULTS_TABLE_TITLE"] == '') {
                        $title = ($arrCol["TITLE_TYPE"] == "html") ? strip_tags($arrCol["TITLE"]) : htmlspecialcharsbx(
                            $arrCol["TITLE"]
                        );
                    } else {
                        $title = htmlspecialcharsbx($arrCol["RESULTS_TABLE_TITLE"]);
                    }
                    ?>
                    <td valign="top"><?
                    if ($F_RIGHT >= 25) :
                        ?>[<?= $arrCol["ID"] ?>]<br><?
                    endif;
                    echo $title;
                    ?></td><?

                endif;

            endif;

        endforeach;
        ?>
    </tr>
    <?
    $j = 0;
    while ($result->NavNext(true, "f_")) :
        $j++;
        $arrRESULT_PERMISSION = CFormResult::GetPermissions($f_ID);
        ?>
        <tr valign="top">
            <td class="number0" nowrap><?= $f_ID ?></td>
            <td align="center"><?= $f_TIMESTAMP_X ?></td>
            <td><? echo "[" . $f_STATUS_ID . "] " . $f_STATUS_TITLE ?></td>
            <? if ($F_RIGHT >= 25):?>
                <td><?
                    if ($f_USER_ID > 0) :
                        $rsUser = CUser::GetByID($f_USER_ID);
                        ClearVars("u_");
                        $rsUser->ExtractFields("u_");
                        $f_LOGIN = $u_LOGIN;
                        $f_USER_NAME = $u_NAME . " " . $u_LAST_NAME;
                        echo "[$f_USER_ID] ($f_LOGIN) $f_USER_NAME";
                        echo ($f_USER_AUTH == "N") ? GetMessage("FORM_NOT_AUTH") : "";
                    else :
                        echo GetMessage("FORM_NOT_REGISTERED");
                    endif;
                    ?></td>
                <? if (CModule::IncludeModule("statistic")):?>
                    <td class="number0"><? echo $f_STAT_GUEST_ID ?></td>
                    <td class="number0"><? echo $f_STAT_SESSION_ID ?></td>
                <?endif; ?>
            <?endif; ?>
            <?
            foreach ($arrColumns as $FIELD_ID => $arrC):

                if (!is_array($arrNOT_SHOW_TABLE) || !in_array($arrC["SID"], $arrNOT_SHOW_TABLE)):

                    if (($arrC["ADDITIONAL"] == "Y" && $SHOW_ADDITIONAL == "Y") || $arrC["ADDITIONAL"] != "Y") :
                        ?>
                        <td valign="top" align="left" nowrap><?
                            $arrAnswer = $arrAnswers[$f_ID][$FIELD_ID];
                            if (!is_array($arrAnswer)) {
                                $arrAnswer = array();
                            }
                            $count = count($arrAnswer);
                            $i = 0;
                            foreach ($arrAnswer as $key => $arrA):
                                $i++;
                                if (trim($arrA["USER_TEXT"]) <> '') {
                                    if (intval($arrA["USER_FILE_ID"]) <= 0) {
                                        echo htmlspecialcharsbx($arrA["USER_TEXT"]) . "<br>";
                                    }
                                }

                                if (trim($arrA["ANSWER_TEXT"]) <> '') {
                                    $answer = "[" . htmlspecialcharsbx($arrA["ANSWER_TEXT"]) . "]";
                                    if (trim($arrA["ANSWER_VALUE"]) <> '' && $SHOW_ANSWER_VALUE == "Y") {
                                        $answer .= "&nbsp;";
                                    } else {
                                        $answer .= "<br>";
                                    }
                                    echo $answer;
                                }
                                if (trim($arrA["ANSWER_VALUE"]) <> '' && $SHOW_ANSWER_VALUE == "Y") {
                                    echo "(" . htmlspecialcharsbx($arrA["ANSWER_VALUE"]) . ")<br>";
                                }

                                if (intval($arrA["USER_FILE_ID"]) > 0) {
                                    $rsFile = CFile::GetByID($arrA["USER_FILE_ID"]);
                                    $arFile = $rsFile->Fetch();

                                    echo GetMessage("FORM_FILE_NAME") . $arFile["FILE_NAME"] . "<br>";

                                    if (intval($arFile["HEIGHT"]) > 0) {
                                        echo GetMessage("FORM_HEIGHT") . $arFile["HEIGHT"] . "<br>";
                                    }

                                    if (intval($arFile["WIDTH"]) > 0) {
                                        echo GetMessage("FORM_WIDTH") . $arFile["WIDTH"] . "<br>";
                                    }

                                    echo GetMessage("FORM_SIZE") . $arFile["FILE_SIZE"] . "<br>";
                                }
                            endforeach;
                            ?></td>
                    <?
                    endif;
                endif;
            endforeach;
            ?>
        </tr>
    <?
    endwhile;
    ?>
    <tr valign="top">
        <td align="left" colspan="<?= $colspan ?>"><?= GetMessage("FORM_TOTAL") ?>
            &nbsp;<? echo $result->SelectedRowsCount() ?></td>
    </tr>
</table>