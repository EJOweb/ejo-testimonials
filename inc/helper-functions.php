<?php
/* 
Not in use anymore!
*/

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

