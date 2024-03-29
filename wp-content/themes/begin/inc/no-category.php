<?php
add_action( 'load-themes.php', 'del_category_base_refresh_rules' );
add_action('created_category',  'del_category_base_refresh_rules');
add_action('delete_category',   'del_category_base_refresh_rules');
add_action('edited_category',   'del_category_base_refresh_rules');
add_action('init',              'del_category_base_permastruct');

add_filter('category_rewrite_rules', 'del_category_base_rewrite_rules');
add_filter('query_vars',             'del_category_base_query_vars');
add_filter('request',                'del_category_base_request');
register_deactivation_hook(__FILE__, 'del_category_base_deactivate');

function del_category_base_refresh_rules() {
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

function del_category_base_deactivate() {
	remove_filter( 'category_rewrite_rules', 'del_category_base_rewrite_rules' );
	del_category_base_refresh_rules();
}

function del_category_base_permastruct() {
	global $wp_rewrite;
	global $wp_version;

	if ( $wp_version >= 3.4 ) {
		$wp_rewrite->extra_permastructs['category']['struct'] = '%category%';
	} else {
		$wp_rewrite->extra_permastructs['category'][0] = '%category%';
	}
}

function del_category_base_rewrite_rules($category_rewrite) {
	global $wp_rewrite;
	$category_rewrite=array();
	if ( class_exists( 'Sitepress' ) ) {
		global $sitepress;

		remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
		$categories = get_categories( array( 'hide_empty' => false ) );
		add_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), 10, 4 );
	} else {
		$categories = get_categories( array( 'hide_empty' => false ) );
	}

	foreach( $categories as $category ) {
		$category_nicename = $category->slug;

		if ( $category->parent == $category->cat_ID ) {
			$category->parent = 0;
		} elseif ( $category->parent != 0 ) {
			$category_nicename = get_category_parents( $category->parent, false, '/', true ) . $category_nicename;
		}

		$category_rewrite['('.$category_nicename.')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
		$category_rewrite["({$category_nicename})/{$wp_rewrite->pagination_base}/?([0-9]{1,})/?$"] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
		$category_rewrite['('.$category_nicename.')/?$'] = 'index.php?category_name=$matches[1]';
	}

	$old_category_base = get_option( 'category_base' ) ? get_option( 'category_base' ) : 'category';
	$old_category_base = trim( $old_category_base, '/' );
	$category_rewrite[$old_category_base.'/(.*)$'] = 'index.php?category_redirect=$matches[1]';

	return $category_rewrite;
}

function del_category_base_query_vars($public_query_vars) {
	$public_query_vars[] = 'category_redirect';
	return $public_query_vars;
}

function del_category_base_request($query_vars) {
	if ( isset( $query_vars['category_redirect'] ) ) {
		$catlink = trailingslashit( get_option( 'home' ) ) . user_trailingslashit( $query_vars['category_redirect'], 'category' );
		status_header( 301 );
		header( "Location: $catlink" );
		exit();
	}
	return $query_vars;
}