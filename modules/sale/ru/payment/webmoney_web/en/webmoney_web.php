<?

global $MESS;

$MESS["SWMWP_DTITLE"] = "Payment via WebMoney (Web) (Russian payment system)";
$MESS["SWMWP_DDESCR"] = "Payment via WebMoney using <b>Web Merchant Interface</b> <a href=\"https://merchant.webmoney.ru/conf/guide.asp\" target=\"_blank\">https://merchant.webmoney.ru/conf/guide.asp</a>.<br>The sevice Web Merchant Interface should be customized to process payments in its settings. On the page <a href=\"https://merchant.webmoney.ru\" target=\"_blank\">https://merchant.webmoney.ru</a> select \"Settings\". Authorise and select wallet that will receive money via Web Merchant Interface.<br>\r\nset parameters:<UL><LI><b>Result URL</b> - to track payments automatically.</LI><LI><b>Success URL</b> - is the page to which the customer will be redirected  on success <nobr>(http://site/success.html)</nobr>. Specify appropriate page here.</LI><LI><b>Fail URL</b> - is the page to which the customer will be redirected  if the Web Merchant Interface failed to process payment. <nobr>(http://site/fail.html)</nobr>.</LI><LI>Signature algorithm - <b>MD5</b></LI><LI><b>Secret Key</b> - any set of symbols. specify if you want to track payments automatically.</LI></UL>";
?>