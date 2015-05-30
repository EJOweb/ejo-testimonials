<?php

/**
 *
 */
class EJO_Testimonials_Metabox 
{
	//* Holds the instance of this class.
	private static $instance;

	//* Plugin setup.
	public function __construct() 
	{
		//* Add Referentie Metabox
		add_action( "add_meta_boxes_".EJO_Testimonials::$post_type, array( $this, 'add_testimonials_metabox' ) );

		//* Save Referentie Metadata
		add_action( 'save_post', array( $this, 'save_testimonial_metadata' ) );
	}

	//*
	public function add_testimonials_metabox() 
	{
		add_meta_box( 
			EJO_Testimonials::$post_type. '_metabox', 
			'Referentie Informatie', 
			array( $this, 'render_testimonials_metabox' ), 
			EJO_Testimonials::$post_type, 
			'normal', 
			'high' 
		);
	}

	//*
	public function render_testimonials_metabox( $post )
	{
		// Noncename needed to verify where the data originated
		wp_nonce_field( EJO_Testimonials::$slug.'-metabox-' . $post->ID, EJO_Testimonials::$slug.'-meta-nonce' );

		//* Meta key
		$meta_key = 'ejo_testimonials_data';

		$default_testimonial = array(
			'company' => '',
			'author' => '',
			'date'   => '',
			'info' 	 => '',
			'url'    => '',
		);
		$testimonial = get_post_meta( $post->ID, $meta_key, true );
		$testimonial = wp_parse_args( $testimonial, $default_testimonial );
	?>
		<table class="form-table">
			<tr>
				<th scope="row" style="width: 140px">
					<label for="testimonial-company">Bedrijf</label>
				</th>
				<td>
					<input
						id="testimonial-company"
						value="<?php echo $testimonial['company']; ?>"
						type="text"
						name="<?php echo EJO_Testimonials::$slug; ?>[company]"
						class="text large-text"
					>
					<!-- <span class="description">Wanneer de referentie-titel niet de auteur is.</span> -->
				</td>
			</tr>
			<tr>
				<th scope="row" style="width: 140px">
					<label for="testimonial-author">Persoon</label>
				</th>
				<td>
					<input
						id="testimonial-author"
						value="<?php echo $testimonial['author']; ?>"
						type="text"
						name="<?php echo EJO_Testimonials::$slug; ?>[author]"
						class="text large-text"
					>
					<!-- <span class="description">Wanneer de referentie-titel niet de auteur is.</span> -->
				</td>
			</tr>
			<tr>
				<th scope="row" style="width: 140px">
					<label for="testimonial-date">Datum</label>
				</th>
				<td>
					<input
						id="testimonial-date"
						value="<?php echo $testimonial['date']; ?>"
						type="text"
						name="<?php echo EJO_Testimonials::$slug; ?>[date]"
						class="text large-text"
					>
					<span class="description"></span>
				</td>
			</tr>
			<tr>
				<th scope="row" style="width: 140px">
					<label for="testimonial-info">Extra info</label>
				</th>
				<td>
					<input
						id="testimonial-info"
						value="<?php echo $testimonial['info']; ?>"
						type="text"
						name="<?php echo EJO_Testimonials::$slug; ?>[info]"
						class="text large-text"
					>
					<span class="description"></span>
				</td>
			</tr>
			<tr>
				<th scope="row" style="width: 140px">
					<label for="testimonial-url">URL</label>
				</th>
				<td>
					<input
						id="testimonial-url"
						value="<?php echo $testimonial['url']; ?>"
						type="text"
						name="<?php echo EJO_Testimonials::$slug; ?>[url]"
						class="text large-text"
					>
				</td>
			</tr>
		</table>
		<?php	
	}

	// Manage saving Metabox Data
	public function save_testimonial_metadata($post_id) 
	{
		//* Don't try to save the data under autosave, ajax, or future post.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			return;
		if ( defined( 'DOING_CRON' ) && DOING_CRON )
			return;

		//* Don't save if WP is creating a revision (same as DOING_AUTOSAVE?)
		if ( wp_is_post_revision( $post_id ) )
			return;

		//* Check that the user is allowed to edit the post
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;

		// Verify where the data originated
		if ( !isset($_POST[EJO_Testimonials::$slug."-meta-nonce"]) || !wp_verify_nonce( $_POST[EJO_Testimonials::$slug."-meta-nonce"], EJO_Testimonials::$slug."-metabox-" . $post_id ) )
			return;

		$meta_key = 'ejo_testimonials_data';

		if ( isset( $_POST[EJO_Testimonials::$slug] ) )
			update_post_meta( $post_id, $meta_key, $_POST[EJO_Testimonials::$slug] );
	}

	//* Returns the instance.
	public static function init() 
	{
		if ( !self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}