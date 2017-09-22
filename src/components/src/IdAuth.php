<?php

namespace Components;

use Yii;
use yii\base\Component;

class IdAuth extends Component
{
    public $systemId;
    public $authKey;
    public $identityClass = \backend\models\Admin::class;
    public $duration = 86400;

    public function authorize()
    {
        return Yii::$app->response->redirect('http://id.2144.cn/adapt/'.$this->systemId);
    }

    public function login($id, $time, $remoteToken)
    {
        $localToken = md5($this->authKey . $time . $id);
        if ($localToken == $remoteToken) {
            $user = call_user_func([$this->identityClass, 'findIdentity'], $id);
            if ($user) {
                Yii::$app->user->login($user, $this->duration);
                return true;
            }
        }
        return false;
    }

    public function register($username, $nick, $time, $remoteToken)
    {
        $localToken = md5($this->authKey . $time . urlencode($username));
        $res = array('status' => 'failed', 'msg' => '签名无效');
        if ($localToken == $remoteToken) {
            $user = call_user_func([$this->identityClass, 'findByUsername'], $username);
            if (! $user) {
                $user = call_user_func_array([$this->identityClass, 'register'], [
                    $username,
                    substr($localToken, 0, 16),
                    $nick,
                ]);
            }

            if ($user && $user->id) {
                $res = array('status' => 'success', 'id' => $user->id);
            } else {
                $res = array('status' => 'failed', 'msg' => '注册失败');
            }
        }

        return $res;
    }
}
