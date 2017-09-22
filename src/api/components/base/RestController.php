<?php
namespace api\components\base;

use Yii;
use yii\base\Model;
use yii\web\Response;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use api\components\ApiHeaderAuth;


class RestController extends ActiveController
{
    public $modelClass = '';
    public $serializer = [
        'class' => 'api\components\QuerySerializer',
        'collectionEnvelope' => 'items',
        'linksEnvelope' => false,
        'metaEnvelope' => 'pages',
    ];

    /**
     * 注销系统自带的方法
     */
    public function actions()
    {
        $actions = parent::actions();
        $methods = ['index', 'view', 'update', 'delete', 'create', 'view'];
        foreach ($methods as $item) {
            if (isset($actions[$item])) {
                unset($actions[$item]);
            }
        }
        return $actions;
    }

    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => ApiHeaderAuth::className(),
            ],
            [
                'class' => \yii\filters\ContentNegotiator::className(),
                'only' => ['index', 'view'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ]
            ],
        ];
    }

    public function actionIndex()
    {
        /* @var Object $modelClass */
        $modelClass = $this->modelClass;
        $pageSize = Yii::$app->request->get('page_size', 10);
        $query = $modelClass::find();
        return new ActiveDataProvider([
            'pagination' => [
                'pageSize' => intval($pageSize),
            ],
            'query' => $query
        ]);
    }

    public function actionView()
    {
        $id = Yii::$app->request->get('id', '');

        return $this->findMore($id);
    }

    protected function findMore($id)
    {
        $ids = is_string($id) ? preg_split('/\s*,\s*/', $id, -1, PREG_SPLIT_NO_EMPTY) : [];
        $idArr = array_filter($ids, 'is_numeric');
        /* @var Object $modelClass */
        $modelClass = $this->modelClass;
        if (count($idArr) == 1) {
            $id = array_pop($idArr);
            return $this->findModel($id);
        } else {
            $query = $modelClass::find()->where(['in', 'id', $idArr]);
            return new ActiveDataProvider([
                'pagination' => false,
                'query' => $query
            ]);
        }
    }

    protected function findModel($id)
    {
        /* @var Object $modelClass */
        $modelClass = $this->modelClass;
        if (($model = $modelClass::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('查询失败');
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        parent::checkAccess($action, $model, $params);
    }
}