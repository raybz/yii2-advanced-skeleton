<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use kartik\widgets\DatePicker;

$this->title = '用户信息查询';

$columns = [
    ['attribute' => 'id', 'label' => '用户ID'],
    ['attribute' => 'name', 'label' => '用户名'],
    ['attribute' => 'pos_id', 'label' => 'POS_ID', 'value' => function ($data) {
        return $data['pos_id'] > 0 ? $data['pos_id'] : '';
    }],
    ['label' => '广告位名称', 'value' => function ($data) {
        return \common\models\mapi\Pos::getPosInfoById($data['pos_id'], 'name');
    }],
    ['label' => '渠道', 'value' => function ($data) {
        return \common\models\mapi\Channel::getChannelInfoById($data['channel_id'], 'name');
    }],
    ['attribute' => 'dev_id', 'label' => '设备标识'],
    ['label' => '注册IP', 'value' => function ($data) {
        return (new \yii\db\Query())->from('user_game')
            ->where('user_id = :user_id', [':user_id' => $data['id']])
            ->orderBy('created_at ASC')
            ->limit(1)->select('register_ip')
            ->scalar(Yii::$app->mapi_slave);
    }],
//    [
//        'label' => '玩第一款游戏',
//        'value' => function ($data) {
//            $gameInfo = (new \yii\db\Query())->from('user_game')
//                ->limit(1)
//                ->where('user_game.user_id = :user_id', [':user_id' => $data['id']])
//                ->orderBy('user_game.created_at ASC')
//                ->select('game.name, game.os')
//                ->leftJoin('game', 'user_game.game_id = game.id')
//                ->one(Yii::$app->mapi_slave);
//            $gameName = $gameInfo['name'];
//            $os = '(Android)';
//            if ($gameInfo['os'] == \common\models\mapi\Game::OS_IOS) {
//                $os = '(iOS)';
//            }
//            return $gameName . $os;
//        }
//    ],
    ['attribute' => 'created_at', 'label' => '注册时间'],
    [
        'header' => '详情',
        'class' => '\kartik\grid\ActionColumn',
        'template' => '{view}',
        'buttons' => [
            'view' => function ($url, $data) {
                $url = "http://backend.mapi.2144.cn/user/update/{$data['id']}";
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, ['title' => '详细信息', 'target' => '_blank', 'data-original-title' => '详细信息', 'data-toggle' => 'tooltip']);
            },
        ]
    ]
];
?>


<div class="box box-default">
    <div class="box-body">
        <div class="row">
            <?php $form = \yii\widgets\ActiveForm::begin([
                'method' => 'get',
            ]); ?>
            <div class="col-md-12">
                <div class="col-md-2">
                    <?= $form->field($searchModel, 'user_id')->textInput()->label('用户ID'); ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($searchModel, 'username')->textInput()->label('用户名'); ?>
                </div>
                <div class="col-md-2" style="line-height: 74px;">
                    <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
                </div>
            </div>
        </div>
        <?php \yii\widgets\ActiveForm::end(); ?>

    </div>
</div>

<?= GridView::widget([
    'autoXlFormat' => true,
    'showPageSummary' => true,
    'pageSummaryRowOptions' => ['class' => 'kv-page-summary default'],
    'export' => [
        'fontAwesome' => true,
        'showConfirmAlert' => false,
        'target' => GridView::TARGET_BLANK
    ],
    'dataProvider' => $dataProvider,
    'pjax' => true,
    'toolbar' => [
        ['content' => '',]
    ],
    'striped' => false,
    'hover' => true,
    'floatHeader' => false,
    'columns' => $columns,
    'responsive' => true,
    'condensed' => true,
    'panel' => [
        'heading' => '用户信息查询',
        'type' => 'default',
        'before' => false,
        'after' => false,
    ],
]); ?>
