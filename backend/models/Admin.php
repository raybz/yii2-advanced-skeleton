<?php

namespace backend\models;

use common\models\behaviors\BaseTimestampBehavior;
use Components\IdRegisterInterface;
use mdm\admin\components\MenuHelper;
use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;


/**
 * Class Admin
 *
 * @property string   $authKey
 * @property integer  $id
 * @property string   $password
 * @property string   $nickname
 * @property string   $routes
 * @property string   $username
 * @property string   $status
 * @property  string  $login_times
 * @property  string  $last_login_time
 * @property  integer $nick
 * @property  string  $data
 * @property  string  $email
 * @property  string  $created_at
 * @property  string  $updated_at
 * @property  string  $auth_key
 * @property  string  $last_login_ip
 * @property  integer $password_reset_token
 * @property  integer $password_hash
 * @package backend\models
 */
class Admin extends ActiveRecord implements IdentityInterface, IdRegisterInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            BaseTimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne(
            [
                'password_reset_token' => $token,
                'status' => self::STATUS_ACTIVE,
            ]
        );
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];

        return $timestamp + $expire >= time();
    }

    public static function register($username, $password, $nick = null)
    {
        $model = static::findByUsername($username);
        if (!$model) {
            $model = new Admin();
            $model->username = $username;
            $model->generateAuthKey();
            $model->setPassword($password);
            $model->generatePasswordResetToken();
            $model->status = self::STATUS_ACTIVE;
            $model->email = $username.'@2144.cn';
            $model->nickname = $nick ? $nick : $username;
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = '1970-01-01 00:00:00';
            $model->save();

            return $model;
        }

        return false;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString().'_'.time();
    }

    /**
     * @param $userId
     * @return bool
     */
    public static function isAdmin($userId)
    {
        $roles = Yii::$app->authManager->getRolesByUser($userId);
        $bool = false;
        if ($roles) {
            foreach ($roles as $role) {
                if ($role->name == 'admin') {
                    $bool = true;
                    break;
                }
            }
        }

        return $bool;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getRoutes()
    {
        $menus = MenuHelper::getAssignedMenu($this->id);
        $routes = [];
        foreach ($menus as $menu) {
            if (empty($menu['items'])) {
                continue;
            }

            foreach ($menu['items'] as $item) {
                $routes = ArrayHelper::merge($routes, $item['url']);
            }
        }

        return $routes;
    }
}
