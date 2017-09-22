<?php

namespace Components;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class Audit extends Component
{
    public $systemId;
    public $authKey;
    public $usernameAttribute = 'username';
    public $api = 'http://audit.2144.cn/api/report';

    private $uid;
    private $username;
    private $action;
    private $params;

    public function init()
    {
        parent::init();

        $this->uid = Yii::$app->user->id;
        $this->username = $this->uid ? Yii::$app->user->identity->{$this->usernameAttribute} : '';
        $this->action = Yii::$app->controller->action->uniqueId;
        $this->params = Json::encode(ArrayHelper::merge($_GET, $_POST));
    }

    protected function generateSign()
    {
        return md5(
            $this->uid.
            $this->username.
            $this->systemId.
            $this->action.
            $this->params.
            $this->authKey
        );
    }

    public function report()
    {
        if ($this->uid) {
            $params = array(
                'uid' => $this->uid,
                'username' => $this->username,
                'sid' => $this->systemId,
                'action' => $this->action,
                'params' => $this->params,
                'sign' => $this->generateSign(),
            );
            $this->asyncRequest($this->api, $params);
        }
    }

    protected function asyncRequest($url, $params = array(), $type = 'GET')
    {
        $query = http_build_query($params);

        $parts = parse_url($url);

        $fp = fsockopen(
            $parts['host'],
            isset($parts['port']) ? $parts['port'] : 80,
            $errno,
            $errstr,
            30
        );

        $location = $parts['path'].(empty($parts['query']) ? '' : "?{$parts['query']}");
        $location .= ($type == 'GET' && !empty($query)) ? (empty($parts['query']) ? '?' : '&').$query : '';

        $out = "{$type} {$location} HTTP/1.1\r\n";
        $out .= "Host: {$parts['host']}\r\n";
        $out .= "Connection: Close\r\n\r\n";
        $out .= $type == 'POST' ? "Content-Type: application/x-www-form-urlencoded\r\n" : '';
        $out .= 'Content-Length: '.strlen($query)."\r\n";
        $out .= ($type == 'POST' && isset($query)) ? $query : '';

        fwrite($fp, $out);
        fclose($fp);
    }
}
