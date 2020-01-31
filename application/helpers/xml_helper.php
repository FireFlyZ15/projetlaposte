<?php
if (!function_exists('xml_format')) {
    /**
     * Met en forme le XML pour qu'il soit plus lisible par un humain
     * @param $xml
     * @return mixed
     */
    function xml_format($xml)
    {
        $myxmlfilecontent = preg_replace('/>\s*</', '><', $xml);
        $myxmlfilecontent = str_replace('<', '^', $myxmlfilecontent);
        $result = "";
        $nb = strlen($myxmlfilecontent);
        $nbtab = -1;
        for ($i = 0; $i < $nb; $i++) {
            if ($myxmlfilecontent[$i] == '>') {
                if ($myxmlfilecontent[$i - 1] == '/') {
                    $nbtab--;
                }
                $tab = "";
                for ($j = 0; $j < $nbtab; $j++) {
                    //$tab.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    $tab .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                }
                $result .= $myxmlfilecontent[$i];

                if ($myxmlfilecontent[$i + 1] != '^') {
                    $result .= "<br>" . $tab;
                }

            } else if ($myxmlfilecontent[$i] == '^') {
                $tab = "";
                if ($myxmlfilecontent[$i + 1] == '/') {
                    $nbtab--;
                }
                for ($j = 0; $j < $nbtab; $j++) {
                    $tab .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                }
                $result .= "<br>" . $tab;
                $result .= $myxmlfilecontent[$i];
                if ($myxmlfilecontent[$i + 1] != '/') {
                    $nbtab++;
                }
            } else {
                $result .= $myxmlfilecontent[$i];
            }
        }
        return str_replace('^', '&lt;', $result);
    }
}