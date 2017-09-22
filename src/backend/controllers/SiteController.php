<?php

namespace backend\controllers;

use backend\models\form\LoginForm;
use Yii;
use yii\web\Response;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class SiteController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'idregister', 'idlogin'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'doc'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect(['common/dashboard']);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

//        return Yii::$app->idauth->authorize();
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }


    public function actionIdregister($name, $nick, $token, $time)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return Yii::$app->idauth->register($name, $nick, $time, $token);
    }


    public function actionIdlogin()
    {
        $id = Yii::$app->request->get('id', null);
        $time = Yii::$app->request->get('time', null);
        $token = Yii::$app->request->get('token', null);
        if (Yii::$app->idauth->login($id, $time, $token)) {
            return $this->goHome();
        }
        echo '登陆失败';
    }

    /**
     * Logout action.
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionDoc()
    {
        $api = 'http://audit.2144.cn/manual/report';
        $params = [
            'sid' => Yii::$app->audit->systemId,
            'route' => implode(',', Yii::$app->user->identity->getRoutes()),
            'time' => time(),
        ];
        $params['token'] = md5(implode($params).Yii::$app->audit->authKey);

        return $this->redirect($api.'?'.http_build_query($params));
    }
}
