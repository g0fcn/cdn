<?php
/*
Plugin Name: 星宿UI多端小程序
Plugin URI: https://www.frbkw.com
Description: 为星宿UI提供简化操作
Version: 1.2.0
Author: yinfengrui（枫瑞博客网）
Author URI: https://www.frbkw.com
License: A "Slug" license name e.g. GPL2
*/

//添加特色图片api
add_filter( 'rest_prepare_post', 'my_rest_prepare_post', 10, 3 );
function my_rest_prepare_post( $data, $post, $request ) {
$_data = $data->data;
if ( has_post_thumbnail() ) {
$thumbnail_id = get_post_thumbnail_id( $_data['id'] );
$thumbnail = wp_get_attachment_image_src( $thumbnail_id , 'thumbnail' );
$thumbnailurl = $thumbnail[0];
$featuredimgurl = $featuredimg[0];
if( ! empty($thumbnailurl)){
$_data['thumbnailurl'] = $thumbnailurl;
}
}else{
$_data['thumbnailurl'] = null;
}
$data->data = $_data;
return $data;
}

// 评论
 function filter_rest_allow_anonymous_comments(){
     return true;
}
add_filter('rest_allow_anonymous_comments','filter_rest_allow_anonymous_comments');



// 只搜索文章标题
function wpse_11826_search_by_title( $search, $wp_query ) {
    if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
        global $wpdb;
        $q = $wp_query->query_vars;
        $n = ! empty( $q['exact'] ) ? '' : '%';
        $search = array();
        foreach ( ( array ) $q['search_terms'] as $term )
            $search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );
        if ( ! is_user_logged_in() )
            $search[] = "$wpdb->posts.post_password = ''";
        $search = ' AND ' . implode( ' AND ', $search );
    }
    return $search;
}
add_filter( 'posts_search', 'wpse_11826_search_by_title', 10, 2 );

// 文章第一张图片为特色图片
function autoset_featured_image(){
global $post;
$already_has_thumb = has_post_thumbnail($post->ID);
if (!$already_has_thumb){
$attached_image = get_children("post_parent=$post->ID&post_type=attachment&post_mime_type=image&numberposts=1");
if ($attached_image){
foreach ($attached_image as $attachment_id => $attachment) {
set_post_thumbnail($post->ID, $attachment_id);
}
}
}
}
add_action('the_post', 'autoset_featured_image');
add_action('save_post', 'autoset_featured_image');
add_action('draft_to_publish', 'autoset_featured_image');
add_action('new_to_publish', 'autoset_featured_image');
add_action('pending_to_publish', 'autoset_featured_image');
add_action('future_to_publish', 'autoset_featured_image');




/*
名称: 腾讯视频短代码
作者博客： https://www.xintheme.com/xin-plugins/72733.html
作者：大胡子

*/

function dahuzi_qq_video($atts, $content=null) {

    extract(shortcode_atts(array("vids" => ''), $atts));

    $url = 'https://vv.video.qq.com/getinfo?vids='.$vids.'&platform=101001&charge=0&otype=json';
    $json = file_get_contents($url);
    preg_match('/^QZOutputJson=(.*?);$/',$json,$json2);
    $tempStr = json_decode($json2[1],true);
    $vurl = 'https://ugcws.video.gtimg.com/'.$tempStr['vl']['vi'][0]['fn']."?vkey=".$tempStr['vl']['vi'][0]['fvkey'];

  	$video = '<video style="width: 100%;" controls src="'.$vurl.'" poster="https://puui.qpic.cn/qqvideo_ori/0/'.$vids.'_496_280/0"></video>';
  
    return $video;
}
add_shortcode('qq-video', 'dahuzi_qq_video');

//新增自定义修改文章API日期时间格式
add_action('rest_api_init', function() {
    register_rest_field(
        array('post'),
        'en_md_date',
        array(
            'get_callback'    => function() {
                return get_the_modified_date() .' - '. get_the_modified_time();
            },
            'update_callback' => null,
            'schema'          => null,
        )
    );
});







?>