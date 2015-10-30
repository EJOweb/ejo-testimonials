<?php

//* 29-10-2015

//* Print testimonial
public static function the_testimonial( $post_id = null )
{
	echo self::get_testimonial($post_id);
}

/** 
 * Get testimonial
 * 
 * Filter examples
 * add_filter( 'ejo_testimonials_widget_output', 'prefix_testimonials_widget_output', 10, 9);
 * function prefix_testimonials_widget_output( $output, $title_output, $image_output, $content_output, $author_output, $info_output, $date_output, $company_output, $link_output ) {}
 *
 *
 *
 *
 *
 */
public static function get_testimonial( $post_id = null )
{
	$title 	 = self::get_testimonial_title($post_id); //* Get title of testimonial
	$image   = self::get_testimonial_image($post_id); //* Store image
	$content = self::get_testimonial_content($post_id); //* Store content
	$author  = self::get_testimonial_author($post_id); //* Get testimonial author
	$info	 = self::get_testimonial_info($post_id); //* Get testimonial info
	$date 	 = self::get_testimonial_date($post_id); //* Get testimonial date
	$company = self::get_testimonial_company($post_id); //* Get testimonial company
	$link 	 = self::get_testimonial_link($post_id); //* Store link of testimonial

	$title_output   = 	sprintf( 
							apply_filters( 'ejo_testimonials_title_wrap', '<h4 class="entry-title">%s</h4>' ), 
							apply_filters( 'ejo_testimonials_title', $title )
						);

	$image_output   = 	sprintf( 
							apply_filters( 'ejo_testimonials_image_wrap', '%s' ),
							apply_filters( 'ejo_testimonials_image', $image )
						);
	$content_output = 	sprintf( 
							apply_filters( 'ejo_testimonials_content_wrap', '<blockquote>%s</blockquote>' ),
							apply_filters( 'ejo_testimonials_content', $content )
						);
	$author_output  = 	sprintf( 
							apply_filters( 'ejo_testimonials_author_wrap', '<span class="author">%s</span>' ),
							apply_filters( 'ejo_testimonials_author', $author )
						);
	$info_output    = 	sprintf( 
							apply_filters( 'ejo_testimonials_info_wrap', '<span class="info">%s</span>' ),
							apply_filters( 'ejo_testimonials_info',  $info)
						);
	$date_output    = 	sprintf( 
							apply_filters( 'ejo_testimonials_date_wrap', '<span class="date">%s</span>' ),
							apply_filters( 'ejo_testimonials_date', $date )
						);
	$company_output = 	sprintf( 
							apply_filters( 'ejo_testimonials_company_wrap', '<span class="company">%s</span>' ),
							apply_filters( 'ejo_testimonials_company', $company )
						);
	$link_output    = 	sprintf( 
							apply_filters( 'ejo_testimonials_link_wrap', '%s' ),
							apply_filters( 'ejo_testimonials_link', $link )
						);

	//* Order output
	$output =	$title_output .
				$image_output .
				$content_output .
				$author_output .
				$info_output .
				$date_output .
				$company_output .
				$link_output;

	//* Apply filter to maybe alter output order
	$output = apply_filters( 
				'ejo_testimonials_output', 
				$output, 
				$title_output, 
				$image_output, 
				$content_output, 
				$author_output, 
				$info_output, 
				$date_output, 
				$company_output, 
				$link_output 
			);		

	return $output;
}

//* OUDER

function ejo_testimonials_loop()
{
	//* Get testimonials settings (order, visibility)
	if (is_singular())
		$testimonials_view_settings = get_option('testimonials_single_settings');		
	else 
		$testimonials_view_settings = get_option('testimonials_archive_settings');

	//* Check if Genesis
	$theme = wp_get_theme();
	$genesis = ($theme->get( 'Template' ) == 'genesis') ? true : false;
	
	//* if no posts exist
	if ( !have_posts() ) {
		if ($genesis) { do_action( 'genesis_loop_else' ); }
		return;
	}

	// Start loop
	if ($genesis) { do_action( 'genesis_before_while' ); }

	while ( have_posts() ) : the_post();

		if ($genesis) { do_action( 'genesis_before_entry' ); }

		printf( '<article %s>', genesis_attr( 'entry' ) );

			$testimonial = EJO_Testimonials::get_testimonial( get_the_ID(), $testimonials_view_settings );

			foreach ($testimonial as $testimonial_part) {
				echo $testimonial_part;
			}

		echo '</article>';

		if ($genesis) { do_action( 'genesis_after_entry' ); }

	endwhile; //* end of one post

	if ($genesis) { do_action( 'genesis_after_endwhile' ); }
}

//* Get testimonial
function ejo_get_testimonial($post_id, $testimonials_settings)
{
	//* Get testimonial meta data
	$testimonial_data = get_post_meta( $post_id, 'ejo_testimonials_data', true );

	//* Keeper of the testimonial output
	$testimonial = array();

	//* Get testimonials info in right order
	foreach ($testimonials_settings as $id => $field) {

		//* Skip the fields which are to be hidden
		if ($field['show'] === false)
			continue;

		switch ($id) {
			case 'title':
				$title = get_the_title( $post_id );
				$heading = is_singular() ? 'h1' : 'h2';
				if( !is_singular() ) {
					$title = sprintf( '<a href="%s" rel="bookmark">%s</a>', get_permalink( $post_id ), $title );
				}
				$title = sprintf( "<{$heading} class='%s' itemprop='%s'>%s</{$heading}>", 'entry-title', 'headline', $title );
				$testimonial['title'] = $title;
				break;

			case 'image':
				$align = is_singular() ? 'alignright' : 'alignleft';
				$image = get_the_post_thumbnail( $post_id, 'medium', array( 'class' => $align ) );
				$testimonial['image'] = $image;
				break;
			
			case 'content':
				$quote = (is_singular()) ? get_the_content() : get_the_excerpt();
				$content = sprintf( '<blockquote>%s</blockquote>', $quote );
				if( !is_singular() ) {
					$content .= sprintf( '<p><a class="%s" href="%s">%s</a></p>', 'button', get_permalink( $post_id ), 'Lees meer' );
				}
				$testimonial['content'] = $content;
				break;
			
			case 'author':
				if ( isset($testimonial_data['author']) ) {
					$testimonial['author'] = '<span class="author">' . $testimonial_data['author'] . '</span>';
				}
				break;
			
			case 'info':
				if ( isset($testimonial_data['info']) ) {
					$info = $testimonial_data['info'];
					if ( is_singular() && isset($testimonial_data['url']) ) {
						$url = $testimonial_data['url'];
						$url = (strpos($url, "http://") === 0) ? $url : "http://{$url}"; //* Check if http://
						$info = sprintf( '<a href="%s" target="_blank">%s</a>', $url, $info );
					}
					$testimonial['info'] = '<span class="info">' . $info . '</span>';
				}
				break;
			
			case 'date':
				if ( isset($testimonial_data['date']) ) {
					$testimonial['date'] = '<span class="date">' . $testimonial_data['date'] . '</span>';
				}
				break;						
		}
	}

	return $testimonial;
}



//* Wrap testimonial title
public static function get_testimonial_title_wrapped( $post_id = null )
{
	//* Get title
	$title = self::get_testimonial_title($post_id);

	//* Add link to title if not in singular page
	if( !is_singular( self::$post_type ) ) 
		$title = sprintf( '<a href="%s" rel="bookmark">%s</a>', get_permalink( $post_id ), $title );
	
	//* Set html tag used for title. Default is h1 or h2.
	$html_tag = apply_filters( 'ejo_testimonials_title_tag', ( is_singular( self::$post_type ) ) ? 'h1' : 'h2' );

	//* Wrap title in heading
	$output = sprintf( "<{$html_tag} class='%s' itemprop='%s'>%s</{$html_tag}>", 'entry-title', 'headline', $title );

	return apply_filters( 'ejo_testimonials_title_wrap', $output, $title );
}

//* Wrap testimonial image
public static function get_testimonial_image_wrapped( $post_id = null )
{	
	//* Set html tag used for image. Default is thumbnail.
	$image_size = apply_filters( 'ejo_testimonials_image_size', 'thumbnail' );

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

	return apply_filters( 'ejo_testimonials_quote_wrap', $output, $content );
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


/* ================================ */

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
	$output  = $title . $image . $content . $author . $info . $date . $company . $link . "\n\n";

	return apply_filters( 'ejo_testimonials', $output, $title, $image, $content, $author, $info, $date, $company, $link);
}

/* ================================ */

//* Add filters for widgets
add_filter( 'ejo_testimonials_title_tag', function() { return apply_filters( 'ejo_testimonials_widget_title_wrap', 'h4' ); } );

//* Read More Text for widgets Filter
add_filter( 'ejo_testimonials_link_text', function($read_more) { 
	return apply_filters( 'ejo_testimonials_widget_link_text', $read_more ); 
} );

//* Link wrap for widgets Filter
add_filter( 'ejo_testimonials_link_wrap', function($output, $link) { 
	return apply_filters( 'ejo_testimonials_widget_link_wrap', $link ); 
}, 10, 2 );

//* Create new filter option for widget
add_filter( 'ejo_testimonials', function($output, $title, $image, $testimonial, $author, $info, $date, $company, $link) {

	//* Create new filter option for widget
	return apply_filters( 'ejo_testimonials_widget', $output, $title, $image, $testimonial, $author, $info, $date, $company, $link );
	
}, 10, 9);