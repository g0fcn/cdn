<?php
namespace Bee\Beebox\Utils;

use Qiniu\Auth as QNAuth;
use Qiniu\Config as QNConfig;
use Qiniu\Storage\UploadManager as QNUploadManager;
use Qiniu\Storage\BucketManager as QNBucketManager;

class CDNCore {
    // get config 
    /**
     * @param $target 
     */
    public static function updateConfig($target, $data) {
        switch($target) {
            case 'qiniu':
                update_option('beebox_qn_cdn_state', $data['qn_cdn_state']);
                update_option('beebox_qn_access_key', $data['qn_access_key']);
                update_option('beebox_qn_secret_key', $data['qn_secret_key']);
                update_option('beebox_qn_domain_name', $data['qn_domain_name']);
                update_option('beebox_qn_bucket_name', $data['qn_bucket_name']);
                break;
        }
    }


    /**
     * @param $target
     */
    public static function getConfig($target) {
        $data = array();
        switch($target) {
            case 'qiniu':
                $data = array(
                    'qn_cdn_state' => get_option('beebox_qn_cdn_state', 'off'),
                    'qn_access_key' => get_option('beebox_qn_access_key', ''),
                    'qn_secret_key' => get_option('beebox_qn_secret_key', ''),
                    'qn_domain_name' => get_option('beebox_qn_domain_name', ''),
                    'qn_bucket_name' => get_option('beebox_qn_bucket_name', ''),
                );
               break;
        }
        return $data;
    }

    /**
     * 
     */
    public static function getImageAmount() {
        $queryArgs = array(
            'post_type' => 'attachment',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'post_mime_type' => array('image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/tiff', 'image/x-icon'),
            'fields' => 'ids'
        );
        $result = new \WP_Query($queryArgs);
        return count($result->posts);
    }

    /**
     * 
     */
    public static function syncImage($target, $args) {
        switch($target) {
            case 'qiniu':
                return self::qiniuSyncImage($args);
            break;
        }
    }

    /**
     * 
     */
    public static function getQNUploadMgr() {
        $uploadedMgr = new QNUploadManager();
        return $uploadedMgr;
    }

    /**
     * 
     */
    public static function getQNToken() {
        $auth = self::getAuth();
        $config = self::getConfig('qiniu');
        $token = $auth->uploadToken($config['qn_bucket_name']);
        return $token;
    }

    /**
     * 
     */
    public static function getAuth() {
        $config = self::getConfig('qiniu');
        $auth = new QNAuth($config['qn_access_key'], $config['qn_secret_key']); 
        return $auth;
    }

    /**
     * 
     */
    public static function qiniuSyncImage($args) {
        $token = self::getQNToken();
		$uploadedMgr = new QNUploadManager();
        $queryArgs = array(
            'offset' => $args['offset'],
            'posts_per_page' => $args['size'],
            'post_type' => 'attachment',
            'post_status' => 'any',
            'post_mime_type' => array('image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/tiff', 'image/x-icon'),
        );
        $result = new \WP_Query($queryArgs);
        $images = $result->posts;
        $resData = array();
        foreach($images as $image) {
            // 需上传所有尺寸的图片
            $imageId = $image->ID;
            $allImages = self::getAllSizeImages($imageId);
            $msg = array();
            $wpUploadDir = wp_upload_dir();
            $upUploadDirBase = $wpUploadDir['basedir'];
            foreach($allImages as $image) {
                $filePath = $upUploadDirBase . '/' . $image['filename'];
                $msg[] = $uploadedMgr->putFile($token, $image['filename'], $filePath);
            }
            $resData[] = array(
                'image_id' => $imageId,
                'msg' => $msg
            );
        }
        return $resData;
    }

	public static function getIMageData($id, $size)
	{
        $fileUrl = wp_get_attachment_image_src($id, $size);
        $fileKey = str_replace(home_url('/'), '', $fileUrl[0]);
        $meta = wp_get_attachment_metadata($id);
        $filename = $meta['file'];
		return array(
            'fileUrl' => $fileUrl[0],
            'filename' => $filename,
            'fileKey' => $fileKey,
		);
    }
    
    public static function syncSingleImage($meta) {
        $token = self::getQNToken();
        $uploadedMgr = new QNUploadManager();

        $images = array();
        $images[] = array(
            'filename' => $meta['file'],
            'filekey' => $meta['file']
        );

        foreach($meta['sizes'] as $subimage) {
            $images[] = array(
                'filename' => dirname($meta['file']) . '/' . $subimage['file'],
                'fikekey' => $subimage['file']
            );
        }
    
        $msg = array();
        $wpUploadDir = wp_upload_dir();
        $upUploadDirBase = $wpUploadDir['basedir'];
        foreach($images as $image) {
            $filePath = $upUploadDirBase . '/' . $image['filename'];
            $msg[] = $uploadedMgr->putFile($token, $image['filename'], $filePath);
        }
        return $msg;
    }

    public static function getAllSizeImages($id) {
        $meta = wp_get_attachment_metadata($id);
        $allImages = array();
        $allImages[] = array(
            'filename' => $meta['file'],
            'filekey' => $meta['file']
        );
        if (is_array($meta['sizes'])) {
            foreach($meta['sizes'] as $subimage) {
                $allImages[] = array(
                    'filename' => dirname($meta['file']) . '/' . $subimage['file'],
                    'fikekey' => $subimage['file']
                );
            }
        }
        return $allImages;
    }

    /**
     * 
     */
    public static function getBucketMgr() {
        $auth = self::getAuth();
        $qnConfig = new QNConfig();
        $bucketMgr = new QNBucketManager($auth, $qnConfig);
        return $bucketMgr;
    }

    public static function deleteImage($id) {
        $bucket = get_option('beebox_qn_bucket_name', '');
        $images = self::getAllSizeImages($id);
        $bucketMgr = self::getBucketMgr();
        foreach($images as $image) {
            $filekey = $image['filename'];
            $bucketMgr->delete($bucket, $filekey);
        }
    }
}