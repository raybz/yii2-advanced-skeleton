<?php

namespace Components;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    public $responseFormat = Response::FORMAT_JSON;
    public $errorCodeClass;

    public function init()
    {
        parent::init();
        Yii::$app->response->format = $this->responseFormat;
    }

    protected function response($code, $data = [], $message = '')
    {
        $response = [
            'code' => $code,
            'message' => $message ?: call_user_func([$this->errorCodeClass, 'getLabel'], $code),
            'data' => $data,
        ];

        if (Yii::$app->response->format == Response::FORMAT_JSONP) {
            Yii::$app->response->data['data'] = $response;
            Yii::$app->response->data['callback'] = Yii::$app->request->get('callback', 'callback');
        } else {
            Yii::$app->response->data = $response;
        }

        Yii::$app->end();
    }
}
