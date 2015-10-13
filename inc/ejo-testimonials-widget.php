<?php

class EJO_Testimonials_Widget extends WP_Widget {

	//* Constructor. Set the default widget options and create widget.
	function __construct() 
	{
		//* Widget Title
		$widget_title = 'EJO Testimonials Widget';

		//* Widget Description
		$widget_info = array(
			'description' => 'Show Testimonials',
			'classname'   => 'ejo-testimonials-widget',
		);

		//* Setup Widget
		parent::__construct( 'ejo-testimonials-widget', $widget_title, $widget_info );

		//* Add scripts to settings page
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_testimonials_widget_scripts_and_styles' ) ); 

		//* Add Testimonial print to custom widget hook
		add_action( 'ejo_testimonials_widget_loop', array( $this, 'ejo_testimonials_widget_do_testimonial' ) );
	}
	
	//* Echo the widget content.
	function widget( $args, $instance ) 
	{
		//* Get filtered title
		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		//* Open widget
		echo $args['before_widget'];

		//* Show title
		echo (!empty($instance['title'])) ? $args['before_title'] . $instance['title'] . $args['after_title'] : '';

		//* Query args to get testimonials
		$query_args = array(
			'orderby' => $instance['sort'],
			'posts_per_page' => $instance['count'],
			'post_type' => EJO_Testimonials::$post_type,
		);

		//* Get testimonial posts
		$testimonials = new WP_Query($query_args);

		//* Loop through Testimonials
		if ( $testimonials->have_posts() ) : // Check if testimonials available ?>

			<?php while ( $testimonials->have_posts() ) : // Loop through testimonials ?>

				<?php $testimonials->the_post(); // Loads the post data ?>

				<?php do_action( 'ejo_testimonials_widget_loop' ); // Hook for printing testimonial ?>

			<?php endwhile; ?>

		<?php else : ?>

			<?php /* No testimonials */ ?>

		<?php endif; 

		//* Restore original Post Data 
		wp_reset_postdata();

		//* Close widget
		echo $args['after_widget'];
	}

	//* Update a particular instance.
	function update( $new_instance, $old_instance ) {

		$new_instance['title'] = strip_tags( $new_instance['title'] );

		if(!empty($new_instance['view_settings']))
			$new_instance['view_settings'] = EJO_Testimonials_Settings::testimonials_template_settings_checkbox_fix($new_instance['view_settings']);
	
		// write_log($new_instance);

		return $new_instance;
	}

	//* Echo the settings update form.
	function form( $instance ) 
	{
		$title = isset( $instance['title'] ) ? $instance['title'] : '';

		?>		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
		</p>

		<ul>
			<li>
				<label for="testimonials-number">Aantal referenties: </label>
				<select id="testimonials-number" name="<?php echo $this->get_field_name('count'); ?>">
					<?php 
						for ( $i=1; $i<=10; $i++ ){
							$selected = ( isset($instance['count']) && $i == $instance['count'] ) ? 'selected="selected"': '';
							echo "<option value='{$i}' {$selected}>{$i}</option>";
						}
					?>
				</select>
			</li>
			<li>
				<?php

				?>
				<label for="testimonials-sort">Sortering: </label>
				<select id="testimonials-sort" name="<?php echo $this->get_field_name('sort'); ?>">
					<?php 
						$sort_options = array(
							'rand' => 'Random',
							'date' => 'Nieuw - Oud',
						);
						foreach ( $sort_options as $key => $label ){
							$selected = ( isset($instance['sort']) && $key == $instance['sort'] ) ? 'selected="selected"': '';
							echo "<option value='{$key}' {$selected}>{$label}</option>";
						}
					?>
				</select>
			</li>
		</ul>
		<?php
		
	}

	public function admin_testimonials_widget_scripts_and_styles($hook)
	{
		if( $hook != 'widgets.php' ) 
			return;

		//* Settings page javascript
		wp_enqueue_script( EJO_Testimonials::$slug."-admin-widget-js", EJO_Testimonials::$uri ."js/admin-widget.js", array('jquery') );

		//* Settings page stylesheet
		wp_enqueue_style( EJO_Testimonials::$slug."-admin-widget-css", EJO_Testimonials::$uri ."css/admin-widget.css" );
	}

	public function get_testimonial( $post_id )
	{
		$title 	 = EJO_Testimonials::get_testimonial_title($post_id); //* Get title of testimonial
		$image   = EJO_Testimonials::get_testimonial_image($post_id); //* Store image
		$content = EJO_Testimonials::get_testimonial_content($post_id); //* Store content
		$author  = EJO_Testimonials::get_testimonial_author($post_id); //* Get testimonial author
		$info	 = EJO_Testimonials::get_testimonial_info($post_id); //* Get testimonial info
		$date 	 = EJO_Testimonials::get_testimonial_date($post_id); //* Get testimonial date
		$company = EJO_Testimonials::get_testimonial_company($post_id); //* Get testimonial company
		$link 	 = EJO_Testimonials::get_testimonial_link($post_id); //* Store link of testimonial

		$title_output	= '<h4 class="entry-title">' . $title . '</h4>';
		$image_output	= $image;
		$content_output	= '<blockquote>' . $content . '</blockquote>';
		$author_output	= !empty($author) ? '<span class="author">' . $author . '</span>' : '';
		$info_output	= !empty($info) ? '<span class="info">' . $info . '</span>' : '';
		$date_output	= !empty($date) ? '<span class="date">' . $date . '</span>' : '';
		$company_output	= !empty($company) ? '<span class="company">' . $company . '</span>' : '';
		$link_output	= $link;

		//* Output
		$output  = $title_output . $image_output . $content_output . $author_output . $info_output . $date_output . $company_output . $link_output . "\n\n";

		return apply_filters( 'ejo_testimonials_widget', $output, $title_output, $image_output, $content_output, $author_output, $info_output, $date_output, $company_output, $link_output);
	}

	//* Print testimonial inside custom loop
	public function ejo_testimonials_widget_do_testimonial()
	{
		$title 	 = EJO_Testimonials::get_testimonial_title(); //* Get title of testimonial
		$image   = EJO_Testimonials::get_testimonial_image(); //* Get image
		$content = EJO_Testimonials::get_testimonial_content(); //* Get content
		$author  = EJO_Testimonials::get_testimonial_author(); //* Get testimonial author
		$info	 = EJO_Testimonials::get_testimonial_info(); //* Get testimonial info
		$date 	 = EJO_Testimonials::get_testimonial_date(); //* Get testimonial date
		$company = EJO_Testimonials::get_testimonial_company(); //* Get testimonial company
		$link 	 = EJO_Testimonials::get_testimonial_link(); //* Get link of testimonial

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

	/**
     * Tell WP we want to use this widget.
     *
     * @wp-hook widgets_init
     * @return void
     */
    public static function register()
    {
        register_widget( __CLASS__ );
    }
}
