<?php
namespace Bee\Beebox;

/**
 * @subpackage Shortcode
 */
class Shortcode {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
			self::$instance->do_hooks();
		}

		return self::$instance;
	}

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		$plugin = Plugin::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->version = $plugin->get_plugin_version();

	}


	/**
	 * Handle WP actions and filters.
	 *
	 * @since 	1.0.0
	 */
	private function do_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_scripts' ) );
	}

	/**
	 * Register frontend-specific javascript
	 *
	 * @since     1.0.0
	 */
	public function register_frontend_scripts() {
		wp_register_script( $this->plugin_slug . '-shortcode-script', plugins_url( 'assets/js/shortcode.js', dirname( __FILE__ ) ), array( 'jquery' ), $this->version );
		wp_register_style( $this->plugin_slug . '-shortcode-style', plugins_url( 'assets/css/shortcode.css', dirname( __FILE__ ) ), $this->version );
	}

	public function shortcode( $atts ) {
		return '';
	}
}
