<?php
namespace api\modules\v1\controllers;


use Yii;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{

    public function init()
    {
        Yii::$app->user->enableSession = false;
        Yii::$app->user->enableAutoLogin = false;
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($exception !== null) {
            return [
                'code' => $exception->statusCode,
                'data' => [],
                'message' => $exception->getMessage(),
            ];
        }
    }
}