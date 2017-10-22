<?php

namespace common\components;

use Components\Utils\Http;
use yii\helpers\Json;
use yii\web\HttpException;

class Ip
{
    /**
     * getLocation desc
     *
     * @param $ip
     * @return bool|array
     * @throws HttpException
     */
    public static function getLocation($ip)
    {
        $url = "http://ip.service.2144.cn/lookup?ip={$ip}&key=CWh2gjvJwlYvf6lr";
        try {
            $result = Http::get($url);
            $res = Json::decode($result);
            if (isset($res['code']) && $res['code'] == 0) {
                return $res['data'];
            }
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(408, 'network 错误.');
        }

        return false;
    }
}
