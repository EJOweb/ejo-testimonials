<?php
/**
 * Plugin Name: EJO Testimonials
 * Plugin URI: http://github.com/ejoweb/ejo-testimonials
 * Description: Testimonials, the EJOweb way. 
 * Version: 0.9.1
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
	public static $version = '0.9.1';

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
		//* Get title of testimonial
		$title 	 = self::get_testimonial_title_wrapped($post_id);

		//* Store image
		$image   = self::get_testimonial_image_wrapped($post_id);

		//* Store content
		$content = self::get_testimonial_content_wrapped($post_id);

		//* Get testimonial author
		$author  = self::get_testimonial_author_wrapped($post_id);

		//* Get testimonial info
		$info	 = self::get_testimonial_info_wrapped($post_id);

		//* Get testimonial date
		$date 	 = self::get_testimonial_date_wrapped($post_id);

		//* Get testimonial company
		$company = self::get_testimonial_company_wrapped($post_id);

		//* Store link of testimonial
		$link 	 = self::get_testimonial_link_wrapped($post_id);

		//* Output
		$output = $title . $image . $content . $author . $info . $date . $company . $link . "\n\n";

		return apply_filters( 'ejo_testimonials', $output, $title, $image, $content, $author, $info, $date, $company, $link);
	}

	//* Get testimonial title
	public static function get_testimonial_title( $post_id = null )
	{
		return $title = get_the_title( $post_id );
	}

	//* Get testimonial image
	public static function get_testimonial_image($post_id = null, $image_size = 'medium')
	{		
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
	public static function get_testimonial_link( $post_id = null, $class = 'button' )
	{	
		//* Get linktext
		get_option( 'ejo_testimonials_other_settings', array() );

		//* Linktext. Default = Lees Meer
		$read_more_text = (isset($ejo_testimonials_other_settings['linktext'])) ? $ejo_testimonials_other_settings['linktext'] : 'Lees Meer';

		//* Get the permalink to the testimonial
		$url = self::get_testimonial_permalink($post_id);
		
		//* Process link
		$link = sprintf( "<a class='%s' href='%s'>%s</a>", $class, $url, $read_more_text );

		return $link;
	}

	//* Wrap testimonial title
	public static function get_testimonial_title_wrapped( $post_id = null )
	{
		//* Set html tag used for title. Default is h1 or h2.
		$html_tag = ( is_singular( self::$post_type ) ) ? 'h1' : 'h2' ;

		//* Get title
		$title = self::get_testimonial_title($post_id);

		//* Add link to title if not in singular page
		if( !is_singular( self::$post_type ) ) 
			$title = sprintf( '<a href="%s" rel="bookmark">%s</a>', get_permalink( $post_id ), $title );
		
		//* Wrap title in heading
		$output = sprintf( "<{$html_tag} class='%s' itemprop='%s'>%s</{$html_tag}>", 'entry-title', 'headline', $title );

		return apply_filters( 'ejo_testimonials_title_wrap', $output, $title );
	}

	//* Wrap testimonial image
	public static function get_testimonial_image_wrapped( $post_id = null )
	{	
		//* Set html tag used for image. Default is medium.
		$image_size = apply_filters( 'ejo_testimonials_image_size', 'medium' );

		//* Get image
		$image = self::get_testimonial_image($post_id, $image_size);

		return apply_filters( 'ejo_testimonials_image_wrap', $image );
	}

	//* Get testimonial content
	public static function get_testimonial_content_wrapped( $post_id = null )
	{
		//* If post is not single testimonial and has excerpt, then use excerpt!
		$show_excerpt = ( !is_singular( self::$post_type ) );
			
		$content = self::get_testimonial_content($post_id, $show_excerpt);

		//* Wrap content
		$output = sprintf( '<blockquote>%s</blockquote>', $content );

		return apply_filters( 'ejo_testimonials_title_wrap', $output, $content );
	}


	//* Get testimonial author
	public static function get_testimonial_author_wrapped( $post_id = null )
	{
		//* Get author
		$author = self::get_testimonial_author($post_id);

		//* If no author is given return empty string
		if ( empty($author) )
			return '';

		//* Wrap author in specified html tag
		$output = sprintf( "<span class='%s'>%s</span>", 'author', $author );

		return apply_filters( 'ejo_testimonials_author_wrap', $output, $author );
	}

	//* Get testimonial info
	public static function get_testimonial_info_wrapped( $post_id = null )
	{	
		//* Get info
		$info = self::get_testimonial_info($post_id);

		//* If no info is given return empty string
		if ( empty($info) )
			return '';

		//* Wrap info in specified html tag
		$output = sprintf( "<span class='%s'>%s</span>", 'info', $info );

		return apply_filters( 'ejo_testimonials_info_wrap', $output, $info );
	}

	//* Get testimonial date
	public static function get_testimonial_date_wrapped( $post_id = null )
	{	
		//* Get date
		$date = self::get_testimonial_date($post_id);

		//* If no date is given return empty string
		if ( empty($date) )
			return '';

		//* Wrap date in specified html tag
		$output = sprintf( "<span class='%s'>%s</span>", 'date', $date );

		return apply_filters( 'ejo_testimonials_date_wrap', $output, $date );
	}

	//* Get testimonial company
	public static function get_testimonial_company_wrapped( $post_id = null )
	{	
		//* Get company
		$company = self::get_testimonial_company($post_id);

		//* If no company is given return empty string
		if ( empty($company) )
			return '';

		//* Wrap company in specified html tag
		$output = sprintf( "<span class='%s'>%s</span>", 'company', $company );

		return apply_filters( 'ejo_testimonials_company_wrap', $output, $company );
	}

	//* Get testimonial link
	public static function get_testimonial_link_wrapped( $post_id = null )
	{	
		//* If on singular testimonial page, do not show link
		if( is_singular( self::$post_type ) )
			return '';

		//* Get link
		$link = self::get_testimonial_link($post_id);

		//* Wrap link in specified html tag
		$output = sprintf( "<p class='%s'>%s</p>", 'link', $link );

		return apply_filters( 'ejo_testimonials_link_wrap', $output, $link );
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
