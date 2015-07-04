<?php	
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