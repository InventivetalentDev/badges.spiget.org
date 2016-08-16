<?php
function getJson($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "SpigetBadges/1.0");

    $result = curl_exec($ch);
    if (($code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) !== 200) {
        curl_close($ch);
        return $code;
    }
    curl_close($ch);
    return json_decode($result, true);
}