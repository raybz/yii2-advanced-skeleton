<?php
namespace Components\Utils;

class DateTime
{
    public static function parseRange($range, $defaultFrom, $defaultTo, $srcFormat = 'Ymd', $distFormat = 'Y-m-d')
    {
        if (! $range) {
            $from = date($distFormat, $defaultFrom);
            $to = date($distFormat, $defaultTo);
            $range = date($srcFormat, $defaultFrom) . '-' . date($srcFormat, $defaultTo);
        } else {
            list($from, $to) = explode('-', $range);
            $from = date($distFormat, strtotime($from));
            $to = (strtotime(date($srcFormat)) < strtotime(date($distFormat)) - 60) ? "{$to} 23:59:59" : $to;
            $to = date($distFormat, strtotime($to));
        }
        return array($range, $from, $to);
    }

    public static function parseChinese($datetime)
    {
        if (mb_substr($datetime, 0, 2, 'utf-8') == '今天') {
            $time = strptime($datetime, '%H:%M');
            $time = mktime($time['tm_hour'], $time['tm_min'], 0);
        } elseif (mb_substr($datetime, -3, 3, 'utf-8') == '分钟前') {
            $time = mb_substr($datetime, 0, -3, 'utf-8');
            $time = time() - 60 * $time;
        } elseif (!strpos($datetime, '月')) {
            $time = strptime($datetime, '%Y-%m-%d %H:%M');
            $time = mktime($time['tm_hour'], $time['tm_min'], 0, $time['tm_mon'], $time['tm_mday'], $time['tm_year'] + 1900);
        } else {
            $time = strptime($datetime, '%m月%d日 %H:%M');
            $time = mktime($time['tm_hour'], $time['tm_min'], 0, $time['tm_mon'], $time['tm_mday']);
        }

        return $time;
    }
}