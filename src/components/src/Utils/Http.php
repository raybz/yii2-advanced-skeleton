<?php

namespace Components\Utils;

class Http
{
    /**
     * @param $url
     * @param int $timeout
     *
     * @param mixed $cookies
     * @return string
     */
    public static function get($url, $timeout = 10, $cookies = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if (is_array($cookies)) {
            $cookies = static::parseCookie($cookies);
        }
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);

        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

    /**
     * @param $url
     * @param array $data
     * @param int   $timeout
     *
     * @return mixed
     */
    public static function post($url, $data, $timeout = 10)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

    /**
     * @param array  $data
     * @param string $callback
     * @param bool   $exit
     *
     * @return array|string
     */
    public static function responseJsonp(array $data, $callback = 'callback', $exit = true)
    {
        $callback = $callback ? (isset($_GET[$callback]) ? $_GET['callback'] : false) : false;
        $data = json_encode($data);
        $data = $callback ? $callback.'('.$data.');' : $data;
        if ($exit) {
            header('Content-type: text/javascript;charset=utf-8');
            echo $data;
            exit;
        } else {
            return $data;
        }
    }

    public static function parseCookie(array $cookies)
    {
        $tmp = array();
        foreach ($cookies as $key => $value) {
            if ($key != 'Array') {
                $tmp[] = $key.'='.$value;
            }
        }

        return implode(';', $tmp);
    }
}
