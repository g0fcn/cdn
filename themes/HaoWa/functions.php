<?php
//支持外链缩略图
if ( function_exists('add_theme_support') )
add_theme_support('post-thumbnails');
function catch_first_image() {global $post, $posts;$first_img = '';
ob_start();
ob_end_clean();
$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
$first_img = $matches [1] [0];
if(empty($first_img)){
$random = mt_rand(1, 10);
echo get_bloginfo ( 'stylesheet_directory' );
echo '/images/random/'.$random.'.jpg';
}
return $first_img;
}
;
//模板选择
include('functions-Category.php');

//小工具
include(TEMPLATEPATH . '/widget/loo-imglist.php');

//添加文章形式
add_theme_support( 'post-formats', array( 'aside' ) );

//wordpress 禁止摘要自动添加分段标签p
remove_filter( 'the_excerpt', 'wpautop' );


//菜单函数
if(function_exists('register_nav_menus')){  
  
register_nav_menus(  
array(  
'top-menu' => __( '顶部导航' ), 
'header-menu' => __( '网站导航' ),  

)  
);  
}  
// 导入bootstrap菜单样式
include_once('includes/wp_bootstrap_navwalker.php');

//title标题 From wazhuti.com

function _title() {
	if (is_category()) {  
  if( cs_get_option( 'i_catseo_u' ) ){
	 $title = the_wa_title(); 
}else{ echo ($title) ? ''.$title.'' : single_cat_title(); 
 }
 echo '  - ';
 }
elseif (function_exists('is_tag') && is_tag()) {
   single_tag_title('"'); echo '"相关的内容列表 - ';
   }
    elseif (is_archive()) {
   wp_title(''); echo ' Archive - ';
 } elseif (is_search()) {
   echo 'Search for "'.wp_specialchars($s).'" - ';
 } elseif (!(is_404()) && (is_single()) || (is_page())) {
   wp_title(''); echo ' - ';
 } elseif (is_404()) {
   echo 'Not Found - ';
 }
if (is_home()) {
   bloginfo('name'); echo ' - '; bloginfo('description');
 } else {
   bloginfo('name');
}
   if ($paged > 1) {
   echo ' - page '. $paged;
 }
 }

//自定义wordpress分类标题 From wazhuti.com
class wa_wp_title{
    function __construct(){
    // 分类
        add_action( 'category_add_form_fields', array( $this, 'add_tax_title_field' ) );
        add_action( 'category_edit_form_fields', array( $this, 'edit_tax_title_field' ) );
        add_action( 'edited_category', array( $this, 'save_tax_meta' ), 10, 2 );
        add_action( 'create_category', array( $this, 'save_tax_meta' ), 10, 2 );
    /* 标签*/
        add_action( 'post_tag_add_form_fields', array( $this, 'add_tax_title_field' ) );
       add_action( 'post_tag_edit_form_fields', array( $this, 'edit_tax_title_field' ) );
        add_action( 'edited_post_tag', array( $this, 'save_tax_meta' ), 10, 2 );
       add_action( 'create_post_tag', array( $this, 'save_tax_meta' ), 10, 2 );
    }
    public function add_tax_title_field(){
?>
        <div class="form-field term-title-wrap">
            <label for="term_meta[tax_wa_title]">SEO标题</label>
            <input type="text" name="term_meta[tax_wa_title]" id="term_meta[tax_wa_title]" value="" />
            <p class="description">搜索引擎优化自定义标题，不填写即为默认标题</p>
        </div>
<?php
    } // add_tax_title_field
    public function edit_tax_title_field( $term ){
        $term_id = $term->term_id;
        $term_meta = get_option( "wa_taxonomy_$term_id" );
        $wa_title = $term_meta['tax_wa_title'] ? $term_meta['tax_wa_title'] : '';
?>
        <tr class="form-field term-title-wrap">
            <th scope="row">
                <label for="term_meta[tax_wa_title]">SEO标题</label>
                <td>
                    <input type="text" name="term_meta[tax_wa_title]" id="term_meta[tax_wa_title]" value="<?php echo $wa_title; ?>" />
                    <p class="description">搜索引擎优化自定义标题，不填写即为默认标题</p>
                </td>
            </th>
        </tr>
<?php
    } // edit_tax_title_field
    public function save_tax_meta( $term_id ){
        if ( isset( $_POST['term_meta'] ) ) {
            $t_id = $term_id;
            $term_meta = array();
            $term_meta['tax_wa_title'] = isset ( $_POST['term_meta']['tax_wa_title'] ) ? $_POST['term_meta']['tax_wa_title'] : '';
            update_option( "wa_taxonomy_$t_id", $term_meta );
        } // if isset( $_POST['term_meta'] )
    } // save_tax_meta
} // wa_wp_title
$wptt_tax_title = new wa_wp_title();
function the_wa_title() {
    $category = get_the_category();
    $term_id = $category[0]->cat_ID;
    $term_meta = get_option( "wa_taxonomy_$term_id" );
    $tax_wa_title = $term_meta['tax_wa_title'] ? $term_meta['tax_wa_title'] : '';
    echo $tax_wa_title;
}
function get_current_tag_id() {
    $current_tag = single_tag_title('', false);
    $tags = get_tags();
    foreach($tags as $tag) {
        if($tag->name == $current_tag) return $tag->term_id;
    }
}
function wa_tag_title() {
    $term_id = get_current_tag_id();
    $term_meta = get_option( "wa_taxonomy_$term_id" );
    $wa_tag_title = $term_meta['tax_wa_title'] ? $term_meta['tax_wa_title'] : '';
    echo $wa_tag_title;
}


/**
    *自动添加图片 alt 和 title 属性  From wazhuti.com
*/
function image_alttitle( $imgalttitle ){
        global $post;
        $category = get_the_category();
        $flname=$category[0]->cat_name;
        $btitle = get_bloginfo();
        $imgtitle = $post->post_title;
        $imgUrl = "<img\s[^>]*src=(\"??)([^\" >]*?)\\1[^>]*>";
        if(preg_match_all("/$imgUrl/siU",$imgalttitle,$matches,PREG_SET_ORDER)){
                if( !empty($matches) ){
                        for ($i=0; $i < count($matches); $i++){
                                $tag = $url = $matches[$i][0];
                                $j=$i+1;
                                $judge = '/title=/';
                                preg_match($judge,$tag,$match,PREG_OFFSET_CAPTURE);
                                if( count($match) < 1 )
                                $altURL = ' alt="'.$imgtitle.' '.$flname.' 第'.$j.'张" title="'.$imgtitle.' '.$flname.' 第'.$j.'张-'.$btitle.'" ';
                                $url = rtrim($url,'>');
                                $url .= $altURL.'>';
                                $imgalttitle = str_replace($tag,$url,$imgalttitle);
                        }
                }
        }
        return $imgalttitle;
}
add_filter( 'the_content','image_alttitle');

//remove insert images attribute
//add_filter( 'the_content', 'fanly_remove_images_attribute', 99 );
add_filter( 'post_thumbnail_html', 'fanly_remove_images_attribute', 10 );
add_filter( 'image_send_to_editor', 'fanly_remove_images_attribute', 10 );
function fanly_remove_images_attribute( $html ) {
	//$html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
	$html = preg_replace( '/width="(\d*)"\s+height="(\d*)"\s+class=\"[^\"]*\"/', "", $html );
	$html = preg_replace( '/  /', "", $html );
	return $html;
}

/**
 * WordPress 无链接标签 From wazhuti.com
 */

function tagtext(){
	global $post;
	$gettags = get_the_tags($post->ID);
	if ($gettags) {
		foreach ($gettags as $tag) {
			$posttag[] = $tag->name;
		}
		$tags = implode( ',', $posttag );
		echo $tags;
	}
}

/**
 * 为WordPress后台的文章、分类等显示ID From wazhuti.com
 */
// 添加一个新的列 ID
function ssid_column($cols) {
	$cols['ssid'] = 'ID';
	return $cols;
}
 
// 显示 ID
function ssid_value($column_name, $id) {
	if ($column_name == 'ssid')
		echo $id;
}
 
function ssid_return_value($value, $column_name, $id) {
	if ($column_name == 'ssid')
		$value = $id;
	return $value;
}
 
// 为 ID 这列添加css 
function ssid_css() {
?>
<style type="text/css">
	#ssid { width: 50px; } /* Simply Show IDs */
</style>
<?php	
}
 
// 通过动作/过滤器输出各种表格和CSS
function ssid_add() {
	add_action('admin_head', 'ssid_css');
 
	add_filter('manage_posts_columns', 'ssid_column');
	add_action('manage_posts_custom_column', 'ssid_value', 10, 2);
 
	add_filter('manage_pages_columns', 'ssid_column');
	add_action('manage_pages_custom_column', 'ssid_value', 10, 2);
 
	add_filter('manage_media_columns', 'ssid_column');
	add_action('manage_media_custom_column', 'ssid_value', 10, 2);
 
	add_filter('manage_link-manager_columns', 'ssid_column');
	add_action('manage_link_custom_column', 'ssid_value', 10, 2);
 
	add_action('manage_edit-link-categories_columns', 'ssid_column');
	add_filter('manage_link_categories_custom_column', 'ssid_return_value', 10, 3);
 
	foreach ( get_taxonomies() as $taxonomy ) {
		add_action("manage_edit-${taxonomy}_columns", 'ssid_column');			
		add_filter("manage_${taxonomy}_custom_column", 'ssid_return_value', 10, 3);
	}
 
	add_action('manage_users_columns', 'ssid_column');
	add_filter('manage_users_custom_column', 'ssid_return_value', 10, 3);
 
	add_action('manage_edit-comments_columns', 'ssid_column');
	add_action('manage_comments_custom_column', 'ssid_value', 10, 2);
}
 
add_action('admin_init', 'ssid_add');




/**
 * WordPress 媒体库过滤不同类型的文件 From wazhuti.com  
 */
add_filter( 'post_mime_types', 'modify_post_mime_types' );
function modify_post_mime_types( $post_mime_types ) {
	// 添加查询 application 这个大类的文件
	$post_mime_types['application'] = array( __( '应用文件' ), __( '管理应用文件' ), _n_noop( '应用文件 <span class="count">(%s)</span>', '应用文件 <span class="count">(%s)</span>' ) );
	// 添加查询 application 大类下的 ZIP 文件
	$post_mime_types['application/zip'] = array( __( 'ZIP' ), __( '管理ZIP文件' ), _n_noop( 'ZIP <span class="count">(%s)</span>', 'ZIP <span class="count">(%s)</span>' ) );
	return $post_mime_types;
}


/**
 * WordPress 设置图片的默认显示方式（尺寸/对齐方式/链接到） From wazhuti.com
 */
add_action( 'after_setup_theme', 'default_attachment_display_settings' );
function default_attachment_display_settings() {
	update_option( 'image_default_align', 'left' );
	update_option( 'image_default_link_type', 'none' );
	update_option( 'image_default_size', 'full' );
}



/**
 * WordPress 去除后台标题中的"—— WordPress"  From wazhuti.com
 */
add_filter('admin_title', 'wpdx_custom_admin_title', 10, 2);
function wpdx_custom_admin_title($admin_title, $title){
    return $title.' &lsaquo; 欢迎使用挖主题'.get_bloginfo('name');
}

/**
 * WordPress 后台文章列表设置文章特色图片（缩略图）集成版  From wazhuti.com
 */
class doocii_Easy_Thumbnail_Switcher {
    
    public $add_new_str;
    public $change_str;
    public $remove_str;
    public $upload_title;
    public $upload_add;
    public $confirm_str;
    
    public function __construct() {
    
        $this->add_new_str = __( '添加');
        $this->change_str = __( '更改');
        $this->remove_str = __( '移除');
        $this->upload_title = __( '上传特色图片');
        $this->upload_add = __( '确定');
        $this->confirm_str = __( '你确定?');
        
        add_filter( 'manage_posts_columns', array( $this, 'add_column' ) );
        add_action( 'manage_posts_custom_column', array( $this, 'thumb_column' ), 10, 2 );
        add_action( 'admin_footer', array( $this, 'add_nonce' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
        
        add_action( 'wp_ajax_ts_ets_update', array( $this, 'update' ) );
        add_action( 'wp_ajax_ts_ets_remove', array( $this, 'remove' ) );
        
        add_image_size( 'ts-ets-thumb', 75, 75, array( 'center', 'center' ) );
        
    }
    
    /**
     * 安全检查
     */
    public function add_nonce() {
        
        global $pagenow;
        
        if( $pagenow !== 'edit.php' ) {
            return;
        }
        wp_nonce_field( 'ts_ets_nonce', 'ts_ets_nonce' );
        
    }
    
    /**
     * 加载脚本
     */
    public function scripts( $pagenow ) {
        
        if( $pagenow !== 'edit.php' ) {
            return;
        }
        
        wp_enqueue_media();
        wp_enqueue_script( 'doocii-ets-js', get_template_directory_uri() . '/js/script.js', array( 'jquery', 'media-upload', 'thickbox' ), '1.0', true );

        wp_localize_script( 'doocii-ets-js', 'ets_strings', array(
            'upload_title' => $this->upload_title,
            'upload_add' => $this->upload_add,
            'confirm' => $this->confirm_str,
        ) );
        
    }
    
    /**
     * The action which is added to the post row actions
     */
    public function add_column( $columns ) {
        
        $columns['ts-ets-option'] = __( '缩略图');
        return $columns;
        
    }
    
    /**
     * 显示列
     */
    public function thumb_column( $column, $id ) {
        
        switch( $column ) {
            case 'ts-ets-option':
                
                if( has_post_thumbnail() ) {
                    the_post_thumbnail( 'ts-ets-thumb' );
                    echo '<br>';
                    echo sprintf( '<button type="button" class="button-primary ts-ets-add" data-id="%s">%s</button>', esc_attr( $id ), $this->change_str );
                    echo sprintf( ' <button type="button" class="button-secondary ts-ets-remove" data-id="%s">%s</button>', esc_attr( $id ), $this->remove_str );
                } else {
                    echo sprintf( '<button type="button" class="button-primary ts-ets-add" data-id="%s">%s</button>', esc_attr( $id ), $this->add_new_str );
                }
                
                break;
        }
        
    }
    
    /**
     * AJAX保存更新缩略图
     */
    public function update() {
        
        // 检查是否所有需要的数据都设置与否
        if( !isset( $_POST['nonce'] ) || !isset( $_POST['post_id'] ) || !isset( $_POST['thumb_id'] ) ) {
            wp_die();
        }
        
        //验证
        if( !wp_verify_nonce( $_POST['nonce'], 'ts_ets_nonce' ) ) {
            wp_die();
        }
        
        $id = $_POST['post_id'];
        $thumb_id = $_POST['thumb_id'];
        
        set_post_thumbnail( $id, $thumb_id );
        
        echo wp_get_attachment_image( $thumb_id, 'ts-ets-thumb' );
        echo '<br>';
        echo sprintf( '<button type="button" class="button-primary ts-ets-add" data-id="%s">%s</button>', esc_attr( $id ), $this->change_str );
        echo sprintf( ' <button type="button" class="button-secondary ts-ets-remove" data-id="%s">%s</button>', esc_attr( $id ), $this->remove_str );
        
        wp_die();
        
    }
    
    /**
     * AJAX回调后删除缩略图
     */
    public function remove() {
        
        // Check if all required data are set or not
        if( !isset( $_POST['nonce'] ) || !isset( $_POST['post_id'] ) ) {
            wp_die();
        }
        
        // Verify nonce
        if( !wp_verify_nonce( $_POST['nonce'], 'ts_ets_nonce' ) ) {
            wp_die();
        }
        
        $id = $_POST['post_id'];
        
        delete_post_thumbnail( $id );
        
        echo sprintf( '<button type="button" class="button-primary ts-ets-add" data-id="%s">%s</button>', esc_attr( $id ), $this->add_new_str );
        
        wp_die();
        
    }
    
}

new doocii_Easy_Thumbnail_Switcher();




//彻底禁止WordPress缩略图  From wazhuti.com
add_filter( 'add_image_size', create_function( '', 'return 1;' ) );

//WordPress媒体库附件上传图片自动重命名方案 From wazhuti.com

//WordPress中文名、数字名图片上传自动重命名
add_filter('sanitize_file_name','fanly_custom_upload_name', 5, 1 );
function fanly_custom_upload_name($file){
	$info	= pathinfo($file);
	$ext	= empty($info['extension']) ? '' : '.' . $info['extension'];
	$name	= basename($file, $ext);
	if(preg_match("/[一-龥]/u",$file)){//中文换名
		$file	= substr(md5($name), 0, 20) . rand(00,99) . $ext;//截取前20位MD5长度，加上两位随机
	}elseif(is_numeric($name)){//数字换名
		$file	= substr(md5($name), 0, 20) . rand(00,99) . $ext;//截取前20位MD5长度，加上两位随机
	}
    return $file;
}



/*wordpress蜘蛛爬行记录 From wazhuti.com
http://网址/robotslogs.txt 即可看到蜘蛛爬行记录了！
*/
function get_naps_bot(){  
$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);  
if (strpos($useragent, 'googlebot') !== false){  
return 'Googlebot';  
}  
if (strpos($useragent, 'msnbot') !== false){  
return 'MSNbot';  
}  
if (strpos($useragent, 'slurp') !== false){  
return 'Yahoobot';  
}  
if (strpos($useragent, 'baiduspider') !== false){  
return 'Baiduspider';  
}  
if (strpos($useragent, 'sohu-search') !== false){  
return 'Sohubot';  
}  
if (strpos($useragent, 'lycos') !== false){  
return 'Lycos';  
}  
if (strpos($useragent, 'robozilla') !== false){  
return 'Robozilla';  
}  
return false;  
}  
function nowtime(){  
date_default_timezone_set('Asia/Shanghai');  
$date=date("Y-m-d.G:i:s");  
return $date;  
}  
$searchbot = get_naps_bot();  
if ($searchbot) {  
$tlc_thispage = addslashes($_SERVER['HTTP_USER_AGENT']);  
$url=$_SERVER['HTTP_REFERER'];  
$file="robots_log.txt";  
$time=nowtime();  
$data=fopen($file,"a");  
$PR="$_SERVER[REQUEST_URI]";  
fwrite($data,"Time:$time robot:$searchbot URL:$tlc_thispage\n page:$PR\r\n");  
fclose($data);  
}  


/**
 * WordPress 自动为文章添加已使用过的标签  From wazhuti.com
 */
add_action('save_post', 'auto_add_tags');
function auto_add_tags(){
	$tags = get_tags( array('hide_empty' => false) );
	$post_id = get_the_ID();
	$post_content = get_post($post_id)->post_content;
	if ($tags) {
		foreach ( $tags as $tag ) {
			// 如果文章内容出现了已使用过的标签，自动添加这些标签
			if ( strpos($post_content, $tag->name) !== false)
				wp_set_post_tags( $post_id, $tag->name, true );
		}
	}
}

// 自动为文章内的标签添加内链开始 From wazhuti.com
$match_num_from = 1;        //一篇文章中同一个标签少于几次不自动链接
$match_num_to = 1;      //一篇文章中同一个标签最多自动链接几次
function tag_sort($a, $b){
    if ( $a->name == $b->name ) return 0;
    return ( strlen($a->name) > strlen($b->name) ) ? -1 : 1;
}
function tag_link($content){
    global $match_num_from,$match_num_to;
        $posttags = get_the_tags();
        if ($posttags) {
            usort($posttags, "tag_sort");
            foreach($posttags as $tag) {
                $link = get_tag_link($tag->term_id);
                $keyword = $tag->name;
                $cleankeyword = stripslashes($keyword);
                $url = "<a href=\"$link\" title=\"".str_replace('%s',addcslashes($cleankeyword, '$'),__('【查看含有[%s]标签的文章】'))."\"";
                $url .= ' target="_blank"';
                $url .= ">".addcslashes($cleankeyword, '$')."</a>";
                $limit = rand($match_num_from,$match_num_to);
                $content = preg_replace( '|(<a[^>]+>)(.*)('.$ex_word.')(.*)(</a[^>]*>)|U'.$case, '$1$2%&&&&&%$4$5', $content);
                $content = preg_replace( '|(<img)(.*?)('.$ex_word.')(.*?)(>)|U'.$case, '$1$2%&&&&&%$4$5', $content);
                $cleankeyword = preg_quote($cleankeyword,'\'');
                $regEx = '\'(?!((<.*?)|(<a.*?)))('. $cleankeyword . ')(?!(([^<>]*?)>)|([^>]*?</a>))\'s' . $case;
                $content = preg_replace($regEx,$url,$content,$limit);
                $content = str_replace( '%&&&&&%', stripslashes($ex_word), $content);
            }
        }
    return $content;
}
add_filter('the_content','tag_link',1);
/* 自动为文章内的标签添加内链结束 */


//给文章图片自动添加alt和title信息  From wazhuti.com
add_filter('the_content', 'imagesalt');
function imagesalt($content) {
       global $post;
       $pattern ="/<a(.*?)href=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
       $replacement = '<a$1href=$2$3.$4$5 alt="'.$post->post_title.'" title="'.$post->post_title.'"$6>';
       $content = preg_replace($pattern, $replacement, $content);
       return $content;
}



//侧边栏
if (function_exists('register_sidebar'))
{
    register_sidebar(array(
	'name'=>'侧边栏',
	'description'   => '以下小工具在页面右边显示',
	'before_widget'=>'<div class="widget row">',
	'after_widget'=>'</div>',
	'before_title'=>'<h3>',
	'after_title'=>'</h3>',
	));


}
/*
add_action( 'widgets_init', 'my_unregister_widgets' );   
function my_unregister_widgets() {   
    unregister_widget( 'WP_Widget_Archives' );   
    unregister_widget( 'WP_Widget_Calendar' );   
    unregister_widget( 'WP_Widget_Categories' );   
    unregister_widget( 'WP_Widget_Meta' );   
    unregister_widget( 'WP_Widget_Recent_Comments' );   
    unregister_widget( 'WP_Widget_RSS' );   
    unregister_widget( 'WP_Widget_Search' );   
}  */






// 修改 WordPress 后台显示字体 From wazhuti.com
function admin_lettering(){
    echo'<style type="text/css">
     body{ font-family: Microsoft YaHei;}
    </style>';
    }
add_action('admin_head', 'admin_lettering');

//移除wp-embed.min.js

function disable_embeds_init() {
    /* @var WP $wp */
    global $wp;
 
    // Remove the embed query var.
    $wp->public_query_vars = array_diff( $wp->public_query_vars, array(
        'embed',
    ) );
 
    // Remove the REST API endpoint.
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );
 
    // Turn off
    add_filter( 'embed_oembed_discover', '__return_false' );
 
    // Don't filter oEmbed results.
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
 
    // Remove oEmbed discovery links.
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
 
    // Remove oEmbed-specific JavaScript from the front-end and back-end.
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
    add_filter( 'tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin' );
 
    // Remove all embeds rewrite rules.
    add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
}
 
add_action( 'init', 'disable_embeds_init', 9999 );
 
/**
 * Removes the 'wpembed' TinyMCE plugin.
 *
 * @since 1.0.0
 *
 * @param array $plugins List of TinyMCE plugins.
 * @return array The modified list.
 */
function disable_embeds_tiny_mce_plugin( $plugins ) {
    return array_diff( $plugins, array( 'wpembed' ) );
}
 
/**
 * Remove all rewrite rules related to embeds.
 *
 * @since 1.2.0
 *
 * @param array $rules WordPress rewrite rules.
 * @return array Rewrite rules without embeds rules.
 */
function disable_embeds_rewrites( $rules ) {
    foreach ( $rules as $rule => $rewrite ) {
        if ( false !== strpos( $rewrite, 'embed=true' ) ) {
            unset( $rules[ $rule ] );
        }
    }
 
    return $rules;
}
 
/**
 * Remove embeds rewrite rules on plugin activation.
 *
 * @since 1.2.0
 */
function disable_embeds_remove_rewrite_rules() {
    add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
    flush_rewrite_rules();
}
 
register_activation_hook( __FILE__, 'disable_embeds_remove_rewrite_rules' );
 
/**
 * Flush rewrite rules on plugin deactivation.
 *
 * @since 1.2.0
 */
function disable_embeds_flush_rewrite_rules() {
    remove_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
    flush_rewrite_rules();
}
 
register_deactivation_hook( __FILE__, 'disable_embeds_flush_rewrite_rules' );

// head code
add_action('wp_head', '_the_head');
function _the_head() {
	_the_keywords();
	_the_description();
	
      _the_head_css();
//	_the_head_code();
}

function _the_head_css() {
	$styles = '';

//灰白模式


	if (cs_get_option( 'checkbox_1', '' )) {
		$styles .= "html{overflow-y:scroll;filter:progid:DXImageTransform.Microsoft.BasicImage(grayscale=1);-webkit-filter: grayscale(100%);}";
	}

	

	$color = '';
	if (cs_get_option( 'i_favicon_icon', '' ) && cs_get_option( 'i_favicon_icon', '' ) !== '45B6F7') {
		$color = cs_get_option( 'i_favicon_icon', '' );
	}

	if (cs_get_option( 'color_picker_1', '' ) && cs_get_option( 'i_favicon_icon', '' ) !== '#45B6F7') {
		$color = substr(cs_get_option( 'color_picker_1', '' ), 1);
	}

	if ($color) {
		$styles .= '

	
		';
	}

	if ($styles) {
		echo '<style>' . $styles . '</style>';
	}
}


//禁止WordPress头部加载s.w.org  From wazhuti.com
remove_action( 'wp_head', 'wp_resource_hints', 2 );


/* 自定义css */
add_action('wp_footer', 'add_css', 99);
function add_css() {
    echo '<!-- 自定义css --><style>' . cs_get_option('i_css') . '</style>';
}

/* 自定义js */
add_action('wp_footer', 'add_js', 99);
function add_js() {
	
    echo '<!-- 自定义js --><script>' . cs_get_option('i_js') . '</script>';
}

/* 引入后台框架 */
require_once dirname(__FILE__) . '/cs-framework/cs-framework.php'; 

add_filter('manage_pages_columns', 'ws_manage_pages_columns_add_template');
function ws_manage_pages_columns_add_template($columns){
    $columns['template'] = '模板文件';
    return $columns;
}

add_action('manage_pages_custom_column','ws_manage_pages_custom_column_show_template',10,2);
function ws_manage_pages_custom_column_show_template($column_name,$id){
    if ($column_name == 'template') {
        echo get_page_template_slug();
    }
}

function enable_more_buttons($buttons) {   
     $buttons[] = 'newdocument';   
     $buttons[] = 'del';   
     $buttons[] = 'sub';   
     $buttons[] = 'sup';    
     $buttons[] = 'fontselect';   
     $buttons[] = 'fontsizeselect';   
     $buttons[] = 'cleanup';      
     $buttons[] = 'styleselect';   
     $buttons[] = 'wp_page';   
     $buttons[] = 'anchor';   
     $buttons[] = 'backcolor';   
     return $buttons;   
     }   
add_filter("mce_buttons_3", "enable_more_buttons");  


add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1);
add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1);
add_filter('page_css_class', 'my_css_attributes_filter', 100, 1);
function my_css_attributes_filter($var) {
   return is_array($var) ? array_intersect($var, array('menu-item','current-menu-item','current-post-parent','current-menu-parent')) : '';
}

/* 
 * delete google fonts
 * ====================================================
*/
// Remove Open Sans that WP adds from frontend
if (!function_exists('remove_wp_open_sans')) :
    function remove_wp_open_sans() {
        wp_deregister_style( 'open-sans' );
        wp_register_style( 'open-sans', false );
    }
    add_action('wp_enqueue_scripts', 'remove_wp_open_sans');
 
    // Uncomment below to remove from admin
    // add_action('admin_enqueue_scripts', 'remove_wp_open_sans');
endif;

function remove_open_sans() {    
    wp_deregister_style( 'open-sans' );    
    wp_register_style( 'open-sans', false );    
    wp_enqueue_style('open-sans','');    
}    
add_action( 'init', 'remove_open_sans' );


// no self Pingback
add_action('pre_ping', '_noself_ping');
function _noself_ping(&$links) {
	$home = get_option('home');
	foreach ($links as $l => $link) {
		if (0 === strpos($link, $home)) {
			unset($links[$l]);
		}
	}
}

// delete wp_head code
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'wp_generator');


/**
 * Disable the emoji's
 */
 function disable_emojis() {
 remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
 remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
 remove_action( 'wp_print_styles', 'print_emoji_styles' );
 remove_action( 'admin_print_styles', 'print_emoji_styles' );
 remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
 remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
 remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
 add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
 }
 add_action( 'init', 'disable_emojis' );
/**
 * Filter function used to remove the tinymce emoji plugin.
 */
 function disable_emojis_tinymce( $plugins ) {
 if ( is_array( $plugins ) ) {
 return array_diff( $plugins, array( 'wpemoji' ) );
 } else {
 return array();
 }
 }


//禁用REST API
add_filter('rest_enabled', '__return_false');
add_filter('rest_jsonp_enabled', '__return_false');
 
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );

//关键字
function _the_keywords() {
	global $new_keywords;
	if( $new_keywords ) {
		echo "<meta name=\"keywords\" content=\"{$new_keywords}\">\n";
		return;
	}

	global $s, $post;
	$keywords = '';
	if (is_singular()) {
		if (get_the_tags($post->ID)) {
			foreach (get_the_tags($post->ID) as $tag) {
				$keywords .= $tag->name . ', ';
			}
		}
		foreach (get_the_category($post->ID) as $category) {
			$keywords .= $category->cat_name . ', ';
		}
		$keywords = substr_replace($keywords, '', -2);
		$the = trim(get_post_meta($post->ID, 'keywords', true));
		if ($the) {
			$keywords = $the;
		}
	} elseif (is_home()) {
	$keywords = cs_get_option('dawa_keywords','');
	} elseif (is_tag()) {
		$keywords = single_tag_title('', false);
	} elseif (is_category()) {

		global $wp_query; 
		$cat_ID = get_query_var('cat');
		$keywords = _get_tax_meta($cat_ID, 'keywords');
		if( !$keywords ){
			$keywords = single_cat_title('', false);
		}
	
	} elseif (is_search()) {
		$keywords = esc_html($s, 1);
	} else {
		$keywords = trim(wp_title('', false));
	}
	if ($keywords) {
		echo "<meta name=\"keywords\" content=\"{$keywords}\">\n";
	}
}

//网站描述
function _the_description() {
	global $new_description;
	if( $new_description ){
		echo "<meta name=\"description\" content=\"$new_description\">\n";
		return;
	}

	global $s, $post;
	$description = '';
	$blog_name = get_bloginfo('name');
	if (is_singular()) {
		if (!empty($post->post_excerpt)) {
			$text = $post->post_excerpt;
		} else {
			$text = $post->post_content;
		}
		$description = trim(str_replace(array("\r\n", "\r", "\n", "　", " "), " ", str_replace("\"", "'", strip_tags($text))));
		$description = mb_substr($description, 0, 200, 'utf-8');

		if (!$description) {
			$description = $blog_name . "-" . trim(wp_title('', false));
		}

		$the = trim(get_post_meta($post->ID, 'description', true));
		if ($the) {
			$description = $the;
		}
		
	} elseif (is_home()) {
	$description = cs_get_option('dawa_description','');
	} elseif (is_tag()) {
		$description = trim(strip_tags(tag_description()));
	} elseif (is_category()) {

		global $wp_query; 
		$cat_ID = get_query_var('cat');
		$description = _get_tax_meta($cat_ID, 'description');
		if( !$description ){
			$description = trim(strip_tags(category_description()));
		}

	} elseif (is_archive()) {
		$description = $blog_name . "'" . trim(wp_title('', false)) . "'";
	} elseif (is_search()) {
		$description = $blog_name . ": '" . esc_html($s, 1) . "' 的搜索結果";
	} else {
		$description = $blog_name . "'" . trim(wp_title('', false)) . "'";
	}
	
	echo "<meta name=\"description\" content=\"$description\">\n";
}

/* 
 * post meta keywords
 * ====================================================
*/
$postmeta_keywords_description = array(
    
  
    array(
        "name" => "keywords",
        "std" => "",
        "title" => __('关键字', 'haoui').'：'
    ),
    array(
        "name" => "description",
        "std" => "",
        "title" => __('描述', 'haoui').'：'
        )
);

    add_action('admin_menu', '_postmeta_keywords_description_create');
    add_action('save_post', '_postmeta_keywords_description_save');


function _postmeta_keywords_description() {
    global $post, $postmeta_keywords_description;
    foreach($postmeta_keywords_description as $meta_box) {
        $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
        if($meta_box_value == "")
            $meta_box_value = $meta_box['std'];
        echo'<p>'.$meta_box['title'].'</p>';
        if( $meta_box['name'] == 'description' ){
            echo '<p><textarea style="width:98%" name="'.$meta_box['name'].'">'.$meta_box_value.'</textarea></p>';
        }else{
            echo '<p><input type="text" style="width:98%" value="'.$meta_box_value.'" name="'.$meta_box['name'].'"></p>';
        }
    }
   
    echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
}

function _postmeta_keywords_description_create() {
    global $theme_name;
    if ( function_exists('add_meta_box') ) {
        add_meta_box( 'postmeta_keywords_description_boxes', __('SEO设置', 'haoui'), '_postmeta_keywords_description', 'post', 'normal', 'high' );
        add_meta_box( 'postmeta_keywords_description_boxes', __('SEO设置', 'haoui'), '_postmeta_keywords_description', 'page', 'normal', 'high' );
    }
}

function _postmeta_keywords_description_save( $post_id ) {
    global $postmeta_keywords_description;
   
    if ( !wp_verify_nonce( isset($_POST['post_newmetaboxes_noncename'])?$_POST['post_newmetaboxes_noncename']:'', plugin_basename(__FILE__) ))
        return;
   
    if ( !current_user_can( 'edit_posts', $post_id ))
        return;
                   
    foreach($postmeta_keywords_description as $meta_box) {
        $data = $_POST[$meta_box['name']];
        if(get_post_meta($post_id, $meta_box['name']) == "")
            add_post_meta($post_id, $meta_box['name'], $data, true);
        elseif($data != get_post_meta($post_id, $meta_box['name'], true))
            update_post_meta($post_id, $meta_box['name'], $data);
        elseif($data == "")
            delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
    }
}

/**
 * post formats
 */
/*add_theme_support( 'post-formats', array(  'image') );  
add_post_type_support( 'page', 'post-formats' );
*/
//cat template
include( 'includes/metabox.php' );

//postviews   
function get_post_views ($post_id) {   
  
    $count_key = 'views';   
    $count = get_post_meta($post_id, $count_key, true);   
  
    if ($count == '') {   
        delete_post_meta($post_id, $count_key);   
        add_post_meta($post_id, $count_key, '0');   
        $count = '0';   
    }   
  
    echo number_format_i18n($count);   
  
}   
  
function set_post_views () {   
  
    global $post;   
  
    $post_id = $post -> ID;   
    $count_key = 'views';   
    $count = get_post_meta($post_id, $count_key, true);   
  
    if (is_single() || is_page()) {   
  
        if ($count == '') {   
            delete_post_meta($post_id, $count_key);   
            add_post_meta($post_id, $count_key, '0');   
        } else {   
            update_post_meta($post_id, $count_key, $count + 1);   
        }   
  
    }   
  
}   
add_action('get_header', 'set_post_views');  


/**修改后台底部左侧版权 */
function modify_footer_admin () {
	echo 'Design By <a href="http://www.wazhuti.com" target="_blank">DAWA</a>';
	echo '     ';
	echo 'Powered By <a href="http://WordPress.org" target="_blank">WordPress</a>';
}
add_filter('admin_footer_text', 'modify_footer_admin');

/**移除后台顶部左上角图标*/
function annointed_admin_bar_remove() {
        global $wp_admin_bar;
        /* Remove their stuff */
        $wp_admin_bar->remove_menu('wp-logo');
}
add_action('wp_before_admin_bar_render', 'annointed_admin_bar_remove', 0);


//自动生成版权时间
function comicpress_copyright() {
global $wpdb;
$copyright_dates = $wpdb->get_results("
    SELECT
    YEAR(min(post_date_gmt)) AS firstdate,
    YEAR(max(post_date_gmt)) AS lastdate
    FROM
    $wpdb->posts
    WHERE
    post_status = 'publish'
    ");
$output = '';
if($copyright_dates) {
$copyright = '&copy; '.$copyright_dates[0]->firstdate;
if($copyright_dates[0]->firstdate != $copyright_dates[0]->lastdate) {
$copyright .= '-'.$copyright_dates[0]->lastdate;
}
$output = $copyright;
}
return $output;
}

// 分页代码
function par_pagenavi($range = 3){
    global $paged, $wp_query;
    if ( !$max_page ) {$max_page = $wp_query->max_num_pages;}
    if($max_page > 1){if(!$paged){$paged = 1;}
    if($paged != 1){echo "<a href='" . get_pagenum_link(1) . "' class='extend' title='跳转到首页'>«</a>";}
    if($max_page > $range){
        if($paged < $range){for($i = 1; $i <= ($range + 1); $i++){echo "<a href='" . get_pagenum_link($i) ."'";
        if($i==$paged)echo " class='current'";echo ">$i</a>";}}
    elseif($paged >= ($max_page - ceil(($range/2)))){
        for($i = $max_page - $range; $i <= $max_page; $i++){echo "<a href='" . get_pagenum_link($i) ."'";
        if($i==$paged)echo " class='current'";echo ">$i</a>";}}
    elseif($paged >= $range && $paged < ($max_page - ceil(($range/2)))){
        for($i = ($paged - ceil($range/2)); $i <= ($paged + ceil(($range/2))); $i++){echo "<a href='" . get_pagenum_link($i) ."'";if($i==$paged) echo " class='current'";echo ">$i</a>";}}}
    else{for($i = 1; $i <= $max_page; $i++){echo "<a href='" . get_pagenum_link($i) ."'";
    if($i==$paged)echo " class='current'";echo ">$i</a>";}}
    next_posts_link(' »');
}
}

//侧边栏分类
function get_category_root_id($cat)  
{  
$this_category = get_category($cat); // 取得当前分类  
while($this_category->category_parent) // 若当前分类有上级分类时，循环  
{  
$this_category = get_category($this_category->category_parent); // 将当前分类设为上级分类（往上爬）  
}  
return $this_category->term_id; // 返回根分类的id号  
}



include('includes/breadcrumbs.php');//面包屑

//屏蔽顶部
 add_filter( 'show_admin_bar', '__return_false' );

//链接
add_filter('pre_option_link_manager_enabled','__return_true');

remove_action('wp_head', 'wp_generator' ); //去除版本信息
remove_action('wp_head', 'wlwmanifest_link' );
remove_action('wp_head', 'rsd_link' );//清除离线编辑器接口
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );//清除前后文信息
remove_action('wp_head', 'feed_links',2 );
remove_action('wp_head', 'feed_links_extra',3 );//清除feed信息
remove_action('wp_head', 'wp_shortlink_wp_head',10,0 );

//添加特色缩略图支持
if ( function_exists('add_theme_support') )add_theme_support('post-thumbnails');

//输出缩略图地址
function post_thumbnail_src(){
	global $post;
	if( $values = get_post_custom_values("thumb") ) {	//输出自定义域图片地址
		$values = get_post_custom_values("thumb");
		$post_thumbnail_src = $values [0];
	} elseif( has_post_thumbnail() ){    //如果有特色缩略图，则输出缩略图地址
		$thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID),'full');
		$post_thumbnail_src = $thumbnail_src [0];
	} else {
		$post_thumbnail_src = '';
		ob_start();
		ob_end_clean();
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
		if(!empty($matches[1][0])){
			$post_thumbnail_src = $matches[1][0];   //获取该图片 src
		}else{	//如果日志中没有图片，则显示随机图片
			$random = mt_rand(1, 1);
			$post_thumbnail_src = get_template_directory_uri().'/images/random/'.$random.'.jpg';
			//如果日志中没有图片，则显示默认图片
			//$post_thumbnail_src = get_template_directory_uri().'/images/default_thumb.jpg';
		}
	};
	echo $post_thumbnail_src;
} 




// print_r( _get_tax_meta(21, 'style') );

function _get_tax_meta($id=0, $field=''){
    $ops = get_option( "_taxonomy_meta_$id" );

    if( empty($ops) ){
        return '';
    }

    if( empty($field) ){
        return $ops;
    }

    return isset($ops[$field]) ? $ops[$field] : '';
}

class __Tax_Cat{

    function __construct(){
        add_action( 'category_add_form_fields', array( $this, 'add_tax_field' ) );
        add_action( 'category_edit_form_fields', array( $this, 'edit_tax_field' ) );

        add_action( 'edited_category', array( $this, 'save_tax_meta' ), 10, 2 );
        add_action( 'create_category', array( $this, 'save_tax_meta' ), 10, 2 );
    }
 
    public function add_tax_field(){
        echo '
           
            <div class="form-field">
                <label for="term_meta[keywords]">SEO 关键字（keywords）</label>
                <input type="text" name="term_meta[keywords]" id="term_meta[keywords]" />
            </div>
            <div class="form-field">
                <label for="term_meta[keywords]">SEO 描述（description）</label>
                <textarea name="term_meta[description]" id="term_meta[description]" rows="4" cols="40"></textarea>
            </div>
        ';
    }
 
    public function edit_tax_field( $term ){

        $term_id = $term->term_id;
        $term_meta = get_option( "_taxonomy_meta_$term_id" );

        $meta_title = isset($term_meta['title']) ? $term_meta['title'] : '';
        $meta_keywords = isset($term_meta['keywords']) ? $term_meta['keywords'] : '';
        $meta_description = isset($term_meta['description']) ? $term_meta['description'] : '';
        
        echo '
           
            <tr class="form-field">
                <th scope="row">
                    <label for="term_meta[keywords]">SEO 关键字（keywords）</label>
                    <td>
                        <input type="text" name="term_meta[keywords]" id="term_meta[keywords]" value="'. $meta_keywords .'" />
                    </td>
                </th>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="term_meta[description]">SEO 描述（description）</label>
                    <td>
                        <textarea name="term_meta[description]" id="term_meta[description]" rows="4">'. $meta_description .'</textarea>
                    </td>
                </th>
            </tr>
        ';
    }
 
    public function save_tax_meta( $term_id ){
 
        if ( isset( $_POST['term_meta'] ) ) {
            
            $term_meta = array();

            $term_meta['title'] = isset ( $_POST['term_meta']['title'] ) ? esc_sql( $_POST['term_meta']['title'] ) : '';
            $term_meta['keywords'] = isset ( $_POST['term_meta']['keywords'] ) ? esc_sql( $_POST['term_meta']['keywords'] ) : '';
            $term_meta['description'] = isset ( $_POST['term_meta']['description'] ) ? esc_sql( $_POST['term_meta']['description'] ) : '';

            update_option( "_taxonomy_meta_$term_id", $term_meta );
 
        }
    }
 
}
 
$tax_cat = new __Tax_Cat();

?>