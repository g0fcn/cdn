<?php
namespace Bee\Beebox\Utils;

class Common {

    public static function getUsers() {
        return get_users(array(
            'fields' => array('ID', 'user_nicename', 'display_name')
        ));
    }

    public static function getCurrentUser() {
        return get_current_user_id();
    }

    public static function getCategories($hideEmpty = false) {
        return get_categories(array(
            'hide_empty' => $hideEmpty,
        ));
    }
    
    public static function getTags($hideEmpty = false) {
        return get_tags(array(
            'hide_empty' => $hideEmpty,
        ));
    }

    public static function getPostTypes() {
        $types = get_post_types(array(
            'public' => true,
        ));
        $typeMap = array(
            'post' => '文章',
            'page' => '页面',
        );

        $typeArray = array();

        foreach($types as $type) {
            if (in_array($type, array('attachment'))) continue;
            $typeName = isset($typeMap[$type]) ? $typeMap[$type] : get_post_type_object($type)->name;
            $typeArray[] = array(
                'type' => $type,
                'name' => $typeName
            );
        }
        return $typeArray;
    }

    /**
     * @param $target 
     * @param $value
     */
    public static function updateConfig($target, $value) {
        switch($target) {
            case 'mb_post_template':
                $status = isset($value['status']) ? $value['status'] : get_option('mb_post_template_status', 'off');
                $position = isset($value['position']) ? $value['position'] : get_option('mb_post_template_position', 'end');
                $type = isset($value['type']) ? $value['type'] : get_option('mb_post_template_type', 'custom');
                $template_code = isset($value['template_code']) ? $value['template_code'] : get_option('mb_post_template_custom_code', '');
                $donate_title = isset($value['donate_title']) ? $value['donate_title'] : get_option('mb_post_donate_title', '感谢支持');
                $donate_qr_code_1 = isset($value['donate_qr_code_01']) ? $value['donate_qr_code_01'] : get_option('mb_post_template_donate_qr_code_1', '');
                $donate_qr_code_2 = isset($value['donate_qr_code_02']) ? $value['donate_qr_code_02'] : get_option('mb_post_template_donate_qr_code_2', '');
                $subcribe_qr_code = isset($value['subscribe_qr_code']) ? $value['subscribe_qr_code'] : get_option('mb_post_subscribe_title', '');
                $subcribe_title = isset($value['subscribe_title']) ? $value['subscribe_title'] : get_option('mb_post_template_subscribe_qr_code', '欢迎关注');

                update_option( 'mb_post_template_status', $status);
                update_option( 'mb_post_template_position', $position);
                update_option( 'mb_post_template_type', $type);
                update_option( 'mb_post_template_custom_code', $template_code);
                update_option( 'mb_post_donate_title', $donate_title);
                update_option( 'mb_post_template_donate_qr_code_1', $donate_qr_code_1);
                update_option( 'mb_post_template_donate_qr_code_2', $donate_qr_code_2);
                update_option( 'mb_post_subscribe_title', $subcribe_title);
                update_option( 'mb_post_template_subscribe_qr_code', $subcribe_qr_code);

                $value = array(
                    'status' => $status,
                    'position' => $position,
                    'type' => $type,
                    'template_code' => $template_code,
                    'donate_title' => $donate_title,
                    'donate_qr_code_01' => $donate_qr_code_1,
                    'donate_qr_code_02' => $donate_qr_code_2,
                    'subscribe_title' => $subcribe_title,
                    'subscribe_qr_code' => $subcribe_qr_code,
                );
                
                break;
            default:
                update_option($target, $value);
                break;
        }
        return array(
            'target' => $target,
            'value' => $value
        );
    }
}
