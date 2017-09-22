<?php
namespace backend\helpers;
class Constants
{
    /**
     * 过滤的游戏
     */
    public static function operateFilterGames($flag = false)
    {
        $data = [
            10026 => '全民天堂',
            10020 => '神泣之光',
            10011 => '终极三国',
            10009 => '这不是三国',
            10001 => 'KO三国',
            10000 => '幻想神兵'
        ];
        if ($flag) {
            return $data;
        }
        return array_keys($data);
    }
}
