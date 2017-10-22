<?php
use yii\helpers\Html;

?>
<header class="main-header">
    <?= Html::a(
        '<span class="logo-mini">APP</span><span class="logo-lg">'.Yii::$app->name.'</span>',
        Yii::$app->homeUrl,
        ['class' => 'logo']
    ) ?>
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-user"></i>
                        <span class="hidden-xs">
                            <?= isset(Yii::$app->user->identity->nickname) ? Yii::$app->user->identity->nickname : ''; ?>
                            <i class="caret"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu">

                        <li class="user-footer">
                            <div class="pull-left">
                                <?= Html::a(
                                    '修改密码',
                                    ['/site/edit'],
                                    ['class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                            <div class="pull-left">
                                <?= Html::a(
                                    '操作手册',
                                    ['/site/doc'],
                                    ['class' => 'btn btn-success btn-flat', 'target' => '_blank']
                                ) ?>
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    '退出',
                                    'http://id.2144.cn/auth/logout',
                                    ['data-method' => 'get', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>