<?

header(
    "P3P: policyref=\"/bitrix/p3p.xml\", CP=\"NON DSP COR CUR ADM DEV PSA PSD OUR UNR BUS UNI COM NAV INT DEM STA\""
);
header("Content-type: image/png");
if (isset($_GET["k"]) && isset($_GET["s"]) && is_string($_GET["k"]) && is_string($_GET["s"]) && $_GET["k"] <> '') {
    $LICENSE_KEY = "";
    @include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/license_key.php");
    if ($LICENSE_KEY == "" || mb_strtoupper($LICENSE_KEY) == "DEMO") {
        $LICENSE_KEY = "DEMO";
    }

    $cookie = base64_decode($_GET["s"]);
    $salt = $_SERVER["REMOTE_ADDR"] . "|" . @filemtime(
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/version.php"
        ) . "|" . $LICENSE_KEY;
    if (md5($cookie . $salt) === $_GET["k"]) {
        $arr = explode(chr(2), $cookie);
        if (is_array($arr) && count($arr) > 0) {
            foreach ($arr as $str) {
                if ($str <> '') {
                    $host = $_SERVER["HTTP_HOST"];
                    if (($pos = strpos($host, ":")) !== false) {
                        $host = substr($host, 0, $pos);
                    }

                    $ar = explode(chr(1), $str);
                    setcookie($ar[0], $ar[1], $ar[2], $ar[3], $host, $ar[5], $ar[6]);

                    //logout
                    if (substr($ar[0], -5) == '_UIDH' && $ar[1] == '') {
                        $kernelSession = \Bitrix\Main\Application::getInstance()->getKernelSession();
                        $kernelSession->start();
                        $kernelSession["SESS_AUTH"] = Array();
                        unset($kernelSession["SESS_AUTH"]);
                        unset($kernelSession["SESS_OPERATIONS"]);
                        unset($kernelSession["MODULE_PERMISSIONS"]);
                        unset($kernelSession["SESS_PWD_HASH_TESTED"]);
                    }
                }
            }
        }
    }
}