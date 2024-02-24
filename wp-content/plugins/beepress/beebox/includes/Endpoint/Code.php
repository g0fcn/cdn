<?php

namespace Bee\Beebox\Endpoint;
use Bee\Beebox;
use Bee\Beebox\Utils;

class Code {
    protected static $instance = null;

    private function __construct() {
        $plugin = Beebox\Plugin::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();
        $this->version = 1;
    }

    public function do_hooks() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
        add_action( 'wp_head', array( $this, 'add_custom_code_in_head') );
        add_action( 'wp_footer', array( $this, 'add_custom_code_in_foot'), 20);
    }

    public static function get_instance() {
        
        if(null == self::$instance) {
            self::$instance = new self;
            self::$instance->do_hooks();
        }
        return self::$instance;
    }

    public function add_custom_code_in_head() {
        $customCodeArr = Utils\CodeCore::getCustomCode();
        foreach($customCodeArr as $code) {
            if($code['status'] == 'on' && $code['position'] == 'head') {
                echo $code['content'];
            } 
        }
    }

    public function add_custom_code_in_foot() {
        $customCodeArr = Utils\CodeCore::getCustomCode();
        foreach($customCodeArr as $code) {
            if($code['status'] == 'on' && $code['position'] == 'body') {
                echo $code['content'];
            } 
        }
    }

    public function register_routes() {
        $namespace = $this->plugin_slug . '/v' . $this->version;
        $endpoint = '/code/';

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
            case 'fetch':
                $codeData = Utils\CodeCore::getCustomCode();
                $res = array(
                    'success' => true,
                    'data' => $codeData
                );    
                break;    
            case 'save':
                $data = $request->get_param('data');
                $resData = Utils\CodeCore::saveCustomCode($data);
                $res = array(
                    'success' => true,
                    'data' => $resData,
                );    
                break;    
        }

        return new \WP_REST_Response(
            $res
            , 200
        );
    }
}