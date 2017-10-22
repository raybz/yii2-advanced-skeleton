<?php
namespace Components;

use Yii;
use yii\base\Component;
use yii\web\ForbiddenHttpException;

class Doc extends Component
{
    public $classes = [];

    public $allowedIPs = ['127.0.0.1', '::1'];

    public function init()
    {
        if (!$this->checkAccess()) {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }

        parent::init();
    }

    protected function checkAccess()
    {
        $ip = Yii::$app->getRequest()->getUserIP();
        foreach ($this->allowedIPs as $filter) {
            if ($filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos))) {
                return true;
            }
        }
        Yii::warning('Access to Gii is denied due to IP address restriction. The requested IP is ' . $ip, __METHOD__);

        return false;
    }

    public function getDocs()
    {
        $res = [];
        foreach ($this->classes as $class) {
            $reflection = new \ReflectionClass($class);
            $res[$class]['doc'] = $reflection->getDocComment();
            $res[$class]['methods'] = [];
            $methods = $reflection->getMethods();
            foreach ($methods as $method) {
                if ($method->class == $class && $method->isPublic() && strpos($method->name, 'action') !== false) {
                    $res[$class]['methods'][$method->name] = $method->getDocComment();
                }
            }
        }

        return $res;
    }
}