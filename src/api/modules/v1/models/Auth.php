<?php
namespace api\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class Auth extends ActiveRecord implements IdentityInterface
{

    const STATUS_ACTIVE = 1;

    public static function tableName()
    {
        return 'api_auth';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }



    public static function findIdentity($id)
    {

    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['appkey' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    public function getId()
    {

    }

    public function getAuthKey()
    {
    }

    public function validateAuthKey($authKey)
    {

    }
}
