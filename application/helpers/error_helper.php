<?php
if (!function_exists('error_json')) {
    /**
     * Met en forme le message d'erreur au format json
     * @param $xml
     * @return mixed
     */
    function error_json($code_error, $msg_error, $msg_error2 = "")
    {
        $json = [];
        $json['type'] = "error";
        $json['code'] = $code_error;
        $json['msg'] = $msg_error;
        if ($msg_error2 != "") {
            $json['msg2'] = $msg_error2;
        }

        return json_encode($json);
    }
}