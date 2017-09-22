<?php

namespace Components;

use yii\composer\Installer;

class AdminlteInstaller extends Installer
{
    public static function initProject()
    {
        static::formatAdminLTE();
    }

    /**
     * 替换 AmdinLTE 模板的google api, 原因嘛....
     * [@link](/member/link) http://www.cmsky.com/google-fonts-ssl-ustc/.
     */
    public static function formatAdminLTE()
    {
        $vendorPath = 'vendor';
        $cssFile[] = $vendorPath.'/almasaeed2010/adminlte/dist/css/AdminLTE.css';
        $cssFile[] = $vendorPath.'/almasaeed2010/adminlte/dist/css/AdminLTE.min.css';
        foreach ($cssFile as $css) {
            self::replaceCss($css);
        }
    }

    /**
     * @param $cssFile
     */
    public static function replaceCss($cssFile)
    {
        if (file_exists($cssFile)) {
            $content = file_get_contents($cssFile);
            if ($content = str_replace('fonts.googleapis.com', 'fonts.css.network', $content)) {
                file_put_contents($cssFile, $content);
                echo "'{$cssFile}' google api replace success.\n";
            }
        } else {
            echo "'{$cssFile}' file is not exists.\n";
        }
    }
}
