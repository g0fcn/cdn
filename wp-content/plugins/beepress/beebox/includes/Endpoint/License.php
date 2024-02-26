<?php

namespace Bee\Beebox\Endpoint;
use Bee\Beebox;
use Bee\Beebox\Utils;

use function vierbergenlars\SemVer\Internal\parse;

class License {
    protected static $instance = null;

    private function __construct() {
        $plugin = Beebox\Plugin::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();
        $this->version = 1;
    }

    public function do_hooks() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
        add_action('before_delete_post', array( $this, 'delete_images_of_post'));
        add_filter('rest_url', array( $this, 'process_rest_url'), 1, 4);
    }

    public static function get_instance() {
        
        if(null == self::$instance) {
            self::$instance = new self;
            self::$instance->do_hooks();
        }
        return self::$instance;
    }

    public function process_rest_url($url, $path, $bid, $scheme) {
        $newUrl = $url;
        if ($scheme == 'rest') {
            $newUrl = str_replace(home_url(), site_url(), $newUrl);
        }
        return $newUrl;
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
        $endpoint = '/license/';

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
        $restURL = rest_url();
        $pareseURL = parse_url($restURL);
        $domainName = isset($pareseURL['host']) ? $pareseURL['host'] : $_SERVER['SERVER_NAME'];
        switch ($action) {
            case 'check_license':
                $key = $request->get_param('key');
                $md5Code = md5($key . $domainName);
                Utils\LicenseCore::updateLicenseCode($md5Code);
                $startDate = Utils\LicenseCore::updateStartDate(time());
                $res = array(
                    'success' => true,
                    'key' => $md5Code,
                    'start_date' => $startDate,
                    'host' => $domainName,
                    'verification_code_state' => Utils\LicenseCore::getVerificationState(),
                    'trail_amount' => UTils\LicenseCore::getTrailAmount()
                );    
                break;    
            case 'get_license':
                $licenseCode = Utils\LicenseCore::getLicenseCode();
                $res = array(
                    'success' => true,
                    'key' => $licenseCode,
                    'host' => $domainName,
                    'verification_code_state' => Utils\LicenseCore::getVerificationState(),
                    'trail_amount' => UTils\LicenseCore::getTrailAmount()
                );    
                break;    
            case 'check_verifcation_code':
                $code = $request->get_param('code');
                $verificationRes = Utils\LicenseCore::checkVerificationCode($code);
                $res = array(
                    'success' => $verificationRes['success'],
                    'code' => $verificationRes['code'],
                    'amount' => $verificationRes['amount']
                );    
                break;    
            case 'get_verification':
                $verificationRes = Utils\LicenseCore::getVerification();
                $res = array(
                    'success' => $verificationRes['success'],
                    'code' => $verificationRes['code'],
                    'amount' => $verificationRes['amount']
                );    
                break;    
        }

        return new \WP_REST_Response(
            $res
            , 200
        );
    }
}