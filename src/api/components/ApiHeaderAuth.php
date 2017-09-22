<?php
namespace api\components;

use api\modules\v1\models\Auth;
use yii\filters\auth\AuthMethod;
use yii\web\UnauthorizedHttpException;

class ApiHeaderAuth extends AuthMethod
{
    // API请求参数: 时间戳
    const API_REQUEST_TIME = 'X-Api-Request-Timestamp';
    // API请求参数: key
    const API_REQUEST_APPKEY = 'X-Api-Request-Appkey';
    // API请求参数: token
    const API_REQUEST_TOKEN = 'X-Api-Request-Token';

    const REQUEST_TIMEOUT = 60;

    private $_headerCollection = [];

    private $headers = [];

    private $app = null;

    public function init()
    {
        parent::init();
        $this->_headerCollection = [
            self::API_REQUEST_TIME,
            self::API_REQUEST_APPKEY,
            self::API_REQUEST_TOKEN
        ];
    }

    public function authenticate($user, $request, $response)
    {
        /**
         * 如果在测试环境, 可以使用_skip参数跳过验证
         */
        $_skip = $request->get('_skip', false);
        if ($_skip && YII_DEBUG) {
            $this->app = Auth::findIdentityByAccessToken('zvZyiSRqF6V_5gb5');
        } else {
            $this->headers = $request->getHeaders();
            $this->checkAuthHeader();
            $this->checkTime();
            $this->checkAppkey();
            $this->checkToken();
        }
        return $this->app;
    }

    /**
     * 验证头信息验证参数是否完整
     */
    public function checkAuthHeader()
    {
        foreach ($this->_headerCollection as $item) {
            if (! $this->headers->has($item) )  {
                throw new UnauthorizedHttpException("当前接口需要授权");
            }
        }
    }

    /**
     * 验证appkey正确性
     */
    public function checkAppkey()
    {
        $appkey = $this->headers->get(self::API_REQUEST_APPKEY);
        $appInfo = $this->getAppInfo($appkey);
        if (!$appInfo) {
            throw new UnauthorizedHttpException('当前appkey未得到授权');
        }
        return true;
    }


    public function getAppInfo($appkey)
    {
        $this->app = Auth::findIdentityByAccessToken($appkey);
        return $this->app;
    }

    /**
     * 验证请求时间戳是否超时
     */
    public function checkTime()
    {
        $headerTime = $this->headers->get(self::API_REQUEST_TIME);
        $time = time();
        if (($time - $headerTime) >= self::REQUEST_TIMEOUT) {
            throw new UnauthorizedHttpException("接口请求超时");
        }

    }

    /**
     * 验证请求token是否有效
     */
    public function checkToken()
    {
        $token = $this->headers->get(self::API_REQUEST_TOKEN);
        $generateToken = $this->generateToken();
        if ( $generateToken !== $token ) {
            throw new UnauthorizedHttpException("接口授权认证失败, 无法获取数据");
        }
    }


    /**
     * 生成token
     */
    public function generateToken()
    {
        $time = $this->headers->get(self::API_REQUEST_TIME);
        $appkey = $this->app['appkey'];
        $appsecret = $this->app['appsecret'];
        return sha1($appkey . $appsecret . $time);
    }



}