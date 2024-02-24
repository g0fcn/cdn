<?php
namespace Bee\Beebox\Utils;

class PostCore {
    // get config 
    /**
     * @param $target 
     */
    public static function getConfig($target = null, $default = null) {
        $config = get_option($target, $default);
        return $config;
    }
}