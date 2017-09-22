<?php

namespace console\models;

use common\models\mapi\UserGame;
use Yii;

/**
 * This is the model class for table "play_history".
 *
 * @property int $id
 * @property int $user_id
 * @property int $game_id
 * @property int $server_id
 * @property int $channel_id
 * @property int $pos_id
 * @property string $time
 * @property string $register_at
 * @property string $game_register_at
 * @property string $device_id
 * @property string $ip
 */
class PlayHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'play_history';
    }

    public static function insertData(array $data, UserGame $userInfoObj)
    {
        $model = new self();
        $model->user_id = $data['user_id'];
        $model->game_id = $userInfoObj->game_id;
        $model->server_id = isset($data['server_id']) ? $data['server_id'] : 0;
        $model->channel_id = $userInfoObj->channel_id;
        $model->pos_id = isset($userInfoObj->pos_id) ? $userInfoObj->pos_id : '';
        $model->time = $data['time'];
        $model->register_at = $userInfoObj->register_at;
        $model->game_register_at = $userInfoObj->created_at;
        $model->device_id = $data['device_id'];
        $model->ip = $data['ip'];

        return $model->save();
    }

    /**
     * getGameIdByAppKey desc.
     *
     * @param $userId
     * @param $gameId
     *
     * @return bool|UserGame
     */
    public static function getGameIdByAppKey($userId, $gameId)
    {
        $info = UserGame::find()
            ->where(
                'user_id = :user_id AND game_id = :game_id',
                [
                    ':user_id' => $userId,
                    ':game_id' => $gameId,
                ]
            )
            ->one(Yii::$app->mapi_slave);

        if ($info) {
            return $info;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'game_id', 'channel_id', 'pos_id', 'time', 'register_at', 'game_register_at'], 'required'],
            [['user_id', 'game_id', 'server_id', 'channel_id', 'pos_id'], 'integer'],
            [['time', 'register_at', 'game_register_at'], 'safe'],
            [['device_id', 'ip'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'game_id' => 'Game ID',
            'server_id' => 'Server ID',
            'channel_id' => 'Channel ID',
            'pos_id' => 'Pos ID',
            'time' => 'Time',
            'register_at' => 'Register At',
            'game_register_at' => 'Game Register At',
            'device_id' => 'Device ID',
            'ip' => 'Ip',
        ];
    }

    /**
     * @param $user_id
     *
     * @return array|null|\yii\db\ActiveRecord|PlayHistory
     */
    public static function getLastPlay($user_id)
    {
        return self::find()
            ->where('user_id=:user_id', [':user_id' => $user_id])
            ->orderBy(['time' => SORT_DESC])
            ->limit(1)
            ->one();
    }

    public static function getCountPlay($user_id)
    {
        return self::find()
            ->where('user_id=:user_id', [':user_id' => $user_id])
            ->count();
    }
}
