<?php
/**
 * Plugin Name:         EJO Testimonials
 * Plugin URI:          http://github.com/ejoweb/ejo-testimonials
 * Description:         Testimonials, the EJOweb way. 
 * Version:             1.0
 * Author:              Erik Joling
 * Author URI:          http://www.ejoweb.nl/
 *
 * GitHub Plugin URI:   https://github.com/EJOweb/ejo-testimonials
 * GitHub Branch:       experimental_rewrite
 */

// Store directory path of this plugin
define( 'EJO_TS_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'EJO_TS_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

//* Load classes
include_once( EJO_TS_PLUGIN_DIR . 'inc/helper-functions.php' );
include_once( EJO_TS_PLUGIN_DIR . 'inc/metabox-class.php' );
include_once( EJO_TS_PLUGIN_DIR . 'inc/settings-class.php' );
include_once( EJO_TS_PLUGIN_DIR . 'inc/widget-class.php' );

/**
 *
 */
final class EJO_Testimonials
{
	 //* Version number of this plugin
	public static $version = '1.0';

	//* Holds the instance of this class.
	private static $_instance = null;

	//* Store the slug of this plugin
	public static $slug = 'ejo-testimonials';

	//* Store post-type
	public static $post_type = 'ejo_testimonials';

	//* Stores the directory path for this plugin.
	public static $dir;

	//* Stores the directory URI for this plugin.
	public static $uri;

	//* Returns the instance.
	public static function init() 
	{
		if ( !self::$_instance )
			self::$_instance = new self;
		return self::$_instance;
	}

	//* Plugin setup.
	protected function __construct() 
	{
		// //* Register Post Type
		add_action( 'init', array( $this, 'register_testimonials_post_type' ) );

		//* Metabox
		EJO_Testimonials_Metabox::init();

		//* Settings
		EJO_Testimonials_Settings::init();

		//* Widget
		add_action( 'widgets_init', array( 'EJO_Testimonials_Widget', 'register' ) );
	}

	//* Register Post Type
	public function register_testimonials_post_type() 
	{
		include( EJO_TS_PLUGIN_DIR . 'inc/register-post-type.php' );
	}
}

EJO_Testimonials::init();
