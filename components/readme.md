# Yii2 项目公用组件

## 引入

使用 git subtree 将文件引入，其中 `src/components` 这个路径可以根据自己的项目进行修改。

```
git subtree add --prefix=src/components git@192.168.1.205:zhangzhoufei/yii2-components.git master
```

在 `composer.json` 中添加自动加载规则，其中 `src/components` 根据上一步的路径进行相应修改。

```
"autoload": {
    "psr-4": {
        "Components\\": "components/src"
    }
},
```

重新生成自动加载文件：

```
composer dump-autoload
```

## 各模块接入文档

### Id 后台统一用户认证

在配置文件 `main.php` 中的 `components` 中加入：

```php
'idauth' => [
    'class' => \Components\IdAuth::class,
    'systemId' => 0,
    'authKey' => 'xxx',
],
```

修改配置里的 `systemId` 和 `authKey` 为分配给待接入的项目的 `id` 和 `key`。

将后台的 `login` Action 改为未登录时 `return Yii::$app->idauth->authorize();`，例：

```php
public function actionLogin()
{
    if (!Yii::$app->user->isGuest) {
        return $this->goHome();
    }

    return Yii::$app->idauth->authorize();
}
```

然后后台的用户`model`实现`Components\IdRegisterInterface`即可。

### Audit 用户审计

在配置文件 `main.php` 中的 `components` 中加入：

```php
'audit' => [
    'class' => \Components\Audit::class,
    'systemId' => 0,
    'authKey' => 'xxx',
],
```

修改配置里的 `systemId` 和 `authKey` 为分配给待接入的项目的 `id` 和 `key`。

在后台的所有 `Controller` 的公共父类的 `beforeAction` 方法里加入：

```php
Yii::$app->audit->report();
```

在线上服务器上绑定 `hosts` ：

```
222.73.110.249 audit.2144.cn
```

接入完成，进行测试。

### Doc 接口注释提取

在配置文件 `main.php` 中的 `components` 中加入：

```php
'doc' => [
    'class' => \Components\Doc::class,
    'classes' => [
        \frontend\controllers\LookupController::class,
    ],
    'allowedIPs' => ['127.0.0.1'],
],
```

修改其中的 `classes` 为该项目中需要提取文档的所有类， 
`allowedIPs` 为文档服务器的 IP 地址和其他需要置为白名单的服务器地址。

在任意 `Controller` 内添加一个可以被访问的 `Action`，内容为：

```php
\Yii::$app->response->format = Response::FORMAT_JSON;
return \Yii::$app->doc->getDocs();
```

将地址提供给 `FusionApi` 系统即可。
