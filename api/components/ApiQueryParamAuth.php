<?php
namespace api\components;

use Yii;
use api\modules\v1\models\Auth;
use yii\base\InvalidParamException;
use yii\filters\auth\AuthMethod;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class ApiQueryParamAuth extends AuthMethod
{
    protected $appInfo;

    public function authenticate($user, $request, $response)
    {
        /**
         * 如果在测试环境, 可以使用_skip参数跳过验证
         */
        $_skip = $request->get('_skip', false);
        if ($_skip && YII_DEBUG) {
            $this->appInfo = Auth::findIdentityByAccessToken('zvZyiSRqF6V_5gb5');
        } else {
            $this->checkParams($request);
            $this->checkTimeout($request);
            $this->checkApp($request);
            $this->checkToken($request);
        }
        return $this->appInfo;
    }

    protected function checkApp($request)
    {
        $appkey = $request->get('appkey');
        $appInfo = $this->getAppInfo($appkey);
        if (!$appInfo) {
            throw new UnauthorizedHttpException('当前appkey未得到授权');
        }
        return true;
    }


    protected function getAppInfo($appkey)
    {
        if ($this->appInfo == null) {
            $this->appInfo = Auth::findIdentityByAccessToken($appkey);
        }
        return $this->appInfo;
    }

    protected function generateToken($request, $appsecret)
    {
        $params = $request->getQueryParams();
        ksort($params, SORT_REGULAR);
        $str = '';
        if (isset($params['access_token'])) {
            unset($params['access_token']);
        }
        foreach ($params as $key => $value) {
            $str .= "{$key}={$value}&";
        }
        $str = rtrim($str, '&') . '&appsecret=' . $appsecret;
        return sha1($str);
    }

    protected function checkToken($request)
    {
        $access_token = $request->get('access_token');
        $appkey = $request->get('appkey');
        if (!$access_token) {
            throw new UnauthorizedHttpException('缺少access_token参数');
        }
        $appInfo = $this->getAppInfo($appkey);
        $generateToken = $this->generateToken($request, $appInfo->appsecret);
        if ($access_token !== $generateToken) {
            throw new UnauthorizedHttpException("签名验证失败");
        }
        return true;
    }

    protected function checkTimeout($request)
    {
        $request_time = $request->get('request_time', '');
        $time = time();
        if ($time - $request_time >= 300) {
            throw new UnauthorizedHttpException('服务器请求超时');
        }
        return true;
    }

    protected function checkParams($request)
    {
        $mustParams = ['request_time', 'access_token', 'appkey'];
        foreach ($mustParams as $param) {
            if ($request->get($param) === null) {
                throw new UnauthorizedHttpException("请授权后访问该接口");
            }
        }
    }


}