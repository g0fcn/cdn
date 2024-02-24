<?php

namespace Bee\Beebox\Endpoint;
use Bee\Beebox;
use Bee\Beebox\Utils;

class Post {
    protected static $instance = null;

    private function __construct() {
        $plugin = Beebox\Plugin::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();
        $this->version = 1;
    }

    public function do_hooks() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
        add_action('before_delete_post', array( $this, 'delete_images_of_post'));
        add_filter( 'the_content', array( $this, 'process_inner_link' ) );
        add_filter( 'the_content', array( $this, 'add_template' ), 1000 );
    }

    public static function get_instance() {
        if(null == self::$instance) {
            self::$instance = new self;
            self::$instance->do_hooks();
        }
        return self::$instance;
    }

    public function add_template($content) {
        $templateStatus = get_option('mb_post_template_status', 'off');
        if($templateStatus == 'on') {
            $templateType = get_option('mb_post_template_type', 'custom');
            $templatePosition = get_option('mb_post_template_position', 'end');
            $templateStr = '';

            switch($templateType) {
                case 'custom':
                    $templateStr = get_option('mb_post_template_custom_code', '');
                break;
                case 'donate':
                    $donateTitle = get_option('mb_post_donate_title', '感谢支持');
                    $donateQRCode1 = get_option('mb_post_template_donate_qr_code_1', null);
                    $donateQRCode2 = get_option('mb_post_template_donate_qr_code_2', null);
                    
                    $templateStr .= '<hr><div style="width: 100%;">';
                    $templateStr .= '<div style="width: 100%;">';
                    $templateStr .= '<span style="text-align: center;display: block;">' . $donateTitle . '</span>';
                    $templateStr .= '</div>';
                    $templateStr .= '<div style="width: 100%;justify-content: center;display: flex;">';
                    $templateStr .= '<img style="margin: 10px;" width="40%" height="auto" src="' . $donateQRCode1 . '">';
                    $templateStr .= '<img style="margin: 10px;" width="40%" height="auto" src="' . $donateQRCode2 . '">';
                    $templateStr .= '</div>';
                    $templateStr .= '</div>';
                break;
                case 'subscribe':
                    $subscribeTitle = get_option('mb_subscribe_title', '扫码关注');
                    $subscribeQRCode = get_option('mb_post_template_subscribe_qr_code', null);
                    
                    $templateStr .= '<hr><div style="width: 100%;">';
                    $templateStr .= '<div style="width: 100%;">';
                    $templateStr .= '<span style="text-align: center;display: block;">' . $subscribeTitle . '</span>';
                    $templateStr .= '</div>';
                    $templateStr .= '<div style="width: 100%;justify-content: center;display: flex;">';
                    $templateStr .= '<img style="margin: 10px;" width="40%" height="auto" src="' . $subscribeQRCode . '">';
                    $templateStr .= '</div>';
                    $templateStr .= '</div>';
                break;
                default:
                break;
            }

            if($templatePosition == 'end') {
                $content .= $templateStr;
            } else {
                $content = $templateStr . $content;
            }
        } else {

        }
        return $content;
    }

    public function process_inner_link($content) {
        $innerLinkState = get_option('mb_post_inner_link', 'off');
        if($innerLinkState != 'on') {
            return $content;
        } else {
            $cates = Utils\Common::getCategories(true);
            $tags = Utils\Common::getTags(true);
            $ex_word = '';
            $case = '';
            $limit = intval(get_option('mb_post_keyword_limit', -1));

            foreach($tags as $tag) {
                $link = get_tag_link( $tag->term_id );
                $keyword = $tag->name;
                $cleankeyword = stripslashes($keyword);
                $url = "<a href=\"$link\" title=\"".str_replace('%s',addcslashes($cleankeyword, '$'),__('%s'))."\"";
                $url .= ' target="_self" class="tag_link"';
                $url .= ">".addcslashes($cleankeyword, '$')."</a>";
                // $limit = ;//rand($match_num_from,$match_num_to);
                $content = preg_replace( '|(<a[^>]+>)(.*)('.$ex_word.')(.*)(</a[^>]*>)|U'.$case, '$1$2%&&&&&%$4$5', $content);
                $content = preg_replace( '|(<img)(.*?)('.$ex_word.')(.*?)(>)|U'.$case, '$1$2%&&&&&%$4$5', $content);
                $cleankeyword = preg_quote($cleankeyword,'\'');
                $regEx = '\'(?!((<.*?)|(<a.*?)))('. $cleankeyword . ')(?!(([^<>]*?)>)|([^>]*?</a>))\'s' . $case;
                $content = preg_replace($regEx,$url,$content,$limit);
                $content = str_replace( '%&&&&&%', stripslashes($ex_word), $content);
            }

            foreach($cates as $tag) {
                $link = get_category_link( $tag->term_id );
                $keyword = $tag->name;
                $cleankeyword = stripslashes($keyword);
                $url = "<a href=\"$link\" title=\"".str_replace('%s',addcslashes($cleankeyword, '$'),__('%s'))."\"";
                $url .= ' target="_self" class="tag_link"';
                $url .= ">".addcslashes($cleankeyword, '$')."</a>";
                // $limit = -1;//rand($match_num_from,$match_num_to);
                $content = preg_replace( '|(<a[^>]+>)(.*)('.$ex_word.')(.*)(</a[^>]*>)|U'.$case, '$1$2%&&&&&%$4$5', $content);
                $content = preg_replace( '|(<img)(.*?)('.$ex_word.')(.*?)(>)|U'.$case, '$1$2%&&&&&%$4$5', $content);
                $cleankeyword = preg_quote($cleankeyword,'\'');
                $regEx = '\'(?!((<.*?)|(<a.*?)))('. $cleankeyword . ')(?!(([^<>]*?)>)|([^>]*?</a>))\'s' . $case;
                $content = preg_replace($regEx,$url,$content,$limit);
                $content = str_replace( '%&&&&&%', stripslashes($ex_word), $content);
            }
            return $content;
        }
    }

    /**
     * @param $postId
     */
    public function delete_images_of_post($postId) {
		if (get_option('mb_post_image_process', 'none') == 'delete') {
			$images = get_attached_media('image', $postId);
			foreach ($images as $image) {
				wp_delete_attachment($image->ID);
			}
		}
    }

    public function register_routes() {
        $namespace = $this->plugin_slug . '/v' . $this->version;
        $endpoint = '/post/';

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
            case 'get_config':
                $target = $request->get_param('target');
                $config = array();
                switch ($target) {
                    case 'mb_post_image_process':
                        $config['image_process'] = Utils\PostCore::getConfig($target, 'none');
                        break;
                    case 'mb_post_inner_link':
                        $config['post_inner_link'] = Utils\PostCore::getConfig($target, 'off');
                        break;
                    case 'mb_post_inner_link_options':
                        $config['link_options'] = Utils\PostCore::getConfig($target, array());
                        break;
                    case 'mb_post_keyword_limit':
                        $config['keyword_limit'] = Utils\PostCore::getConfig($target, array());
                        break;
                    case 'mb_post_template':
                        $config['post_template'] = array(
                            'status' => Utils\PostCore::getConfig('mb_post_template_status', 'off'),
                            'position' => Utils\PostCore::getConfig('mb_post_template_position', 'end'),
                            'type' => Utils\PostCore::getConfig('mb_post_template_type', 'custom'),
                            'template_code' => Utils\PostCore::getConfig('mb_post_template_custom_code', ''),
                            'donate_title' => Utils\PostCore::getConfig('mb_post_donate_title', '感谢支持'),
                            'donate_qr_code_01' => Utils\PostCore::getConfig('mb_post_template_donate_qr_code_1', ''),
                            'donate_qr_code_02' => Utils\PostCore::getConfig('mb_post_template_donate_qr_code_2', ''),
                            'subscribe_title' => Utils\PostCore::getConfig('mb_subscribe_title', '欢迎关注'),
                            'subscribe_qr_code' => Utils\PostCore::getConfig('mb_post_template_subscribe_qr_code', '')
                        );
                        break;
                }
                $res = array(
                    'success' => true,
                    'data' => $config,
                    'request' => array(
                        'target' => $target,
                    )
                );
                break;    
            case 'update_config':
                $target = $request->get_param('target');
                $value = $request->get_param('value');
                $updateRes = Utils\Common::updateConfig($target, $value);
                $optionKey = '';
                switch($target) {
                    case 'mb_post_image_process':
                        $optionKey = 'image_process';
                        break;
                    case 'mb_post_inner_link':
                        $optionKey = 'post_inner_link';
                        break;
                    case 'mb_post_inner_link_options':
                        $optionKey = 'link_options';
                        break;
                    case 'mb_post_keyword_limit':
                        $optionKey = 'keyword_limit';
                        break;
                    case 'mb_post_template':
                        $optionKey = 'post_template';
                        break;
                }

                $res = array(
                    'success' => true,
                    'data' => array(
                        $optionKey => $updateRes['value']
                    ),
                    'request' => array(
                        'target' => $target,
                        'value' => $value
                    )
                );
                break;
        }

        return new \WP_REST_Response(
            $res
            , 200
        );
    }
}