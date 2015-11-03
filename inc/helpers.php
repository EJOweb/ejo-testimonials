<?php 

//* Get testimonial image
function ejo_get_testimonial_image( $post_id = null, $size = 'thumbnail', $attr = '' )
{	
	if (!has_post_thumbnail($post_id))
		return '<img src="'.EJO_TS_PLUGIN_URL.'images/unknown_person.jpg" title="referentie schrijverfoto onbekend" class="attachment-thumbnail wp-post-image">';
	else
		return get_the_post_thumbnail( $post_id, $size, $attr );
}

//* Get testimonial author
function ejo_get_testimonial_author( $post_id = null )
{
	//* If no post_id, get current post_id
	if ( empty($post_id) )
		$post_id = get_the_ID();

	//* Get testimonial info
	$testimonial_info = get_post_meta( $post_id, 'ejo_testimonials_info', true );

	//* Get testimonial author
	$author = (isset($testimonial_info['author'])) ? $testimonial_info['author'] : '';

	return $author;
}

//* Get testimonial info
function ejo_get_testimonial_info( $post_id = null )
{	
	//* If no post_id, get current post_id
	if ( empty($post_id) )
		$post_id = get_the_ID();

	//* Get testimonial info
	$testimonial_info = get_post_meta( $post_id, 'ejo_testimonials_info', true );

	//* Get testimonial info
	$info = (isset($testimonial_info['info'])) ? $testimonial_info['info'] : '';

	return $info;
}

//* Get testimonial date
function ejo_get_testimonial_date( $post_id = null )
{	
	//* If no post_id, get current post_id
	if ( empty($post_id) )
		$post_id = get_the_ID();

	//* Get testimonial info
	$testimonial_info = get_post_meta( $post_id, 'ejo_testimonials_info', true );

	//* Get testimonial date
	$date = (isset($testimonial_info['date'])) ? $testimonial_info['date'] : '';

	return $date;
}

//* Get testimonial company
function ejo_get_testimonial_company( $post_id = null )
{	
	//* If no post_id, get current post_id
	if ( empty($post_id) )
		$post_id = get_the_ID();

	//* Get testimonial info
	$testimonial_info = get_post_meta( $post_id, 'ejo_testimonials_info', true );

	//* Get testimonial company
	$company = (isset($testimonial_info['company'])) ? $testimonial_info['company'] : '';

	return $company;
}

//* Get testimonial link
function ejo_get_testimonial_external_url( $post_id = null )
{	
	//* If no post_id, get current post_id
	if ( empty($post_id) )
		$post_id = get_the_ID();

	//* Get testimonial info
	$testimonial_info = get_post_meta( $post_id, 'ejo_testimonials_info', true );

	//* Get testimonial url
	$url = (isset($testimonial_info['url'])) ? $testimonial_info['url'] : '';

	return $url;
}