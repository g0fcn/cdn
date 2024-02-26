<?php
namespace Bee\Beebox\Utils;
use \Curl\Curl;

use function PHPSTORM_META\type;

if(!class_exists('simple_html_dom_node')){
	require_once("simple_html_dom.php");
}

class CrawlerCore {

    protected static $instance = null;
    public $targetUrl = '';
    protected $rule = null;
    public $targetPlatform = null;

    public function __construct($url = '', $isList = false)
    {
        $this->targetUrl = $url;
        if ($isList) {
            // get list rule
            $this->getListRule();
        } else {
            $this->getRule();
        }
    }

    /**
     * @param String $targetPlatform
     */
    public static function getRuleByTargetPlatform($targetPlatform = null) {
        $config = require_once(__DIR__ . '/../Config/crawler.php');
        return isset($config['rules'][$targetPlatform]) ? $config['rules'][$targetPlatform] : array();
    }

    protected function getListRule() {
        // 根据 $url 获取对应的规则
        $targetUrl = str_replace(array('http://', 'https://'), '', $this->targetUrl) ;

        // 获取所有规则
        $listRuleArr = get_option( 'crawler_list_rule', array() );
        // 匹配对应的规则
        $isMatched = false;
        foreach($listRuleArr as $rule) {
            // $ruleArr = explode('|', $rule['template']);
            // $ruleType = $ruleArr[0];
            // $ruleStr = $ruleArr[1];
            // if ($ruleType == 1) {
            // } else {
            //     // do nothing
            // }
            $isMatched = preg_match($rule['template'], $targetUrl, $matches) == 1;
            if ($isMatched) {
                $this->rule = $rule;
                break;
            }
        }
    }

    /**
     * 
     */

    public function getPostList($response = '') {
        if ($this->rule) {
            $linkRule = $this->rule['link'];
            // var_dump();exit;
            $dirName = pathinfo($this->rule['replace'], PATHINFO_DIRNAME);
            // $domainName = pathinfo($this->rule['replace'], Pathinfo);
            
            $htmlDom = str_get_html($response);
            $links = $htmlDom->find($linkRule);
            $hrefes = array();
            foreach($links as $link) {
                $href = $link->getAttribute('href');
                if (strstr($href, $dirName)) {

                } else {
                    $href = $dirName . '/' . $href;
                }
                $hrefes[] = $href;
            }
            return $hrefes;
        } else {
            return array();
        }
    }

    /**
     * @param String $url Target url
     */
    protected function getRule() {
        // 根据 $url 获取对应的规则
        $config = require_once(__DIR__ . '/../Config/crawler.php');
        $targetMap = $config['target_map'];
        $targetUrl = str_replace(array('http://', 'https://'), '', $this->targetUrl) ;

        // 获取所有规则
        $singleRuleArr = get_option( 'crawler_single_rule', array() );
        // 返回对应的规则
        $keyLen = 0;
        $match = null;
        foreach($singleRuleArr as $rule) {
            $ruleUrl =  str_replace(array('http://', 'https://'), '', $rule['url']);
            if (strstr($targetUrl, $ruleUrl)) {
                $match = strlen($ruleUrl) > $keyLen ? $ruleUrl : $match;
                $keyLen = strlen($ruleUrl);
            }
        }

        if ($match) {
            $rule = null;
            foreach($singleRuleArr as $r) {
                $ruleUrl =  str_replace(array('http://', 'https://'), '', $r['url']);
                if ($match == $ruleUrl) {
                    // 处理规则
                    $titleRuleSplit = explode('|', $r['title']);
                    $contentRuleSplit = explode('|', $r['content']);
                    $innerImageRuleSplit = explode('|', $r['inner_image']);
                    $encode = $r['encode'];
                    $rule = array(
                        'name' => $r['name'],
                        'target' => array(
                            'title' => array(
                                'type' => intval($titleRuleSplit[0]),
                                'rule' => $titleRuleSplit[1]
                            ),
                            'content' => array(
                                'type' => intval($contentRuleSplit[0]),
                                'rule' => $contentRuleSplit[1]
                            ),
                            'img' => array(
                                'inner_img' => array(
                                    'type' => intval($innerImageRuleSplit[0]),
                                    'rule' => $innerImageRuleSplit[1],
                                    'src' => $innerImageRuleSplit[2]
                                )
                            ),
                            'charset' => array(
                                'default' => $encode
                            ),
                        )
                    );
                    break;
                }
            }
            $this->targetPlatform = $match;
            $this->rule = $rule;
        } else {
            foreach($targetMap as $key => $name) {
                if (strstr($targetUrl, $key)) {
                    $match = strlen($key) > $keyLen ? $name : $match;
                    $keyLen = strlen($key);
                }
            }

            $this->targetPlatform = $match;
            $this->rule = isset($config['rules'][$match]) ? $config['rules'][$match] : null;
        }
    }

    public static function getInstance() {
        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * @param String $url Target url 
     */
    public function getTargetContent($isList = false) {
        // get rule
        $rule = $this->rule;
        $curl = new Curl();
        $title = '';
        $content = '';
        $images = array();
        if (empty($this->rule)) {
            return array(
                'success' => false,
                'error_code' => 10002,
                'error_msg' => '请先配置规则',
            );
        } else {
            $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
			$curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
            $curl->setOpt(CURLOPT_SSLVERSION, 3);
            $curl->get($this->targetUrl);
            // $statusCode = $curl->getHttpStatusCode();
            $response = $curl->response;
            if (!$response) {
				$response = @file_get_contents($this->targetUrl);
            }

            if ($rule['target']['charset']['default'] != 'utf8') {
                $response = @iconv(strtolower($rule['target']['charset']['default']), 'utf8', $response);
            }

            if ($isList) {
                // can continue?
                return array(
                    'success' => true,
                    'data' => array(
                        'list' => $this->getPostList($response)
                    ),
                    'link' => $this->targetUrl
                );
            } else {
                // get post date
                $postDate = $this->getPostDate($response);
                // get title
                $title = $this->getTitle($response);
                // if have title
                if ($title) {
                    // TODO: keep the right order
                    // get images
                    // get feature image
                    // $images

                    $imageRules = $rule['target']['img'];
                    $featureImgRule = $imageRules['feature_img'];
                    $featureImgMatches = $this->getMatch($response, $featureImgRule, true);


                    $content = $this->getRealContent($response);

                    $images = $this->getImages($content);


                    // $featureImgSrcArr = array();
                    // switch($featureImgRule['type']) {
                    //     case 1:
                    //         $featureImgSrcArr[] = $featureImgMatches;
                    //         break;
                    //     case 2:
                    //         foreach($featureImgMatches as $match) {
                    //             $src = $match->getAttribute($featureImgRule['src']);
                    //             // process the image src
            
                    //             // get the scheme of post link
                    //             $scheme = parse_url($this->targetUrl, PHP_URL_SCHEME);
                    //             if (stristr($src, 'http://') || stristr($src, 'https://')) {
            
                    //             } else {
                    //                 $src = str_replace(array('//'), array($scheme . '://'), $src);
                    //             }
            
                    //             if ($src) {
                    //                 $featureImgSrcArr[] = $src;
                    //             }
                    //         }    
                    //         break;    
                    // }

                    $images['feature_img'] = $featureImgMatches;
                    // get real content

                    // $content = $this->getRealContent($content);

                    // var_dump($content);
                } else {
                    // failed
                    return array(
                        'success' => false,
                        'error_code' => 10001,
                        'error_msg' => '无法文章获取标题',
                    );
                }
            }
        }
        return array(
            'success' => true,
            'data' => array(
                'title' => $title,
                'content' => $content,
                'post_date' => $postDate,
                'images' => $images
            ),
        );
    }

    /**
     * @param String $content Post Content
     * @param Array $innerImgs Images in Content
     * @return String
     */
    public function filterImages($content = '', $innerImgs = array()) {
        $contentDom = str_get_html($content);
        if($contentDom) {
            $imgDoms = $contentDom->find('img');

            foreach($imgDoms as $imgDom) {
                $imgSrc = $imgDom->getAttribute('data-src');
                if (!in_array($imgSrc, $innerImgs)) {
                    $imgDom->outertext = '';
                }
            }
            return $contentDom->outertext;
        } else {
            return $content;
        }
    }

    /**
     * @param String $content Post Content
     * @param Array $innerImgs Images in Content
     * @return String
     */
    public function removeAllImages($content = '') {
        $contentDom = str_get_html($content);
        if($contentDom) {
            $imgDoms = $contentDom->find('img');

            foreach($imgDoms as $imgDom) {
                $imgSrc = $imgDom->getAttribute('data-src');
                $imgDom->outertext = '';
            }
            return $contentDom->outertext;
        } else {
            return $content;
        }
    }

    /**
     * @param String $content
     * @return String $content
     */
    public function removeLinks($content = '') {
        $content = preg_replace('/<a[^>]*>(.*?)<\/a>/i', '', $content);
        return $content;
    }

    /**
     * @param String $content Target Site Content
     * @return String $title Target Site Title
     */
    public function getTitle($content = '') {
        $title = '';
        if (!$content) return $title;
        $rule = $this->rule;
        $target = $rule['target'];
        $titleRule = $target['title'];
        $title = $this->getMatch($content, $titleRule);
        return $title;
    }

    /**
     * @param String $content Target Site Content
     * @return String $publishTime Target Site Title
     */
    public function getPostDate($content = '') {
        $postDate = time();
        // return $postDate;
        // if (!$content) return $postDate;
        // $rule = $this->rule;
        // $target = $rule['target'];
        // $publishTimeRule = $target['publish_time'];
        // $publishTime = $this->getMatch($content, $publishTimeRule);
        $publishTime = date('Y-m-d H:i:s', $postDate);
        return $publishTime;
    }


    public function getNextPage($page = 0, $size = 20) {
        $rule = $this->rule;
        $replaceTemplate = $rule['replace'];
        // $page += 1;
        $nextPage = str_replace(array('{$1}', '${2}'), array($page, $size), $replaceTemplate);
        // $this->targetUrl = $nextPage;
        return $nextPage;
    }

    /**
     * @param String $content Target Site Content
     * @return String $content Target Site Real Content
     */
    public function getRealContent($content = '') {
        if (!$content) return '';
        $rule = $this->rule;
        $contentRule = $rule['target']['content'];
        // get body
		$content = preg_replace('/<script[\s\S]*?<\/script>/i', '', $content);
		$content = preg_replace('/<link[\s\S]*?<\/link>/i', '', $content);
		$content = preg_replace('/<noscript[\s\S]*?<\/noscript>/i', '', $content);
        // $isMatch = preg_match('/<body(.*)<\/body>/s', $content, $tmpMatches);
        // $content = $tmpMatches[0];
        
        $contentMatches = $this->getMatch($content, $contentRule);
        if ($contentMatches) {
            switch($contentRule['type']) {
                case 1:
                    $content = $contentMatches;
                    break;
                case 2:
                    $content = $contentMatches;
                    // $contentArr = array();
                    // foreach($contentMatches as $match) {
                    //     $contentArr[] = $match->innertext;
                    // }
                    // $content = implode('<br/>', $contentArr);
                    break;    
            }
        } else {
            switch($contentRule['type']) {
                case 2:
                    // return $content;
                    return "";
            }
        }
        return $content;
    }

    /**
     * @param String $content
     */
    public function getImages(&$content = '') {
        $rule = $this->rule;
        $imageRules = $rule['target']['img'];
        // $featureImgRule = $imageRules['feature_img'];
        // $featureImgMatches = $this->getMatch($content, $featureImgRule, true);
        $innerImgMatches = $this->getMatch($content, $imageRules['inner_img'], true);
        $backgroundImgMatches = $this->getMatch($content, $imageRules['background_img'], true);
        

        $innerImgSrcArr = array();
        switch($imageRules['inner_img']['type']) {
            case 1:
                $innerImgSrcArr[] = $innerImgMatches;
                break;
            case 2:
                foreach($innerImgMatches as $match) {
                    $src = $match->getAttribute($imageRules['inner_img']['src']);

                    $scheme = parse_url($this->targetUrl, PHP_URL_SCHEME);
                    if (stristr($src, 'http://') || stristr($src, 'https://')) {

                    } else {
                        $src = str_replace(array('//'), array($scheme . '://'), $src);
                    }
                    if ($src) {
                        $innerImgSrcArr[] = $src;
                    }
                }    
                break;    
        }
        $backgroundImgSrcArr = array();
        switch($imageRules['background_img']['type']) {
            case 1:
                $backgroundImgSrcArr[] = $backgroundImgMatches;
                break;
            case 2:
                foreach($backgroundImgMatches as $match) {
                    $src = $match->getAttribute($imageRules['background_img']['src']);

                    $scheme = parse_url($this->targetUrl, PHP_URL_SCHEME);
                    if (stristr($src, 'http://') || stristr($src, 'https://')) {

                    } else {
                        $src = str_replace(array('//'), array($scheme . '://'), $src);
                    }

                    if ($src) {
                        $backgroundImgSrcArr[] = $src;
                    }
                }    
                break;    
        }
        $images = array(
            'inner_img' => array_merge($innerImgSrcArr, $backgroundImgSrcArr)
        );

        return $images;
    }

    /**
     * @param String $content
     * @param Array $rule
     * @return Mix|Array|String
     */
    public function getMatch(&$content = '', $rule = array(), $isImage = false) {
        $match = '';
        switch($rule['type']) {
            case 1:
                $isMatch = preg_match($rule['rule'], $content, $matches);                
                if ($isMatch) {
                    $match = $matches[1];
                }
                break; 
            case 2:
                $htmlDOM = str_get_html($content);
                if ($htmlDOM) {
                    if ($isImage) {
                        $matches = $htmlDOM->find($rule['rule']);
                        if (isset($rule['src'])) {
                            foreach($matches as $match) {
                                $src = $match->getAttribute($rule['src']);
                                // remove srcset
                                $match->removeAttribute('srcset');
                                $match->removeAttribute('style');
                                if ($src) {
                                    $match->setAttribute('src', $src);
                                }
                                // 改变 html 内容，使得 img 包含 src 属性
                                $content = $htmlDOM->innertext;
                            }    
                        }
                        $match = $matches;
                        $content = $htmlDOM->innertext;
                    } else {
                        $match = trim($htmlDOM->find($rule['rule'], 0)->innertext);
                    }
                } else {
                    $match = array();
                }
                break; 
            default:    
                break;
        }
        return $match;
    }
    /**
     * 
     */
    public static function uploadImage($data = array()) {
		$url = str_replace('&amp;', '&', $data['img_src']);
		$urlFileName = basename(parse_url($url, PHP_URL_PATH));
		$tmpfname = wp_tempnam($urlFileName);
        $config = require_once(__DIR__ . '/../Config/crawler.php');
        $useragent = "";
        if (isset($config['ua']) && is_array($config['ua'])) {
            $useragent = array_rand($config['ua']);
        }
		$response = wp_safe_remote_get($url, array(
            'blocking' => false,
            'timeout' => 300,
            'redirection' => 5,
			'stream' => true,
			'filename' => $tmpfname,
            'user-agent' => $useragent,	
            'headers' => array(
				'referer' => $data['referer']
			),
		));
		if ( is_wp_error( $response ) ) {
			unlink( $tmpfname );
            return array(
                'tmp_file' => null,
                'error' => $response->get_error_message()
            );
		}

		if ( 200 != wp_remote_retrieve_response_code( $response ) ){
            unlink( $tmpfname );
            return array(
                'tmp_file' => null,
                'error' => wp_remote_retrieve_response_code( $response )
            );
            return null;
		}

		$content_md5 = wp_remote_retrieve_header( $response, 'content-md5' );
		if ( $content_md5 ) {
			$md5_check = verify_file_md5( $tmpfname, $content_md5 );
			if ( is_wp_error( $md5_check ) ) {
                unlink( $tmpfname );
                return array(
                    'tmp_file' => null,
                    'error' => 'md5 fail'
                );
			}
		}

        return array(
            'tmp_file' => $tmpfname
        );
    }
}