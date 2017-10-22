<?php
namespace backend\models\search;

use yii\base\Model;
use backend\models\Admin;
use yii\data\ActiveDataProvider;

class AdminSearch extends Admin
{
    public $user_id;

    public $username;

    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['username'], 'safe']
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }


    public function search($params)
    {
        $query = Admin::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC
                ]
            ],
            'pagination' => [
                'pageSize' => 100
            ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere(['id' => $this->user_id]);
        $query->andFilterWhere(['username' => $this->username]);
        return $dataProvider;
    }

}