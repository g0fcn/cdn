<?php
namespace Bee\Beebox\Utils;

class CodeCore {
    // get key 
    /**
     * @param $target 
     */
    public static function getCustomCode() {
        $codeArr = get_option('beebox_custom_code', array());
        return $codeArr;
    }

    /**
     * @param $target 
     */
    public static function saveCustomCode($data = array()) {
        update_option('beebox_custom_code', $data);
        return $data;
    }

}