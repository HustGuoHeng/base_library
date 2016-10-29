<?php 
/**
 * curl 请求
 * [curlRequest description]
 * @param  [type]  $url     [description]
 * @param  array   $params  [description]
 * @param  boolean $isPost  [description]
 * @param  integer $timeOut [description]
 * @return [type]           [description]
 */
function curlRequest($url, $params = array(), $isPost = false, $timeOut = 60) {
    if ($isPost) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
        $result = curl_exec($ch);
        curl_close($ch);
    } else {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFERa, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        $result = curl_exec($ch);
        curl_close($ch);
    }
    return $result;
}