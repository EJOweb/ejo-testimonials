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
		);

		//* Setup Widget
		parent::__construct( 'ejo-testimonials-widget', $widget_title, $widget_info );

		//* Add scripts to settings page
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_testimonials_widget_scripts_and_styles' ) ); 
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

		//* Add filters for widgets
		// add_filter( 'ejo_testimonials_title_tag', function() { return apply_filters( 'ejo_testimonials_widget_title_tag', 'h3' ); } );
		// add_filter( 'ejo_testimonials_image_size', function() { return apply_filters( 'ejo_testimonials_widget_image_size', 'medium' ); } );
		// add_filter( 'ejo_testimonials_author_tag', function() { return apply_filters( 'ejo_testimonials_widget_author_tag', 'span' ); } );
		// add_filter( 'ejo_testimonials_info_tag', function() { return apply_filters( 'ejo_testimonials_widget_info_tag', 'span' ); } );
		// add_filter( 'ejo_testimonials_date_tag', function() { return apply_filters( 'ejo_testimonials_widget_date_tag', 'span' ); } );
		// add_filter( 'ejo_testimonials_company_tag', function() { return apply_filters( 'ejo_testimonials_widget_company_tag', 'span' ); } );

		if ( $testimonials->have_posts() ) : // Check if testimonials available ?>

			<div class="testimonials-container">

			<?php while ( $testimonials->have_posts() ) : // Loop through testimonials ?>

				<?php $testimonials->the_post(); // Loads the post data ?>

				<div class="testimonial">

					<?php echo EJO_Testimonials::the_testimonial( get_the_ID() ); // print testimonial ?>

				</div>
			
			<?php endwhile; ?>

			</div>

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
