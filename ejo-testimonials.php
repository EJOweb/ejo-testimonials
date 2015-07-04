<?php
/**
 * Plugin Name: EJO Testimonials
 * Plugin URI: http://github.com/ejoweb/ejo-testimonials
 * Description: Testimonials, the EJOweb way. 
 * Version: 0.9.3
 * Author: Erik Joling
 * Author URI: http://www.ejoweb.nl/
 */

// Store directory path of this plugin
$plugin_dir = trailingslashit( plugin_dir_path( __FILE__ ) );

//* Load classes
include_once( $plugin_dir . 'inc/ejo-testimonials-metabox-class.php' );
include_once( $plugin_dir . 'inc/ejo-testimonials-settings-class.php' );
include_once( $plugin_dir . 'inc/ejo-testimonials-widget.php' );

/**
 *
 */
final class EJO_Testimonials
{
	 //* Version number of this plugin
	public static $version = '0.9.3';

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
		//* Setup
		self::setup();

		// //* Register Post Type
		add_action( 'init', array( $this, 'register_testimonials_post_type' ) );

		//* Metabox
		EJO_Testimonials_Metabox::init();

		//* Settings
		EJO_Testimonials_Settings::init();

		//* Widget
		add_action( 'widgets_init', array( 'EJO_Testimonials_Widget', 'register' ) );
	}

	//* Setup
	private static function setup() 
	{
		//* Path & Url
		self::$dir = plugin_dir_path( __FILE__ );
		self::$uri = plugin_dir_url( __FILE__ );
	}

	//* Register Post Type
	public function register_testimonials_post_type() 
	{
		include( self::$dir . 'inc/register-post-type.php' );
	}

	//* Print testimonial
	public static function the_testimonial( $post_id = null )
	{
		echo self::get_testimonial($post_id);
	}

	//* Get testimonial
	public static function get_testimonial( $post_id = null )
	{
		$title 	 = EJO_Testimonials::get_testimonial_title($post_id); //* Get title of testimonial
		$image   = EJO_Testimonials::get_testimonial_image($post_id); //* Store image
		$content = EJO_Testimonials::get_testimonial_content($post_id); //* Store content
		$author  = EJO_Testimonials::get_testimonial_author($post_id); //* Get testimonial author
		$info	 = EJO_Testimonials::get_testimonial_info($post_id); //* Get testimonial info
		$date 	 = EJO_Testimonials::get_testimonial_date($post_id); //* Get testimonial date
		$company = EJO_Testimonials::get_testimonial_company($post_id); //* Get testimonial company
		$link 	 = EJO_Testimonials::get_testimonial_link($post_id); //* Store link of testimonial

		?>

		<h4 class="entry-title"><?php echo $title; ?></h4>
		<?php echo $image; ?>
		<blockquote><?php echo $content; ?></blockquote>
		<span class="author"><?php echo $author; ?></span>
		<span class="info"><?php echo $info; ?></span>
		<span class="date"><?php echo $date; ?></span>
		<span class="company"><?php echo $company; ?></span>
		<?php echo $link; ?>
		
		<?php
	}

	//* Get testimonial title
	public static function get_testimonial_title( $post_id = null )
	{
		return $title = get_the_title( $post_id );
	}

	//* Get testimonial image
	public static function get_testimonial_image($post_id = null, $image_size = 'thumbnail')
	{	
		if (!has_post_thumbnail($post_id))
			return '<img src="'.self::$uri.'images/unknown_person.jpg" title="referentie schrijverfoto onbekend" class="attachment-thumbnail wp-post-image">';
		else
			return get_the_post_thumbnail( $post_id, $image_size );
	}

	//* Get testimonial content
	public static function get_testimonial_content( $post_id = null, $show_excerpt = true )
	{
		//* By default show excerpt, otherwise show whole content
		if ( $show_excerpt === true )
			$quote = get_the_excerpt();
		else
			$quote = get_the_content();

		return $quote;
	}

	//* Get testimonial author
	public static function get_testimonial_author( $post_id = null )
	{
		//* If no post_id, get global post_id
		if ( empty($post_id) ) {
			global $post;
			$post_id = $post->ID;
		}
		//* Get author from post meta
		$author = get_post_meta( $post_id, 'ejo_testimonials_author', true );

		//* Fallback to old way of storing testimonials-metadata
		if (empty($author)) {
		
			//* Get testimonial meta data
			$testimonial_data = get_post_meta( $post_id, 'ejo_testimonials_data', true );

			//* Set author
			$author = (isset($testimonial_data['author'])) ? $testimonial_data['author'] : '';
		}		

		return $author;
	}

	//* Get testimonial info
	public static function get_testimonial_info( $post_id = null )
	{	
		//* If no post_id, get global post_id
		if ( empty($post_id) ) {
			global $post;
			$post_id = $post->ID;
		}
		//* Get info from post meta
		$info = get_post_meta( $post_id, 'ejo_testimonials_info', true );

		//* Fallback to old way of storing testimonials-metadata
		if (empty($info)) {
		
			//* Get testimonial meta data
			$testimonial_data = get_post_meta( $post_id, 'ejo_testimonials_data', true );

			//* Set info
			$info = (isset($testimonial_data['info'])) ? $testimonial_data['info'] : '';
		}		

		return $info;
	}

	//* Get testimonial date
	public static function get_testimonial_date( $post_id = null )
	{	
		//* If no post_id, get global post_id
		if ( empty($post_id) ) {
			global $post;
			$post_id = $post->ID;
		}
		//* Get date from post meta
		$date = get_post_meta( $post_id, 'ejo_testimonials_date', true );

		//* Fallback to old way of storing testimonials-metadata
		if (empty($date)) {
		
			//* Get testimonial meta data
			$testimonial_data = get_post_meta( $post_id, 'ejo_testimonials_data', true );

			//* Set date
			$date = (isset($testimonial_data['date'])) ? $testimonial_data['date'] : '';
		}		

		return $date;
	}

	//* Get testimonial company
	public static function get_testimonial_company( $post_id = null )
	{	
		//* If no post_id, get global post_id
		if ( empty($post_id) ) {
			global $post;
			$post_id = $post->ID;
		}
		//* Get company from post meta
		$company = get_post_meta( $post_id, 'ejo_testimonials_company', true );

		//* Fallback to old way of storing testimonials-metadata
		if (empty($company)) {
		
			//* Get testimonial meta data
			$testimonial_data = get_post_meta( $post_id, 'ejo_testimonials_data', true );

			//* Set company
			$company = (isset($testimonial_data['company'])) ? $testimonial_data['company'] : '';
		}

		return $company;
	}

	//* Get testimonial link
	public static function get_testimonial_external_url( $post_id = null )
	{	
		//* If no post_id, get global post_id
		if ( empty($post_id) ) {
			global $post;
			$post_id = $post->ID;
		}
		//* Get url from post meta
		$url = get_post_meta( $post_id, 'ejo_testimonials_url', true );

		//* Fallback to old way of storing testimonials-metadata
		if (empty($url)) {
		
			//* Get testimonial meta data
			$testimonial_data = get_post_meta( $post_id, 'ejo_testimonials_data', true );

			//* Set url
			$url = (isset($testimonial_data['url'])) ? $testimonial_data['url'] : '';
		}

		return $url;
	}

	//* Get testimonial permalink
	public static function get_testimonial_permalink( $post_id = null )
	{	
		//* Get permalink
		$permalink = get_permalink( $post_id );

		return (isset($permalink)) ? $permalink : '';
	}

	//* Get testimonial link
	public static function get_testimonial_link( $post_id = null, $read_more = '', $class = 'button' )
	{	
		//* Get linktext
		$ejo_testimonials_settings = get_option( 'ejo_testimonials_settings', array() );

		//* If no read_more text is given; load value from options or use default 'Lees Meer' fallback
		if (empty($read_more))
			$read_more = (isset($ejo_testimonials_settings['linktext'])) ? $ejo_testimonials_settings['linktext'] : 'Lees Meer';

		//* Filter option for Linktext
		$read_more = apply_filters( 'ejo_testimonials_read_more', $read_more );

		//* Get the permalink to the testimonial
		$url = self::get_testimonial_permalink($post_id);
		
		//* Process link
		$link = sprintf( "<a class='%s' href='%s'>%s</a>", $class, $url, $read_more );

		return $link;
	}
	

	//* Wrap a link around content
	public static function wrap_testimonials_source_link($post_id, $content) 
	{
		//* Get testimonial meta data
		$testimonial_data = get_post_meta( $post_id, 'ejo_testimonials_data', true );

		//* Check if on singular page and if an url is given
		if( is_singular( self::$post_type ) && isset($testimonial_data['url']) ) {

			//* Get url
			$url = $testimonial_data['url'];

			//* Make sure to add 'http://' if not already
			$url = (strpos($url, "http://") !== 0) ? "http://{$url}" : $url; //* Check if http://

			//* Add link to content
			$content = sprintf( '<a href="%s" target="_blank">%s</a>', $url, $content );
		}

		return $content;
	}
}

EJO_Testimonials::init();
