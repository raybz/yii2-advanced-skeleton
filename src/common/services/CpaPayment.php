<?php
namespace common\services;

use Yii;
use common\models\db\Payment;
use common\models\mapi\Game;
use yii\helpers\Json;
use yii\httpclient\Client;

class CpaPayment
{
    const API_KEY = '39JPD!VDX#lTQY2VD';
    const API_URL = "http://api.mobile.2144.cn/vendor/payment-info";

    /**
     * 生成签名
     */
    public static function generateParam($param)
    {
        $params = [
            'gkey' => $param['slug'],
            'app_id' => $param['game_id'],
            'user_id' => $param['user_id'],
            'ad_id' => $param['pos_id'],
            'source' => $param['source'],
            'sn' => $param['sn'],
            'ip' => $param['ip'],
            'payment_way' => $param['paytype'],
            'balance' => $param['money'],
            'time' => $param['finished_at'],
            'register_time' => $param['register_time'],
            'device_id' => '',
        ];
        $sign = md5(implode('', $params) . self::API_KEY);
        $params['sign'] = $sign;
        return $params;
    }

    /**
     *  push mq
     */
    public static function pushMq($sn)
    {
        $client = new Client();
        $data = self::resolveParam($sn);
        if ($data) {
            $data = self::generateParam($data);
            $response = $client->get(self::API_URL, $data)->send();
            if ($response->isOk) {
                $responseData = Json::decode($response->getContent());
                if (isset($responseData['success']) && $responseData['success'] == 'true') {
                    self::log($data['sn'], $data['app_id'], 'ok', $response->getContent());
                } else {
                    self::log($data['sn'], $data['app_id'], 'fail', $response->getContent());
                }
                echo "Success\n";
            } else {
                self::log($data['sn'], $data['app_id'], 'fail2', $response->getStatusCode());
                echo "Fail2\n";
            }
        } else {
            self::log(false, false, 'fail3', false);
            echo "Fail\n";
        }

    }

    /**
     * @param $sn
     * @return array
     */
    public static function resolveParam($sn)
    {
        $payment = Payment::find()->where('sn = :sn', [':sn' => $sn])->one();
        $data = [];
        if ($payment) {
            $data = [
                'slug' => Game::getGameInfoById($payment['game_id'], 'slug'),
                'game_id' => $payment['game_id'],
                'user_id' => $payment['user_id'],
                'pos_id' => $payment['pos_id'],
                'source' => $payment['source'],
                'sn' => $payment['sn'],
                'ip' => $payment['created_ip'],
                'paytype' => $payment['paytype'],
                'money' => $payment['money'],
                'finished_at' => $payment['finished_at'],
                'register_time' => $payment['game_register_at'],
            ];
        }
        return $data;

    }

    /**
     * @param $sn
     * @param $data
     * @param int $http_code
     */
    protected static function log($sn, $game_id, $status, $data, $http_code = 200)
    {
        $date = date('Y-m-d H:i:s');
        $logs = "[{$status}]:[{$date}]:sn={$sn}&game_id={$game_id}&responseCode={$http_code}&responseData={$data}\n";
        $logFile = Yii::$app->getBasePath() . '/logs/payment/push_'.date('Ymd').'.log';
        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0775, true);
        }
        file_put_contents($logFile, $logs, FILE_APPEND);
    }


}