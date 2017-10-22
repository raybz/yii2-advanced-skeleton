<?php
namespace backend\components;

use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\filters\AccessControl;

class BaseController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }


    public function beforeAction($action)
    {
        try {
            Yii::$app->audit->report();
        } catch (Exception $e) {
        }
        return parent::beforeAction($action);
    }


}