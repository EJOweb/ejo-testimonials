<?php
/**
 * Plugin Name: EJO Testimonials
 * Plugin URI: http://github.com/ejoweb/ejo-testimonials
 * Description: Testimonials, the EJOweb way. 
 * Version: 0.8.1
 * Author: Erik Joling
 * Author URI: http://www.ejoweb.nl/
 *
 * GitHub Plugin URI: https://github.com/EJOweb/ejo-testimonials
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
	public static $version = '0.8.1';

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
	public static function instance() 
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

	//* Get testimonial
	public static function get_testimonial($post_id, $testimonials_settings)
	{
		//* Keeper of the testimonial output
		$testimonial = array();

		//* Get testimonials info in right order
		foreach ($testimonials_settings as $testimonial_part => $field) {

			//* Skip the fields which are to be hidden
			if ($field['show'] === false)
				continue;

			//* Process testimonial based on id-part [title, author... etc]
			switch ($testimonial_part) {

				//* Title of testimonial
				case 'title':

					//* Store title of testimonial
					$testimonial['title'] = self::get_testimonial_title($post_id);

					break;

				//* Featured image of testimonial
				case 'image':

					//* Store image
					$testimonial['image'] = self::get_testimonial_image($post_id);

					break;
				
				//* Content of testimonial
				case 'content':

					//* Store content
					$testimonial['content'] = self::get_testimonial_content($post_id);

					break;
				
				//* Author metadata of testimonial
				case 'author':

					// Get testimonial author
					$author = self::get_testimonial_author($post_id);

					//* If no author is set; skip
					if ( empty($author) )
						break;

					//* Store author
					$testimonial['author'] = $author;

					break;
				
				//* Extra info metadata of testimonial
				case 'info':

					// Get testimonial info
					$info = self::get_testimonial_info($post_id);

					//* If no info is set; skip
					if ( empty($info) )
						break;

					//* Store info
					$testimonial['info'] = $info;

					break;
				
				//* Date metadata of testimonial
				case 'date':

					// Get testimonial date
					$date = self::get_testimonial_date($post_id);

					//* If no date is set; skip
					if ( empty($date) )
						break;

					//* Store date
					$testimonial['date'] = $date;

					break;

				//* Company metadata of testimonial
				case 'company':
					
					// Get testimonial company
					$company = self::get_testimonial_company($post_id);

					//* If no company is set; skip
					if ( empty($company) )
						break;

					//* Store company
					$testimonial['company'] = $company;

					break;

				//* Link of testimonial
				case 'link':

					//* Store link of testimonial
					$testimonial['link'] = self::get_testimonial_link($post_id);

					break;
			}
		}

		return $testimonial;
	}

	//* Get testimonial title
	public static function get_testimonial_title($post_id)
	{
		//* Set html tag used for title. Default is h1 or h2.
		$html_tag = apply_filters( 'ejo_testimonials_title_tag', is_singular( self::$post_type ) ? 'h1' : 'h2' );

		//* Title 
		$title = get_the_title( $post_id );

		//* Add link to title if not in singular page
		if( !is_singular( self::$post_type ) ) 
			$title = sprintf( '<a href="%s" rel="bookmark">%s</a>', get_permalink( $post_id ), $title );
		
		//* Wrap title in heading
		$title = sprintf( "<{$html_tag} class='%s' itemprop='%s'>%s</{$html_tag}>", 'entry-title', 'headline', $title );

		return $title;
	}

	//* Get testimonial image
	public static function get_testimonial_image($post_id)
	{		
		//* Set html tag used for image. Default is medium.
		$image_size = apply_filters( 'ejo_testimonials_image_size', 'medium' );

		return get_the_post_thumbnail( $post_id, $image_size );
	}

	//* Get testimonial content
	public static function get_testimonial_content($post_id)
	{
		//* If post is not single testimonial and has excerpt, then use excerpt!
		if ( !is_singular( self::$post_type ) && has_excerpt($post_id))
			$quote = get_the_excerpt();
		else
			$quote = get_the_content();

		//* Wrap quote
		$content = sprintf( '<blockquote>%s</blockquote>', $quote );

		return $content;
	}

	//* Get testimonial author
	public static function get_testimonial_author($post_id)
	{
		//* Get testimonial meta data
		$testimonial_data = get_post_meta( $post_id, 'ejo_testimonials_data', true );

		//* If no data is set; skip
		if ( !isset($testimonial_data['author']) )
			return false;

		//* Get author
		$author = $testimonial_data['author'];

		//* Set html tag used for author. Default is span.
		$html_tag = apply_filters( 'ejo_testimonials_author_tag', 'span' );

		//* Wrap author in specified html tag
		$author = sprintf( "<{$html_tag} class='%s'>%s</{$html_tag}>", 'author', $author );

		return $author;
	}

	//* Get testimonial info
	public static function get_testimonial_info($post_id)
	{	
		//* Get testimonial meta data
		$testimonial_data = get_post_meta( $post_id, 'ejo_testimonials_data', true );

		//* If no data is set; skip
		if ( !isset($testimonial_data['info']) )
			return false;

		//* Set html tag used for info. Default is span.
		$html_tag = apply_filters( 'ejo_testimonials_info_tag', 'span' );

		//* Get info
		$info = $testimonial_data['info'];

		//* Add link to external source if on singular page and url is given
		$info = self::wrap_testimonials_source_link($post_id, $info);

		//* Wrap info in specified html tag
		$info = sprintf( "<{$html_tag} class='%s'>%s</{$html_tag}>", 'info', $info );

		return $info;
	}

	//* Get testimonial date
	public static function get_testimonial_date($post_id)
	{	
		//* Get testimonial meta data
		$testimonial_data = get_post_meta( $post_id, 'ejo_testimonials_data', true );

		//* If no data is set; skip
		if ( !isset($testimonial_data['date']) )
			return false;

		//* Get date
		$date = $testimonial_data['date'];

		//* Set html tag used for date. Default is span.
		$html_tag = apply_filters( 'ejo_testimonials_date_tag', 'span' );

		//* Wrap date in specified html tag
		$date = sprintf( "<{$html_tag} class='%s'>%s</{$html_tag}>", 'date', $date );

		return $date;
	}

	//* Get testimonial company
	public static function get_testimonial_company($post_id)
	{	
		//* Get testimonial meta data
		$testimonial_data = get_post_meta( $post_id, 'ejo_testimonials_data', true );

		//* If no data is set; skip
		if ( !isset($testimonial_data['company']) )
			return false;

		//* Get company
		$company = $testimonial_data['company'];

		//* Set html tag used for company. Default is span.
		$html_tag = apply_filters( 'ejo_testimonials_company_tag', 'span' );

		//* Wrap company in specified html tag
		$company = sprintf( "<{$html_tag} class='%s'>%s</{$html_tag}>", 'company', $company );

		return $company;
	}

	//* Get testimonial link
	public static function get_testimonial_link($post_id)
	{	
		//* Get linktext
		get_option( 'ejo_testimonials_other_settings', array() );

		//* Linktext
		$read_more_text = (isset($ejo_testimonials_other_settings['linktext'])) ? $ejo_testimonials_other_settings['linktext'] : 'Lees Meer';

		//* Add read more link if not on testimonial page
		if( is_singular( self::$post_type ) )
			return false;
		
		//* Set html tag used for link. Default is p.
		$html_tag = apply_filters( 'ejo_testimonials_link_tag', 'p' );
		
		//* Process link
		$link = sprintf( "<a class='%s' href='%s'>%s</a>", 'button', get_permalink( $post_id ), $read_more_text );

		//* Wrap link in specified html tag
		$link = sprintf( "<{$html_tag} class='%s'>%s</{$html_tag}>", 'link', $link );

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

EJO_Testimonials::instance();
