<?php

namespace Bee\Beebox\Endpoint;
use Bee\Beebox;
use Bee\Beebox\Utils;

class CDN {
    protected static $instance = null;

    private function __construct() {
        $plugin = Beebox\Plugin::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();
        $this->version = 1;
    }

    public function do_hooks() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_action( 'delete_attachment', array( $this, 'delete_synced_image' ) );
        add_action( 'wp_loaded', array( $this, 'switch_to_cdn' ) );

        // add_filter( 'the_content', array( $this, 'switch_to_cdn' ) );
        add_filter( 'wp_generate_attachment_metadata', array( $this, 'sync_image' ) );
    }


    public static function get_instance() {
        if(null == self::$instance) {
            self::$instance = new self;
            self::$instance->do_hooks();
        }
        return self::$instance;
    }

    public function sync_image($meta) {
        $cdnState = get_option('beebox_qn_cdn_state', 'off');
        if ($cdnState == 'on') {
            Utils\CDNCore::syncSingleImage($meta);
        }
        return $meta;
    }

    public function delete_synced_image($id) {
        $cdnState = get_option('beebox_qn_cdn_state', 'off');
        if ($cdnState == 'on') {
            Utils\CDNCore::deleteImage($id);
        }
    }
    


    public function switch_to_cdn($content) {
        $cdnState = get_option('beebox_qn_cdn_state', 'off');
        if ($cdnState == 'on' && !is_admin()) {
            ob_start(array($this, 'cdn_domainname_replace'));
            // $wpUploadDir = wp_upload_dir();
            // $cdnHost = get_option('beebox_qn_domain_name', $wpUploadDir['baseurl']);
            // $content = str_replace($wpUploadDir['baseurl'], '//'.$cdnHost, $content);
            // $content = str_replace('//wp-content/uploads/', '/wp-content/uploads/', $content);
            // $content = str_replace('"/wp-content/uploads/', '"//' . $cdnHost, $content);
            // $content = str_replace('"wp-content/uploads/', '"//' . $cdnHost, $content);
        }
        return $content;
    }

    public function cdn_domainname_replace($html) {
        $wpUploadDir = wp_upload_dir();
        $baseUrl = $wpUploadDir['baseurl'];
        $cdnHost = get_option('beebox_qn_domain_name', $baseUrl);
        $uploadDir = str_replace(site_url(), '', $baseUrl);

        $cdn_exts = 'png|jpg|jpeg|gif|ico|eot|woff|ttf|ttf2';  //加速文件类型
        $regex2    = '/' . str_replace('/', '\/', site_url($uploadDir)) . '\/([^\s\?\\\'\"\;\>\<]{1,}.(' . $cdn_exts . '))([\"\\\'\s\?]{1})/';
        $html = preg_replace($regex2, '//' . $cdnHost . '/$1$3', $html); 

        return $html;
    }

    public function register_routes() {
        $namespace = $this->plugin_slug . '/v' . $this->version;
        $endpoint = '/cdn/';

        \register_rest_route($namespace, $endpoint, array(
            array(
                'methods'               => \WP_REST_Server::CREATABLE,
                'callback'              => array($this, 'handleSubmit'),
                'args'                  => array(),
                'permission_callback'   => function($request) {
                    // ... permissions check goes here
                    return current_user_can("edit_posts");
                },
            )
        ));
    }


    /**
     * @param WP_REST_Request
     * @return WP_Error|WP_REST_Response
     */
    public function handleSubmit($request) {
        $action = $request->get_param('action');
        switch ($action) {
            case 'update_config':
                $target = $request->get_param('target');
                $data = $request->get_param('data');
                Utils\CDNCore::updateConfig($target, $data);
                $res = array(
                    'success' => true,
                    'data' => $data,
                    'target' => $target
                );
                break;    
            case 'get_config':
                $target = $request->get_param('target');
                $data = Utils\CDNCore::getConfig($target);
                $wpUploadDir = wp_upload_dir();
                $res = array(
                    'success' => true,
                    'data' => $data,
                    'target' => $target,
                    'upload_dir' => $wpUploadDir,
                    'site_url' => site_url()
                );
                break;    
            case 'get_image_amount':
                $amount = Utils\CDNCore::getImageAmount();
                $res = array(
                    'success' => true,
                    'imageAmount' => $amount 
                );
                break;
            case 'sync_image':
                $target = $request->get_param('target');
                $offset = $request->get_param('offset');
                $amount = $request->get_param('amount');
                $size = $request->get_param('size');
                $res = array(
                    'success' =>  true,
                    'data' => Utils\CDNCore::syncImage($target, array(
                        'offset' => $offset,
                        'size' => $size 
                    )),
                    'can_continue' => ($amount - $offset) > 0,
                    'offset' => $offset + 1,
                    'size' => $size,
                    'amount' => $amount
                );
                break;
        }

        return new \WP_REST_Response(
            $res
            , 200
        );
    }
}