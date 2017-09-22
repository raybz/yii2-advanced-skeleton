<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use kartik\widgets\DatePicker;

\backend\assets\HighChartsAssets::register($this);
$this->title = '平台近日概况';

$columns = [
    ['attribute' => 'date', 'label' => '日期', 'pageSummary' => '总计'],
    ['label' => '活跃用户数', 'attribute' => 'active_user_count', 'pageSummary' => true],
    ['attribute' => 'register_user_count', 'label' => '注册用户数', 'pageSummary' => true],
    ['label' => '注册用户占比(%)', 'value' => function ($data) {
        if ($data['active_user_count']) {
            return round($data['register_user_count'] / $data['active_user_count'], 4) * 100;
        }
    }],
    ['label' => '付费人数', 'attribute' => 'user_pay_count', 'pageSummary' => true],
    ['label' => '付费渗透率(%)', 'value' => function ($data) {
        if ($data['active_user_count']) {
            return round($data['user_pay_count'] / $data['active_user_count'], 4) * 100;
        }
        return 0;
    }],
    ['label' => '付费金额', 'attribute' => 'user_pay_money', 'value' => function ($data) {
        return $data['user_pay_money'] / 100;
    }, 'pageSummary' => true],
    ['label' => 'ARPU', 'value' => function ($data) {
        $money = $data['user_pay_money'] / 100;
        $count = $data['user_pay_count'];
        if ($count) {
            return round($money / $data['user_pay_count'], 2);
        }
        return 0;
    }]

];
?>


    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <?php $form = \yii\widgets\ActiveForm::begin([
                    'method' => 'get',
                    'action' => '/common/dashboard'

                ]); ?>
                <div class="col-md-12">

                    <div class="col-md-3">
                        <label class="control-label">日期</label>
                        <?= DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'from',
                            'attribute2' => 'to',
                            'value' => '',
                            'value2' => '',
                            'options' => ['placeholder' => '注册时间', 'id' => 'from'],
                            'options2' => ['placeholder' => '注册时间', 'id' => 'to', 'display' => 'hidden'],
                            'type' => DatePicker::TYPE_RANGE,
                            'separator' => '到',
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'autoclose' => true,
                            ]
                        ]);
                        ?>
                    </div>
                    <div class="col-md-2" style="line-height: 74px;">
                        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
                    </div>
                </div>

                <?php \yii\widgets\ActiveForm::end(); ?>
            </div>
        </div>
    </div>


    <div class="box box-default">
        <!--折线图-->
        <div class="box-header with-border">
            <h3 class="box-title">图表</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
                <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>

        </div>
        <div class="box-body">
            <div id="higcharts-container"></div>

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
        'heading' => '平台近日概况[付费金额包含mpay数据]',
        'type' => 'default',
        'before' => false,
        'after' => false,
    ],
]); ?>

    <!--actionPlatformDateCharts-->
<?php
//每日注册/活跃走势图
$hcharts = <<<EOL
        var param = {
            api: '/api/platform-date-charts?',
            title: '',
            subtitle:'',
            container: 'higcharts-container',
            param: {
                from: '{$searchModel->from}',
                to: '{$searchModel->to}',
            }
        };
        var chart = new Hcharts(param);
        chart.showSpline();
EOL;
$this->registerJs($hcharts);


?>