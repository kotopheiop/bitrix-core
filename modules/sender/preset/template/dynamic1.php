<table class="mail-grid" width="640" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td>

            <table class="mail-grid-cell" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                    <td data-bx-block-editor-place="body">
                        <!-- content -->
                        <div data-bx-block-editor-block-type="text">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="bxBlockText">
                                <tbody class="bxBlockOut">
                                <tr>
                                    <td valign="top" class="bxBlockInn bxBlockInnText">
                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tbody>
                                            <tr>
                                                <td valign="top" class="bxBlockPadding bxBlockContentText">
                                                    <h2>%HEADER%</h2><br>%TEXT1%
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div data-bx-block-editor-block-type="component">
                            <? EventMessageThemeCompiler::includeComponent(
                                "bitrix:sale.discount.coupon.mail",
                                "",
                                Array(),
                                false
                            ); ?>
                        </div>

                        <div data-bx-block-editor-block-type="button">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="bxBlockButton">
                                <tbody class="bxBlockOut">
                                <tr>
                                    <td valign="top" class="bxBlockPadding bxBlockInn bxBlockInnButton">
                                        <table align="center" border="0" cellpadding="0" cellspacing="0"
                                               class="bxBlockContentButtonEdge">
                                            <tbody>
                                            <tr>
                                                <td valign="top">
                                                    <a class="bxBlockContentButton" title="%BUTTON%" href="/"
                                                       target="_blank">
                                                        %BUTTON%
                                                    </a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- / content -->
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>