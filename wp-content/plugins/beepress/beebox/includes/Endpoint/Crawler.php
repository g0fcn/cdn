<?php

namespace Bee\Beebox\Endpoint;
use Bee\Beebox;
use Bee\Beebox\Utils;

class Crawler {
    protected static $instance = null;

    private function __construct() {
        $plugin = Beebox\Plugin::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();
        $this->version = 1;
    }

    public function do_hooks() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public static function get_instance() {
        
        if(null == self::$instance) {
            self::$instance = new self;
            self::$instance->do_hooks();
        }
        return self::$instance;
    }

    public function register_routes() {
        $namespace = $this->plugin_slug . '/v' . $this->version;
        $endpoint = '/crawler/';

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
        $postID = '';
        $images = array();
        switch ($action) {
            case 'get_target_content':
                $targetUrl = $request->get_param('target_url');
                $authorID = $request->get_param('author');
                $postType = $request->get_param('post_type');
                $postStatus = $request->get_param('post_status');

                $crawler = new Utils\CrawlerCore($targetUrl);
                $targetPlatform = $crawler->targetPlatform;
                $response = $crawler->getTargetContent();

                // new post to pendding 
                if ($response['success']) {
                    // trail amount - 1
                    $trailAmount = Utils\LicenseCore::getTrailAmount();
                    $trailAmount -= 1;
                    Utils\LicenseCore::updateTrailAmount($trailAmount);
                    if (false) {
                        $res = array(
                            'success' => false,
                            'error_msg' => '试用机会已经用完'
                        );
                    } else {
                        $innerImgs = $response['data']['images']['inner_img'];
                        $innerImgs = array_filter($innerImgs);
                        $postContent = $response['data']['content'];
                        $postTitle = $response['data']['title'];
                        $postDate = $response['data']['post_date'];
                        $keepDuplicate = $request->get_param('keep_duplicate') == 'yes';
                        $keepLinks = $request->get_param('keep_links') == 'yes';
                        $removeImages = $request->get_param('removeImages');
                        $removeHTMLTags = $request->get_param('remove_html_tags');

                        $postTags = $request->get_param('post_tags');
                        $postCates = $request->get_param('post_cates');

                        if (post_exists( $postTitle) && !$keepDuplicate) {
                            $res = array(
                                'success' => false,
                                'error_msg' => '重复文章'
                            );
                        } else {
                            switch($removeHTMLTags) {
                                case 'keep':
                                    break;
                                case 'keep_media':
                                    $postContent = strip_tags($postContent, '<br><img><video><iframe><code><a><audio><canvas><input>');
                                    break;
                                case 'all':
                                    $postContent = strip_tags($postContent);
                                    break;
                                case 'specified':
                                    break;
                            }

                            switch($removeImages) {
                                case 'specified':
                                    $removeSpecifiedImages = $request->get_param('remove_specified_images');
                                    $newInnerImgs = array();
                                    foreach($innerImgs as $key => $innerImg) {
                                        if (in_array(intval($key) + 1, $removeSpecifiedImages)) {

                                        } else {
                                            $newInnerImgs[] = $innerImg;
                                        }
                                    }
                                    $innerImgs = $newInnerImgs;
                                    break;
                                case 'all':
                                    $innerImgs = array();
                                    $postContent = $crawler->removeAllImages($postContent);
                                    break;
                                case "keep":
                                    break;
                            }
                            if ($removeSpecifiedImages) {
                                $postContent = $crawler->filterImages($postContent, $innerImgs);
                            }
                            if (!$keepLinks) {
                                $postContent = $crawler->removeLinks($postContent);
                            }
                            $response['data']['images']['inner_img'] = $innerImgs;

                            $postData = array(
                                'post_title'    => $postTitle,
                                'post_name'     => substr(md5($postTitle . time()), 0, 10),
                                'post_content'  => $postContent,
                                'post_author'   => $authorID,
                                'post_type'	    => $postType,
                                'post_status'   => $postStatus,
                                'tags_input'    => $postTags,
                                'post_category' => $postCates,
                                'post_date'     => $postDate,
                            ); 
                            
                            $postID = wp_insert_post($postData);
                            $images = $response['data']['images'];
                            $res = array(
                                'success' => true,
                                'data' => array(
                                    'target_platform' => $targetPlatform,
                                    'post_id'    => $postID,
                                    'post_title' => $response['data']['title'],
                                    'post_link'  => get_permalink($postID),
                                    'images'  => $images,
                                    'referer' => $targetUrl,
                                    'date' => $postDate
                                )
                            );
                        }
                    }
                    
                } else {
                    $res = array(
                        'success' => false,
                        'error_msg' => $response['error_msg'],
                    );
                }
                break;
            case 'fetch_list_data':
                $listUrl = $request->get_param('listUrl');
                $rangeType = $request->get_param('range_type');
                $crawler = new Utils\CrawlerCore($listUrl, true);

                if ($rangeType == 'current') {
                    $res = $crawler->getTargetContent(true);
                    $res['current_page'] = 0;
                    $res['next_page'] = null;
                    $res['can_continue'] = false;
                } else {
                    $pageRange = $request->get_param('page_range');
                    $currentPage= $request->get_param('current_page');
                    $pageRangeArr = explode('|', $pageRange);
                    $startPage = intval($pageRangeArr[0]);
                    $endPage = intval($pageRangeArr[1]);

                    if ($currentPage > 0) {

                    } else {
                        $currentPage = $startPage;
                    }

                    $nextPage = $crawler->getNextPage($currentPage);
                    $crawler->targetUrl = $nextPage;
                    $res = $crawler->getTargetContent(true);
                    $res['next_page'] = $nextPage;
                    $currentPage++;
                    $res['current_page'] = $currentPage;

                    $list = $res['data']['list'];

                    $res['can_continue'] = ($endPage == 0 || $currentPage <= $endPage) && !empty($list);
                }

                break;    
            case 'get_config':
                $target = $request->get_param('target');
                $config = array();
                switch ($target) {
                    case 'crawler':
                        $users = Utils\Common::getUsers();
                        $currentUser = Utils\Common::getCurrentUser();
                        $cates = Utils\Common::getCategories();
                        $postTypes = Utils\Common::getPostTypes();
                        $config = array(
                            'current_user_id' => $currentUser,
                            'users' => $users,
                            'cates' => $cates,
                            'post_types' => $postTypes,
                        );
                        break;
                }
                $res = array(
                    'success' => true,
                    'data' => $config
                );
                break;    
            case 'upload_inner_img':
            case 'upload_feature_img':
                $postID = $request->get_param('post_id');
                $src = $request->get_param('src');
                if (!$src) {
                    $res = array(
                        'success' => false,
                        'error_msg' => '图片链接为空',
                    );
                } else {
                    $res = array(
                        'success' => false,
                        'error_msg' => '下载未开始',
                    );
                    $targetPlatform = $request->get_param('target_platform');
                    $referer = $request->get_param('referer');
                    // get the image rule
                    $targetRule = Utils\CrawlerCore::getRuleByTargetPlatform($targetPlatform);
                    $featureImgRule = array();
                    $proxyLink = null;

                    if ($targetRule) {
                        $featureImgRule = $targetRule['target']['img'];
                        $proxyLink = $featureImgRule['proxy_link'];
                    }

                    $imgExtension = 'jpeg';
                    $isMatch = preg_match('/(jpg|jpe|jpeg|gif|png)/i', $src, $extensionMatches);
                    if ($isMatch) {
                        $imgExtension = $extensionMatches[1];
                    }

                    $data = array(
                        'post_id' => $postID,
                        'img_src' => $proxyLink ? str_replace('{img}', $src, $proxyLink) : $src,
                        'platform' => $targetPlatform,
                        'type' => 'feature',
                        'referer' => $referer 
                    );

                    $uploadResult = Utils\CrawlerCore::uploadImage($data);
                    $tmpfile = $uploadResult['tmp_file'];
                    $imageFileArr = null;
                    if ($tmpfile) {
                        $imageFileArr = array(
                            'name' => rand(0, 10) . '' . time() . '.' . $imgExtension,
                            'tmp_name' => $tmpfile 
                        );
                        
                        $imageID = @media_handle_sideload($imageFileArr, $postID);
                        if (is_wp_error($imageID)) {
                            $res = array(
                                'success' => false,
                                'error_msg' => $imageID->get_error_message(),
                            );
                        } else {
                            if ($action == "upload_feature_img") {
                                @set_post_thumbnail($postID, $imageID);
                            }
                            $imageInfo = wp_get_attachment_image_src( $imageID, 'full');
                            if ($imageInfo) {
                                $imageSrc = $imageInfo[0];
                                $res = array(
                                    'success' => true,
                                    'data' => array(
                                        'post_id' => $postID,
                                        'image_id' => $imageID,
                                        'original_src' => $src,
                                        'new_src' => $imageSrc,
                                        'type' => $action == 'upload_feature_img' ? 'feature' : 'inner'
                                    )
                                );
                            } else {
                                $res = array(
                                    'success' => false,
                                    'error_msg' => '获取图片地址失败'
                                );
                            }
                        }
                    } else {
                        $res = array(
                            'success' => false,
                            'error_msg' => $uploadResult['error'],
                        );
                    }
                    @unlink($tmpfile);
                }
                break;    
            case 'update_post_image':
                $images = $request->get_param('images');
                $postID = $request->get_param('post_id');
                $post_content = get_post($postID);
                $postContent = $post_content->post_content;
                // $postContent = apply_filters('the_content', get_post_field('post_content', $postID));
                foreach($images as $image) {
                    $type = $image['type'];
                    $imageID = $image['image_id'];
                    if ($type == 'feature') {
                        @set_post_thumbnail($postID, $imageID);
                    } else {
                        // $scheme = parse_url($image['new_src'], PHP_URL_SCHEME);

                        $image['new_src'] = str_replace(array('http://', 'https://'), array('//', '//'), $image['new_src']);
                        $postContent = str_replace($image['original_src'], $image['new_src'], $postContent);

                        $image['original_src'] = str_replace(array('http://', 'https://'), array('//', '//'), $image['original_src']);
                        $postContent = str_replace($image['original_src'], $image['new_src'], $postContent);
                    }
                }
                $postID = wp_update_post(array(
                    'ID'           => $postID,
                    'post_content' => $postContent,
                ));
                if ($postID) {
                    $res = array(
                        'success' => true,
                        'data' => array(
                            'post_id' => $postID
                        )
                    );

                } else {
                    $res = array(
                        'success' => false,
                        'error_msg' => '文章图片更新失败'
                    );
                }
                break;    
            case 'save_single_rule':
                $data = $request->get_param('data');
                $target = $request->get_param('target');
                if(in_array($target, array('single', 'list'))) {
                    // update
                    $res = array(
                        'success' => true,
                        'data' => Utils\Common::updateConfig('crawler_single_rule', $data)
                    );
                } else {
                    $res = array(
                        'success' => false,
                        'error_msg' => '没有 ' . $target . ' 对应的规则'
                    );
                }
                break;
            case 'save_list_rule':
                $data = $request->get_param('data');
                $target = $request->get_param('target');
                if(in_array($target, array('single', 'list'))) {
                    // update
                    $res = array(
                        'success' => true,
                        'data' => Utils\Common::updateConfig('crawler_list_rule', $data)
                    );
                } else {
                    $res = array(
                        'success' => false,
                        'error_msg' => '没有 ' . $target . ' 对应的规则'
                    );
                }
                break;
            case 'get_rule': 
                $res = array(
                    'success' => true,
                    'data' => array(
                        'single' => get_option( 'crawler_single_rule', array() ),
                        'list' => get_option( 'crawler_list_rule', array() )
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