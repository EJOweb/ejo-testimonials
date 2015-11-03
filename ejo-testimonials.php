<?php
/**
 * Plugin Name:         EJO Testimonials
 * Plugin URI:          http://github.com/ejoweb/ejo-testimonials
 * Description:         Testimonials, the EJOweb way. 
 * Version:             1.1
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
include_once( EJO_TS_PLUGIN_DIR . 'inc/helpers.php' );
include_once( EJO_TS_PLUGIN_DIR . 'inc/metabox-class.php' );
include_once( EJO_TS_PLUGIN_DIR . 'inc/settings-class.php' );
include_once( EJO_TS_PLUGIN_DIR . 'inc/widget-class.php' );

/**
 *
 */
final class EJO_Testimonials
{
	 //* Version number of this plugin
	public static $version = '1.1';

	//* Holds the instance of this class.
	private static $_instance = null;

	//* Store the slug of this plugin
	public static $slug = 'ejo-testimonials';

	//* Store post-type
	public static $post_type = 'ejo_testimonials';

	//* Store the name of the post-type
	public static $post_type_name = 'Referenties';

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
		//* Get linktext
		$ejo_testimonials_settings = get_option( 'ejo_testimonials_settings', array() );

		//* Archive title
		$title = (!empty($ejo_testimonials_settings['title'])) ? $ejo_testimonials_settings['title'] : EJO_Testimonials::$post_type_name;

		//* Archive description
		$description = (!empty($ejo_testimonials_settings['description'])) ? $ejo_testimonials_settings['description'] : '';

		//* Archive slug
		$slug = (!empty($ejo_testimonials_settings['slug'])) ? $ejo_testimonials_settings['slug'] : self::$slug;

		$labels = array(
			'name'                => $title,
			'singular_name'       => 'Referentie',
			'menu_name'           => 'Referenties',
			'parent_item_colon'   => 'Parent Referentie:',
			'all_items'           => 'Alle Referenties',
			'view_item'           => 'Bekijk Referentie',
			'add_new_item'        => 'Nieuwe Referentie Toevoegen',
			'add_new'             => 'Nieuwe Toevoegen',
			'edit_item'           => 'Wijzig Referentie',
			'update_item'         => 'Update Referentie',
			'search_items'        => 'Zoek Referenties',
			'not_found'           => 'Niet Gevonden',
			'not_found_in_trash'  => 'Niet Gevonden in Prullenbak',
		);
		$args = array(
			'labels'              => $labels,
			'description'         => $description,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', ),
			'hierarchical'        => false,
			'menu_position'       => 26,
			'menu_icon'           => 'dashicons-format-quote',
			'public'              => true,
			'exclude_from_search' => true,

			'rewrite' => array(
				'slug'		 => $slug,
				'with_front' => false,
			),
			'has_archive'	=> $slug,
			
		);

		register_post_type( self::$post_type, $args );
	}
}

EJO_Testimonials::init();
