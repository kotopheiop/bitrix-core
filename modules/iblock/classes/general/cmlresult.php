<?php

class CCMLResult extends CDBResult
{
    function Fetch()
    {
        $r = parent::Fetch();
        if ($r && mb_strlen($r["ATTRIBUTES"])) {
            $a = unserialize($r["ATTRIBUTES"]);
            if (is_array($a)) {
                $r["ATTRIBUTES"] = $a;
            }
        }
        return $r;
    }
}