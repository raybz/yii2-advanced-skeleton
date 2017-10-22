<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use kartik\widgets\DatePicker;
use kartik\export\ExportMenu;

$this->title = '订单数据查询';

$columns = [
    ['attribute' => 'user_id', 'label' => '用户ID', 'pageSummary' => '总计'],
    [
        'attribute' => 'pos_id',
        'label' => 'POS_ID',
        'value' => function ($model) {
            $label = $model['pos_id'];
            if ($model['pos_id'] == 0) {
                $label = '';
            }

            return $label;
        },
    ],
    [
        'label' => '广告位名称',
        'value' => function ($model) {
            return \common\models\mapi\Pos::find()->where(['id' => $model['pos_id']])->select('name')->scalar();
        },
    ],
    ['attribute' => 'channel_id', 'label' => '渠道ID'],
    ['attribute' => 'game_id', 'label' => '游戏ID'],
    [
        'attribute' => 'game_name',
        'label' => '游戏名',
        'value' => function ($model) {
            return \common\models\mapi\Game::getName($model['game_id']);
        },
    ],
    ['attribute' => 'sn', 'label' => '订单号'],
//    ['attribute' => 'trade_no', 'label' => '第三方订单号'],
    ['attribute' => 'pass_param', 'label' => '透传参数'],
    [
        'attribute' => 'money',
        'label' => '充值金额',
        'value' => function ($model) {
            return $model['money'] / 100;
        },
        'pageSummary' => true,
    ],
    ['attribute' => 'paytype', 'label' => '充值方式'],
    [
        'attribute' => 'source',
        'label' => '订单来源',
        'value' => function ($model) {
            $label = '';
            switch ($model['source']) {
                case common\models\db\Payment::PAY_SOURCE_MPAY:
                    $label = 'mpay';
                    break;
                case common\models\db\Payment::PAY_SOURCE_SDK:
                    $label = '2144sdk';
                    break;
            }

            return $label;
        },
    ],
    ['attribute' => 'game_register_at', 'label' => '注册时间'],
    ['attribute' => 'created_at', 'label' => '充值时间'],
];
?>


<div class="box box-default">
    <div class="box-body">
        <div class="row">
            <?php $form = \yii\widgets\ActiveForm::begin(
                [
                    'method' => 'get',
                    'action' => '/common/payment',

                ]
            ); ?>
            <div class="col-md-12">
                <div class="col-md-2">
                    <?= $form->field($searchModel, 'user_id')->textInput()->label('用户ID'); ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($searchModel, 'pos_id')->textInput()->label('POS_ID'); ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($searchModel, 'sn')->textInput()->label('订单号查询'); ?>
                </div>
                <div class="col-md-2" style="line-height: 74px;">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">游戏:</label>
                        <div class="col-sm-8">
                            <style>
                                .dropdown-menu > li > a {
                                    display: block;
                                    padding: 3px 40px;
                                    clear: both;
                                    font-weight: normal;
                                    line-height: 1.42857143;
                                    color: #333;
                                    white-space: nowrap;
                                }
                            </style>
                            <?=
                            \dosamigos\multiselect\MultiSelect::widget(
                                [
                                    'id' => 'md',
                                    "options" => ['multiple' => "multiple"],
                                    'data' => \common\models\mapi\Game::gameDropDownData(),
                                    'model' => $searchModel,
                                    'attribute' => 'game_id',
                                    "clientOptions" =>
                                        [
                                            'enableFiltering' => true,
                                            "selectAllText" => '全选',
                                            "includeSelectAllOption" => true,
                                            'numberDisplayed' => false,
                                            'maxHeight' => 0,
                                            'nonSelectedText' => '选择游戏',
                                        ],
                                ]
                            );

                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="control-label">充值日期</label>
                    <?= DatePicker::widget(
                        [
                            'model' => $searchModel,
                            'attribute' => 'from',
                            'attribute2' => 'to',
                            'value' => '',
                            'value2' => '',
                            'options' => ['placeholder' => '开始时间', 'id' => 'from'],
                            'options2' => ['placeholder' => '结束时间', 'id' => 'to', 'display' => 'hidden'],
                            'type' => DatePicker::TYPE_RANGE,
                            'separator' => '到',
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'autoclose' => true,
                            ],
                        ]
                    );
                    ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-2">
                    <?= $form->field($searchModel, 'trade_no')->textInput()->label('第三方流水号'); ?>
                </div>
                <div class="col-md-2" style="line-height: 74px;">
                    <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
                </div>
            </div>
        </div>
        <?php \yii\widgets\ActiveForm::end(); ?>

    </div>
</div>

<?= GridView::widget(
    [
        'autoXlFormat' => true,
        'showPageSummary' => true,
        'pageSummaryRowOptions' => ['class' => 'kv-page-summary default'],
        'export' => [
            'fontAwesome' => true,
            'showConfirmAlert' => false,
            'target' => GridView::TARGET_BLANK,
        ],
        'dataProvider' => $dataProvider,
        'pjax' => false,
        'toolbar' => [
            ExportMenu::widget(
                [
                    'dataProvider' => $dataProvider,
                    'columns' => $columns,
                    'fontAwesome' => true,
                    'target' => ExportMenu::TARGET_BLANK,
                    'pjaxContainerId' => 'payment_grid',
                    'asDropdown' => true,
                    'showColumnSelector' => false,
                    'dropdownOptions' => [
                        'label' => '导出数据',
                        'class' => 'btn btn-default',
                        'itemsBefore' => [
                            '<li class="dropdown-header">导出全部数据</li>',
                        ],
                    ],
                ]
            ),
        ],
        'striped' => true,
        'hover' => true,
        'floatHeader' => false,
        'columns' => $columns,
        'responsive' => true,
        'condensed' => true,
        'panel' => [
            'heading' => '用户订单数据',
            'type' => 'default',
            'after' => false,
        ],
    ]
); ?>
