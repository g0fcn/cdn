<?php if ( ! defined( 'ABSPATH' ) ) exit;
function be_title_head() {
	global $post, $term;
?>
<?php $term_id = ''; ?>
<?php $bepagin = ' - ' . sprintf( __( '第', 'begin' ) ) . ' '; ?>
<?php if ( zm_get_option( 'languages_en' ) ) { ?><?php $pagin = ' ' . sprintf( __( '页', 'begin' ) ); ?><?php } else { ?><?php $pagin = ''; ?><?php } ?>
<?php if ( zm_get_option( 'wp_title' ) ) { ?>
<?php if ( zm_get_option( 'home_title' ) == '' ) { ?>
<?php if ( is_home() || is_front_page() ) { ?><title><?php bloginfo( 'name' ); ?><?php $description = get_bloginfo( 'description', 'display' ); if ( $description ) : ?><?php if ( zm_get_option( 'blog_info' ) ) { ?><?php connector(); ?><?php bloginfo( 'description' ); ?><?php } ?><?php endif; ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo get_query_var( 'paged' ); echo $pagin;}?></title><?php } ?>
<?php } else { ?>
<?php if ( is_home() || is_front_page() ) { ?><title><?php echo zm_get_option( 'home_title' ); ?><?php if ( zm_get_option( 'home_info' ) ) { ?><?php connector(); ?><?php echo zm_get_option( 'home_info' ); ?><?php } ?><?php if ( get_query_var('paged' ) ) { echo $bepagin; echo get_query_var( 'paged' ); echo $pagin;}?></title><?php } ?>
<?php } ?>
<?php if ( is_search() ) { ?><title><?php echo wp_get_document_title(); ?></title><?php } ?>
<?php if ( is_single() ) { ?><title><?php if ( get_post_meta(get_the_ID(), 'custom_title', true ) ) { ?><?php echo trim( get_post_meta(get_the_ID(), 'custom_title', true ) ); ?><?php if (get_query_var('page')) { echo $bepagin; echo get_query_var( 'page' ); echo $pagin;}?><?php } else { ?><?php if ( zm_get_option( 'seo_title_tag' ) ) { ?><?php be_seo_tags(); ?><?php echo zm_get_option( 'seo_separator_tag' ); ?><?php } ?><?php echo trim( wp_title( '',0 ) ); ?><?php if ( get_query_var( 'page' ) ) { echo $bepagin; echo get_query_var('page'); echo $pagin;}?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?><?php } ?></title><?php } ?>
<?php if ( is_page() && !is_front_page() ) { ?><title><?php if ( get_post_meta(get_the_ID(), 'custom_title', true) ) { ?><?php echo trim(get_post_meta( get_the_ID(), 'custom_title', true ) ); ?><?php } else { ?><?php echo trim( wp_title( '',0 ) ); ?><?php } ?><?php if ( get_post_meta( get_the_ID(), 'custom_title', true ) ) { ?><?php } else { ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?><?php } ?></title><?php } ?>
<?php if ( is_category() ) { ?><?php $term_id = get_query_var( 'cat' ); ?><?php } ?>
<?php if ( get_option( 'cat-title-'.$term_id )) : ?>
<?php if ( is_category() ) { ?><title><?php echo get_option( 'cat-title-'.$term_id ); ?><?php if (get_query_var( 'paged' ) ) { echo $bepagin; echo get_query_var( 'paged' ); echo $pagin;}?></title><?php } ?>
<?php else: ?>
<?php if ( is_category() ) { ?><title><?php single_cat_title(); ?><?php if (get_query_var( 'paged' ) ) { echo $bepagin; echo get_query_var( 'paged' ); echo $pagin;}?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php endif; ?>
<?php if ( is_year() ) { ?><title><?php the_time('Y年'); ?><?php _e( '所有文章', 'begin' ); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( is_month() ) { ?><title><?php the_time('Y年'); ?><?php the_time('F'); ?><?php _e( '份所有文章', 'begin' ); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( is_day() ) { ?><title><?php the_time('Y年n月j日'); ?><?php _e( '所有文章', 'begin' ); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( is_author() ) { ?><title><?php the_author(); ?> <?php _e( '发表的所有文章', 'begin' ); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( is_404() ) { ?><title><?php echo stripslashes( zm_get_option( '404_t' ) ); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( is_tag() ) { ?><?php $term_id = get_query_var('tag_id'); ?><?php } ?>
<?php if ( get_option( 'tag-title-'.$term_id )) : ?>
<?php if ( is_tag() ) { ?><title><?php echo get_option( 'tag-title-'.$term_id ); ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo get_query_var( 'paged' ); echo $pagin;}?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php else: ?>
<?php if (function_exists('is_tag')) { if ( is_tag() ) { ?><title><?php  single_tag_title( "", true ); ?><?php if ( get_query_var('paged' ) ) { echo $bepagin; echo get_query_var( 'paged' ); echo $pagin;}?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?><?php } ?>
<?php endif; ?>
<?php if ( ! is_single() && ! is_home() && ! is_category() && ! is_search() && ! is_tag() && ! is_author() ) { ?>
<?php if ( has_post_format( 'aside' ) ) { ?><title><?php echo get_post_format_string( 'aside' ); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( has_post_format( 'image' ) ) { ?><title><?php echo get_post_format_string( 'image' ); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php } ?>
<?php if ( is_tax( 'notice' ) ) { ?><title><?php be_set_title(); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( is_tax( 'gallery' ) ) { ?><?php $term_my=get_term_by('slug',$term,'gallery'); ?><?php if ( get_option( 'cat-title-'.$term_my->term_id )) : ?><title><?php echo get_option( 'cat-title-'.$term_my->term_id ); ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo  get_query_var( 'paged' ) ; echo $pagin;}?></title><?php else: ?><title><?php be_set_title(); ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo  get_query_var( 'paged' ) ; echo $pagin;}?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php endif; ?><?php } ?>
<?php if ( is_tax( 'gallerytag' ) ) { ?><title><?php be_set_title(); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( is_tax( 'videos' ) ) { ?><?php $term_my=get_term_by('slug',$term,'videos'); ?><?php if ( get_option( 'cat-title-'.$term_my->term_id )) : ?><title><?php echo get_option( 'cat-title-'.$term_my->term_id ); ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo  get_query_var( 'paged' ) ; echo $pagin;}?></title><?php else: ?><title><?php be_set_title(); ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo  get_query_var( 'paged' ) ; echo $pagin;}?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php endif; ?><?php } ?>
<?php if ( is_tax( 'videotag' ) ) { ?><title><?php be_set_title(); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( is_tax( 'taobao' ) ) { ?><?php $term_my=get_term_by('slug',$term,'taobao'); ?><?php if ( get_option( 'cat-title-'.$term_my->term_id )) : ?><title><?php echo get_option( 'cat-title-'.$term_my->term_id ); ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo  get_query_var( 'paged' ) ; echo $pagin;}?></title><?php else: ?><title><?php be_set_title(); ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo  get_query_var( 'paged' ) ; echo $pagin;}?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php endif; ?><?php } ?>
<?php if ( is_tax( 'taotag' ) ) { ?><title><?php be_set_title(); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( is_tax( 'products' ) ) { ?><?php $term_my=get_term_by('slug',$term,'products'); ?><?php if ( get_option( 'cat-title-'.$term_my->term_id )) : ?><title><?php echo get_option( 'cat-title-'.$term_my->term_id ); ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo  get_query_var( 'paged' ) ; echo $pagin;}?></title><?php else: ?><title><?php be_set_title(); ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo  get_query_var( 'paged' ) ; echo $pagin;}?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php endif; ?><?php } ?>
<?php if ( is_tax( 'product_cat' ) ) { ?><title><?php be_set_title(); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( is_tax( 'product_tag' ) ) { ?><title><?php be_set_title(); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if (function_exists( 'is_shop' )) { ?><?php if ( is_shop('')) { ?><title><?php echo trim( wp_title( '',0 ) ); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?><?php } ?>
<?php if ( is_tax( 'download_category' ) ) { ?><title><?php be_set_title(); ?><?php connector(); ?><?php if (!zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( is_tax( 'download_tag' ) ) { ?><title><?php be_set_title(); ?><?php connector(); ?><?php if ( !zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( is_tax( 'dwqa-question_category' ) ) { ?><title><?php be_set_title(); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( is_tax( 'dwqa-question_tag' ) ) { ?><title><?php be_set_title(); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( is_tax( 'favorites' ) ) { ?><title><?php be_set_title(); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( is_tax( 'special' ) ) { ?><?php $term_my=get_term_by('slug',$term,'special'); ?><?php if ( get_option( 'cat-title-'.$term_my->term_id )) : ?><title><?php echo get_option( 'cat-title-'.$term_my->term_id ); ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo  get_query_var( 'paged' ) ; echo $pagin;}?></title><?php else: ?><title><?php be_set_title(); ?><?php if ( get_query_var( 'paged' ) ) { echo $bepagin; echo  get_query_var( 'paged' ) ; echo $pagin;}?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php endif; ?><?php } ?>
<?php if ( is_tax( 'documents' ) ) { ?><title><?php be_set_title(); ?><?php connector(); ?><?php if (! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( function_exists( 'is_bbpress' ) ) { ?><?php if ( is_bbpress() && ! is_single() ) { ?><title><?php the_title();?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?><?php } ?>
<?php if ( is_tax( 'filtersa' ) || is_tax( 'filtersb' ) || is_tax( 'filtersc' ) || is_tax( 'filtersd' ) || is_tax( 'filterse' ) || is_tax( 'filtersf' ) || is_tax( 'filtersg' ) || is_tax( 'filtersh' ) || is_tax( 'filtersl' ) || is_tax( 'filtersj' ) ) { ?><title><?php be_set_title(); ?><?php connector(); ?><?php if ( ! zm_get_option( 'blog_name' ) ) { bloginfo( 'name' );} ?></title><?php } ?>
<?php if ( is_single() || is_page() ) {
	if ( function_exists('get_query_var') ) {
		$cpage = intval( get_query_var( 'cpage' ) );
		$commentPage = intval( get_query_var( 'comment-page' ) );
	}
	if ( ! empty( $cpage ) || !empty( $commentPage ) ) {
		echo '<meta name="robots" content="noindex, nofollow" />';
		echo "\n";
	}
}
?>
<?php
if ( ! function_exists( 'utf8Substr' ) ) {
	function utf8Substr( $str, $from, $len ) {
		return preg_replace( '#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $from . '}' . '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s', '$1', $str );
	}
}
if ( is_single() ){
	if ( $post->post_excerpt ) {
		if ( preg_match( '/<p>(.*)<\/p>/iU',trim( strip_tags( $post->post_excerpt,"<p>" ) ),$result ) ) {
			$post_content = $result['1'];
		} else {
			$post_content_r = explode( "\n",trim( strip_tags( strip_shortcodes($post->post_excerpt ) ) ) );
			$post_content = $post_content_r['0'];
		}
		$description = utf8Substr( $post_content, 0, 220 );
	} else {
		if ( preg_match( '/<p>(.*)<\/p>/iU',trim( strip_tags( $post->post_content,"<p>" ) ),$result ) ) {
			$post_content = $result['1'];
		} else {
			$post_content_r = explode( "\n",trim( strip_tags( strip_shortcodes($post->post_content ) ) ) );
			$post_content = $post_content_r['0'];
		}
		$description = utf8Substr( $post_content, 0, 220 );
	}
	$keywords = "";
	$tags = wp_get_post_tags( $post->ID );
	foreach ( $tags as $tag ) {
		$keywords = $keywords . $tag->name . ",";
	}
}
?>
<?php echo "\n"; ?>
<?php if ( is_single() ) { ?>
<?php if ( get_post_type() == 'sites' ) { ?>
<?php if ( get_post_meta( get_the_ID(), 'sites_des', true ) ) : ?>
<meta name="description" content="<?php echo get_post_meta( get_the_ID(), 'sites_des', true ); ?>" />
<?php else: ?>
<meta name="description" content="<?php echo get_post_meta( get_the_ID(), 'sites_description', true ); ?>" />
<?php endif; ?>
<?php if ( get_post_meta( get_the_ID(), 'keywords', true ) ) : ?>
<meta name="keywords" content="<?php echo get_post_meta( get_the_ID(), 'keywords', true ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php echo trim( wp_title('', 0 ) ); ?>" />
<?php endif; ?>
<?php } else { ?>
<?php if ( get_post_meta( get_the_ID(), 'description', true ) ) : ?>
<meta name="description" content="<?php echo get_post_meta(get_the_ID(), 'description', true ); ?>" />
<?php else: ?>
<?php if ( function_exists( 'is_bbpress' ) ) { ?>
<?php if ( ! bbp_is_single_topic() && ! bbp_is_single_forum() ) { ?>
<meta name="description" content="<?php echo trim( $description ); ?>" />
<?php } ?>
<?php if ( bbp_is_single_forum() ) { ?>
<meta name="description" content="<?php echo wp_trim_words( get_the_content(), 160 ); ?>" />
<?php } ?>
<?php if ( bbp_is_single_topic() ) { ?>
<meta name="description" content="<?php echo wp_trim_words( get_the_content(), 160 ); ?>" />
<?php } ?>
<?php } else { ?>
<meta name="description" content="<?php echo trim( $description ); ?>" />
<?php } ?>
<?php endif; ?>
<?php if ( get_post_meta( get_the_ID(), 'keywords', true ) ) : ?>
<meta name="keywords" content="<?php echo get_post_meta( get_the_ID(), 'keywords', true ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php echo trim( $keywords,',' ); ?>" />
<?php endif; ?>
<?php } ?>
<?php } ?>
<?php if ( is_page() ) { ?>
<?php if ( is_front_page() ) { ?>
<meta name="description" content="<?php echo zm_get_option( 'description' ); ?>" />
<meta name="keywords" content="<?php echo zm_get_option( 'keyword' ); ?>" />
<?php } else { ?>
<meta name="description" content="<?php echo get_post_meta( get_the_ID(), 'description', true ); ?>" />
<meta name="keywords" content="<?php echo get_post_meta( get_the_ID(), 'keywords', true ); ?>" />
<?php } ?>
<?php } ?>
<?php if ( is_category() ) { ?>
<meta name="description" content="<?php echo trim( strip_tags( wp_trim_words(tag_description(), 160 ) ) ); ?>" />
<?php if ( get_option( 'cat-words-' . $term_id ) ) : ?>
<meta name="keywords" content="<?php echo get_option( 'cat-words-'.$term_id ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php single_cat_title(); ?>" />
<?php endif; ?>
<?php } ?>
<?php if ( is_search() ) { ?>
<meta name="description" content="<?php _e( '在', 'begin' ); ?><?php bloginfo( 'name' ); ?><?php _e( '网站搜索', 'begin' ); ?>'<?php echo get_search_query(); ?>'<?php _e( '结果', 'begin' ); ?>" />
<meta name="keywords" content="<?php echo get_search_query(); ?>" />
<?php } ?>
<?php if ( is_author() ) { ?>
<meta name="description" content="<?php if ( get_the_author_meta( 'description' ) ) { ?><?php the_author_meta( 'user_description' ); ?><?php } else { ?><?php _e( '暂无个人说明', 'begin' ); ?><?php } ?>" />
<meta name="keywords" content="<?php the_author(); ?>" />
<?php } ?>
<?php if ( is_tag() ) { ?>
<meta name="description" content="<?php echo trim( strip_tags( wp_trim_words(tag_description(), 160 ) ) ); ?>" />
<?php if ( get_option( 'tag-words-' . $term_id ) ) : ?>
<meta name="keywords" content="<?php echo get_option( 'tag-words-'.$term_id ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php echo single_tag_title(); ?>" />
<?php endif; ?>
<?php } ?>
<?php if ( is_home() ) { ?>
<meta name="description" content="<?php echo zm_get_option( 'description' ); ?>" />
<meta name="keywords" content="<?php echo zm_get_option( 'keyword' ); ?>" />
<?php } ?>
<?php if ( is_tax('taobao') ) { ?>
<meta name="description" content="<?php echo trim(strip_tags(tag_description())); ?>" />
<?php $term_my=get_term_by('slug', $term, 'taobao'); ?>
<?php if ( get_option( 'cat-words-' . $term_my->term_id ) ) : ?>
<meta name="keywords" content="<?php echo get_option( 'cat-words-'.$term_my->term_id ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php be_set_title(); ?>" />
<?php endif; ?>
<?php } ?>
<?php if ( is_tax('gallery') ) { ?>
<meta name="description" content="<?php echo trim( strip_tags( tag_description() ) ); ?>" />
<?php $term_my=get_term_by( 'slug', $term, 'gallery' ); ?>
<?php if ( get_option( 'cat-words-' . $term_my->term_id ) ) : ?>
<meta name="keywords" content="<?php echo get_option( 'cat-words-'.$term_my->term_id ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php be_set_title(); ?>" />
<?php endif; ?>
<?php } ?>
<?php if ( is_tax('videos') ) { ?>
<meta name="description" content="<?php echo trim( strip_tags( tag_description() ) ); ?>" />
<?php $term_my=get_term_by( 'slug', $term, 'videos' ); ?>
<?php if ( get_option( 'cat-words-' . $term_my->term_id ) ) : ?>
<meta name="keywords" content="<?php echo get_option( 'cat-words-'.$term_my->term_id ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php be_set_title(); ?>" />
<?php endif; ?>
<?php } ?>
<?php if ( is_tax( 'products' ) ) { ?>
<meta name="description" content="<?php echo trim( strip_tags( tag_description() ) ); ?>" />
<?php $term_my=get_term_by('slug', $term, 'products'); ?>
<?php if ( get_option( 'cat-words-' . $term_my->term_id ) ) : ?>
<meta name="keywords" content="<?php echo get_option( 'cat-words-'.$term_my->term_id ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php be_set_title(); ?>" />
<?php endif; ?>
<?php } ?>
<?php if ( is_tax( 'special' ) ) { ?>
<meta name="description" content="<?php echo trim( strip_tags( tag_description() ) ); ?>" />
<?php $term_my=get_term_by( 'slug', $term, 'special'); ?>
<?php if ( get_option( 'cat-words-' . $term_my->term_id ) ) : ?>
<meta name="keywords" content="<?php echo get_option( 'cat-words-'.$term_my->term_id ); ?>" />
<?php else: ?>
<meta name="keywords" content="<?php be_set_title(); ?>" />
<?php endif; ?>
<?php } ?>
<?php } ?>
<?php 
if ( zm_get_option( 'baidu_time' ) ) {
if ( is_single() || is_page() ) {
echo '<script type="application/ld+json">
{
	"@context": "https://ziyuan.baidu.com/contexts/cambrian.jsonld",
	"@id": "' . get_the_permalink() . '",
	"appid": "' . zm_get_option('daily_token') . '",
	"title": "'.get_the_title() . '",
	"images": ["' . og_post_img() . '"],
	"description": "' . zm_og_excerpt() . '",
	"pubDate": "' . get_the_time('Y-m-d\TH:i:s') . '",
	"upDate": "' . get_the_modified_time('Y-m-d\TH:i:s') . '",
}
</script>';
}
}
?>
<?php if ( zm_get_option( 'og_title' )) { ?>
<?php if ( is_single() ) { ?>
<meta property="og:type" content="acticle">
<meta property="og:locale" content="<?php echo get_bloginfo( 'language' ); ?>" />
<meta property="og:title" content="<?php the_title(); ?>" />
<meta property="og:author" content="<?php the_author_meta( 'display_name', $post->post_author ); ?>" />
<meta property="og:image" content="<?php echo og_post_img(); ?>" />
<meta property="og:site_name" content="<?php bloginfo( 'name' ); ?>">
<?php $description = get_post_meta( get_the_ID(), 'description', true ); ?>
<?php if ( get_post_meta( get_the_ID(), 'description', true ) ) : ?>
<meta property="og:description" content="<?php echo $description; ?>" />
<?php else: ?>
<meta property="og:description" content="<?php echo zm_og_excerpt(); ?>" />
<?php endif; ?>
<meta property="og:url" content="<?php the_permalink(); ?>" />
<meta property="og:release_date" content="<?php echo get_the_date('Y-m-d'); ?> <?php echo get_the_time('H:i:s'); ?>" />
<?php } ?>
<?php } ?>
<?php } ?>