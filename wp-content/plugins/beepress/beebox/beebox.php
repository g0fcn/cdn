<?php
namespace Bee\Beebox;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WP_BEEBOX_VERSION', '1.7.1' );

/**
 * Autoloader
 *
 * @param string $class The fully-qualified class name.
 * @return void
 *
 *  * @since 1.0.0
 */
spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = __NAMESPACE__;

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/includes/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
require __DIR__ . '/vendor/autoload.php';

/**
 * Initialize Plugin
 *
 * @since 1.0.0
 */
function init() {
	$wpr = Plugin::get_instance();
	$wpr_shortcode = Shortcode::get_instance();
	$wpr_admin = Admin::get_instance();
    $beebox_crawler_rest = Endpoint\Crawler::get_instance();
    $beebox_posts_rest = Endpoint\Post::get_instance();
    $license = Endpoint\License::get_instance();
    $cdn = Endpoint\CDN::get_instance();
    $code = Endpoint\Code::get_instance();
}
add_action( 'plugins_loaded', 'Bee\\Beebox\\init' );

// /**
//  * Register the widget
//  *
//  * @since 1.0.0
//  */
// function widget_init() {
// 	return register_widget( new Widget );
// }
// add_action( 'widgets_init', 'Bee\\Beebox\\widget_init' );

/**
 * Register activation and deactivation hooks
 */
register_activation_hook( __FILE__, array( 'Bee\\Beebox\\Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Bee\\Beebox\\Plugin', 'deactivate' ) );