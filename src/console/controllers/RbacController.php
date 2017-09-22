<?php

namespace console\controllers;

use backend\models\Admin;
use mdm\admin\models\Menu;
use Yii;
use yii\console\Controller;

/**
 * Class RbacController.
 */
class RbacController extends Controller
{
    /**
     * @var \yii\rbac\DbManager
     */
    private $auth;
    public $id;

    public function init()
    {
        $this->auth = Yii::$app->authManager;
    }

    /**
     * 添加新用户.
     *
     * @param $username
     * @param $password
     * @param string $nick
     * @param string $email
     */
    public function actionAddUser($username, $password, $nick = '', $email = '')
    {
        $model = Admin::findOne(['username' => $username]);
        if (!$model) {
            $model = new Admin(['scenario' => 'signup']);
            $model->username = $username;
            $model->nick = $nick ?: $username;
            $model->setPassword($password);
            $model->generateAuthKey();
            $model->email = $email;
            $model->status = Admin::STATUS_ACTIVE;
            $model->password_reset_token = '';
            $model->created_at = date('Y-m-d H:i:s');
            if ($model->save()) {
                $this->stdout('成功添加,id:'.$model->id.PHP_EOL);
            }
        } else {
            $this->stdout("用户名‘{$username}’已存在");
        }
    }

    /**
     * 交互式创建用户.
     */
    public function actionCreateUser()
    {
        $username = $this->prompt('请输入用户名:');
        $nick = $this->prompt('请输昵称:');
        $password = $this->prompt('请输入密码:');
        $email = $this->prompt('请输入邮箱:');
        $model = new Admin();
        if (Admin::findByUsername($username)) {
            $this->stderr("{$username}已经存在！".PHP_EOL);
            exit;
        }
        $model->nick = $nick;
        $model->username = $username;
        $model->setPassword($password);
        $model->generateAuthKey();
        $model->email = $email;
        $model->status = Admin::STATUS_ACTIVE;
        $model->last_login_ip = '127.0.0.1';
        $model->last_login_time = date('Y-m-d H:i:s');
        $model->created_at = date('Y-m-d H:i:s');
        $model->login_times = 0;
        $model->data = '';
        if ($model->save()) {
            $this->id = $model->id;
            $this->stdout('创建成功'.PHP_EOL);
        } else {
            var_dump($model->getErrors());
            $this->stdout('创建失败'.PHP_EOL);
        }
    }

    /**
     * 修改用户密码
     *
     * @param $username
     * @param $password
     */
    public function actionPasswd($username, $password)
    {
        $model = Admin::findOne(['username' => $username]);
        if ($model) {
            $model->setScenario('passwd');
            $model->setPassword($password);
            $model->generateAuthKey();
            if ($model->save()) {
                $this->stdout('已经修改'.$model->nick.'密码'.$password.PHP_EOL);
            }
        } else {
            $this->stdout('用户不存在');
        }
    }

    /**
     * 添加新角色.
     *
     * @param $name
     * @param string $description
     */
    public function actionAddRole($name, $description = '')
    {
        $role = $this->auth->createRole($name);
        $role->description = $description ?: $name;
        $this->auth->add($role);
    }

    /**
     * 添加菜单项.
     *
     * @param $name
     * @param $description
     * @param null $data
     * @param null $rule
     */
    public function actionAddItem($name, $description, $data = null, $rule = null)
    {
        $item = $this->auth->createPermission($name);
        $item->description = $description;
        $item->data = $data;
        $item->ruleName = $rule;
        $item->createdAt = $item->updatedAt = time();
        $this->auth->add($item);
    }

    /**
     * 为角色分配权限.
     *
     * @param $role
     * @param $item
     */
    public function actionAddChild($role, $item)
    {
        $this->auth->addChild($this->auth->getRole($role), $this->auth->getPermission($item));
    }

    /**
     * 为用户分配角色.
     *
     * @param $username
     * @param $role
     */
    public function actionAssign($username, $role)
    {
        $user = Admin::findByUsername($username);
        $this->auth->assign($this->auth->getRole($role), $user['id']);
    }

    protected function addMenu()
    {
        $menu = new Menu();
        $menu->name = '管理员管理';
        $menu->order = 1;
        $menu->data = '{"icon": "users", "visiable":false}';
        $menu->save();
        $id = $menu->id;
        $menus = [
            '/admin/assignment/index' => '权限分配列表',
            '/admin/menu/index' => '菜单列表',
            '/admin/role/index' => '角色列表',
            '/admin/route/index' => '路由列表',
            '/admin/permission/index' => '权限项列表',
        ];
        $i = 1;
        foreach ($menus as $route => $name) {
            if (!in_array($route,  Menu::getSavedRoutes())) {
                $this->stdout('{'.$name."}不在权限表中".PHP_EOL);
                continue;
            }
            $menu = new Menu();
            $menu->name = $name;
            $menu->route = $route;
            $menu->parent = $id;
            $menu->order = $i++;
            $menu->save();
            $this->stdout("已经成功添加路由[{$name}]".PHP_EOL);
        }
    }
    /**
     * 初始化.
     */
    public function actionInit()
    {
        //创建用户
        $this->actionCreateUser();
        //创建admin最高角色
        $auth = $this->auth;
        static $role = 0;
        while (true) {
            $role = $this->prompt('请添加最角色名称[默认:admin]', ['default' => 'admin']);
            if ($auth->getRole($role)) {
                $this->stdout("该角色名称已经存在!, 请重新输入\n");
            } else {
                $roleObj = $this->auth->createRole($role);
                $roleObj->description = '超级管理员';
                $this->auth->add($roleObj);
                break;
            }
        }

        $permission = [
            '/admin/assignment/index' => '权限分配列表',
            '/admin/assignment/*' => '权限分配管理',
            '/admin/menu/index' => '菜单列表',
            '/admin/menu/*' => '菜单管理',
            '/admin/role/index' => '角色列表',
            '/admin/role/*' => '角色管理',
            '/admin/route/index' => '路由列表',
            '/admin/route/*' => '路由管理',
            '/admin/permission/index' => '权限项列表',
            '/admin/permission/*' => '权限管理',
        ];

        foreach ($permission as $name => $description) {
            $permission = $auth->getPermission($name);
            if ($permission) {
                $this->stdout('{'.$name."}权限已经存在\n");
                continue;
            }
            $permission = $auth->createPermission($name);
            $permission->description = $description;
            $auth->add($permission);
            $admin = $auth->getRole($role);
            $auth->addChild($admin, $permission);
            $this->stdout("已经成功添加权限[{$name}]到最高权限{$role}组中\n");
        }
        $this->auth->assign($this->auth->getRole($role), $this->id);
        $this->addMenu();
    }
}