<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use yii\db\Query;

class UserSearch extends Model
{
    public $user_id;

    public $username;

    public function rules()
    {
        return [
            ['user_id', 'integer'],
            ['username', 'string'],
        ];
    }

    public function scenarios()
    {
        return parent::scenarios();
    }

    public function search()
    {
        $query = (new Query())->from('user');
        if (empty($this->user_id) && empty($this->username)) {
            $query->andWhere('0=1');
        }
        $query->andFilterWhere(['id' => $this->user_id]);
        $query->andFilterWhere(['name' => $this->username]);
        list($sql, $sqlParams) = Yii::$app->mapi_slave->getQueryBuilder()->build($query);
        $count = $query->count('*', Yii::$app->mapi_slave);
        $dataProvider = new SqlDataProvider(
            [
                'sql' => $sql,
                'db' => Yii::$app->mapi_slave,
                'params' => $sqlParams,
                'totalCount' => $count,
                'sort' => [
                    'attributes' => [
                        'id',
                        'created_at',
                    ],
                    'defaultOrder' => [
                        'created_at' => SORT_DESC,
                    ],
                ],
                'pagination' => [
                    'pageSize' => 50,
                ],
            ]
        );

        return $dataProvider;
    }
}
