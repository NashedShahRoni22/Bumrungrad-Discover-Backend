<?php

if (!function_exists('res')) {
    function res($result, $msg)
    {
        $response = [
            'status' => 200,
            'data' => $result,
            'msg' => $msg
        ];
        return $response;
    }
}

if (!function_exists('err')) {
    function err($err, $msg = [], $code = 404)
    {
        $response = [
            'status' => $code,
            'msg' => $err
        ];
        if (!empty($msg)) {
            $response['err'] = $msg;
        }
        return $response;
    }
}
