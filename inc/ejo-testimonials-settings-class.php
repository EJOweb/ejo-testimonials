<?php

class EJO_Testimonials_Settings 
{
	//* Holds the instance of this class.
	private static $instance;

	//* Store the post_type of this module
	public static $post_type;

	//* Store the slug of this module
	public static $slug;

	//* Stores the directory path for this module.
	public static $dir;

	//* Stores the directory URI for this module.
	public static $uri;

	//* Plugin setup.
	public function __construct() 
	{
		//* Connect to Testimonials Class
		self::$post_type = EJO_Testimonials::$post_type;
		self::$slug = EJO_Testimonials::$slug;
		self::$dir = EJO_Testimonials::$dir;
		self::$uri = EJO_Testimonials::$uri;

		//* Register Settings for Settings Page
		add_action( 'admin_init', array( $this, 'initialize_testimonials_settings' ) );

		//* Add Settings Page
		add_action( 'admin_menu', array( $this, 'add_testimonials_setting_menu' ) );

		//* Add scripts to settings page
		add_action( 'admin_enqueue_scripts', array( $this, 'add_testimonials_settings_scripts_and_styles' ) ); 
	}

	/***********************
	 * Settings Page
	 ***********************/

	//*
	public function add_testimonials_setting_menu()
	{
		add_submenu_page( 
			"edit.php?post_type=".self::$post_type, 
			'Referentie Instellingen', 
			'Instellingen', 
			'edit_theme_options', 
			'testimonials-settings', 
			array( $this, 'testimonials_settings' ) 
		);
	}

	//* Register settings
	public function initialize_testimonials_settings() 
	{
		// Add option if not already available
		if( false == get_option( 'testimonials_settings' ) ) {  
			add_option( 'testimonials_settings' );
		} 
	}

	//*
	public function testimonials_settings()
	{
	?>
		<div class='wrap' style="max-width:960px;">
			<h2>Referentie Instellingen</h2>

			<?php 
				// Save testimonials data
				if (isset($_POST['submit']) ) {

					if (!empty($_POST['ejo-testimonials-single-settings'])) {
						self::save_testimonials_settings("ejo_testimonials_single_settings", self::testimonials_template_settings_checkbox_fix($_POST['ejo-testimonials-single-settings'])); 
					}

					if (!empty($_POST['ejo-testimonials-archive-settings'])) {
						self::save_testimonials_settings("ejo_testimonials_archive_settings", self::testimonials_template_settings_checkbox_fix($_POST['ejo-testimonials-archive-settings'])); 
					}

					if (!empty($_POST['ejo-testimonials-other-settings'])) {
						update_option( "ejo_testimonials_other_settings", $_POST['ejo-testimonials-other-settings'] ); 
					}

					echo "<div class='updated'><p>Testimonial settings updated successfully.</p></div>";
					// echo "<pre>";print_r($_POST['ejo-testimonials-single-settings']);echo "</pre>";
				}
			?>

			<form action="<?php echo esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) ); ?>" method="post">
				<?php wp_nonce_field('testimonials-settings', 'testimonials-settings-nonce'); ?>

				<h2 class="nav-tab-wrapper" id="ejo-tabs">
					<a class='nav-tab' href='#single'>Single</a>
					<a class='nav-tab' href='#archive'>Archive</a>
					<a class='nav-tab' href='#other'>Overig</a>
				</h2>

				<div id="ejo-tabs-wrapper">
					<div class="tab-content" id="single">
						<?php self::show_testimonials_template_settings('single'); ?>
					</div>
					<div class="tab-content" id="archive">
						<?php self::show_testimonials_template_settings('archive'); ?>
					</div>
					<div class="tab-content" id="other">
						<?php self::show_testimonials_other_settings(); ?>
					</div>
				</div>

				<?php 
					submit_button( 'Wijzigingen opslaan' );
					// submit_button( 'Standaard Instellingen', 'secondary', 'reset' ); 
				?>
			
			</form>

		</div>
	<?php
	}

	public function show_testimonials_template_settings($template_type = '') 
    {
		$template_settings_default = array(
			'title' => array(
				'name' => 'Titel',
				'show' => true,
			),
			'image' => array(
				'name' => 'Afbeelding',
				'show' => true,
			),
			'content' => array(
				'name' => 'Referentie',
				'show' => true,
			),
			'company' => array(
				'name' => 'Bedrijf',
				'show' => true,
			),
			'author' => array(
				'name' => 'Auteur',
				'show' => true,
			),
			'info' => array(
				'name' => 'Extra Info',
				'show' => true,
			),
			'date' => array(
				'name' => 'Datum',
				'show' => true,
			),
			'link' => array(
				'name' => 'Link',
				'show' => true,
			),
		);

		$template_settings = array();

		//* Load template_settings for single testimonial or archive
		if (!empty($template_type))
			$template_settings = get_option("ejo_testimonials_{$template_type}_settings", array());

		write_log($template_settings);

		//* Special merge (+) with default to ensure all default testimonial-parts are shown
		$template_settings = $template_settings + $template_settings_default;

	?>
		<table class="form-table">
		<tbody>
	<?php
			foreach ($template_settings as $id => $field) {
	?>
				<tr>
					<td>
						<div class="ejo-move dashicons-before dashicons-sort"><br/></div>
					</td>
					<td>
						<?php 
							echo $field['name'];
							echo 
								"<input".
								" type='hidden'".
								" name='ejo-testimonials-{$template_type}-settings[{$id}][name]'".
								" value='$field[name]'".
								">";
						?>
					</td>
					<td>
						<?php
							echo 
								"<input".
								" type='checkbox'".
								" name='ejo-testimonials-{$template_type}-settings[{$id}][show]'".
								" id='ejo-testimonials-{$template_type}-settings-{$id}-show'".
								  checked($field['show'], true, false) .
								">";
							echo "<label for='ejo-testimonials-{$template_type}-settings-{$id}-show'>Tonen</label>";
						?>
					</td>
				</tr>
	<?php
			}
	?>						
		</tbody>
		</table>
	<?php
    }

    public function show_testimonials_other_settings() 
    {
    	//* Load settings
    	$ejo_testimonials_other_settings = get_option('ejo_testimonials_other_settings', array());

    	//* Linktext
		$linktext = (isset($ejo_testimonials_other_settings['linktext'])) ? $ejo_testimonials_other_settings['linktext'] : 'Lees Meer';

    	?>
    	<table class="form-table">
			<tbody>

				<tr>					
					<th scope="row" style="width: 140px">
						<label for="ejo-testimonials-other-settings-linktext">Linktekst</label>
					</th>
					<td>
						<input
							id="ejo-testimonials-other-settings-linktext"
							value="<?php echo $linktext; ?>"
							type="text"
							name="ejo-testimonials-other-settings[linktext]"
							class="text"
						>
						<span class="description">Tekst op de button wanneer er gelinkt wordt naar een referentie.</span>
					</td>
				</tr>
				
			</tbody>
		</table>
		<?php
    }

	public static function testimonials_template_settings_checkbox_fix($template_settings) 
    {
		foreach ($template_settings as $id => $field) {
			$template_settings[$id]['show'] = (isset($field['show'])) ? true : false;
		}

		return $template_settings;
	}

	//* Save testimonials settings
	public function save_testimonials_settings($option_name, $testimonials_settings)
	{
		// //* Check that the user is allowed to edit the options
		// if ( ! current_user_can( 'manage_options' ) ) {
		// 	echo "<div class='error'><p>Testimonial settings not updated.</p></div>";
		// 	return;
		// }

		// // Verify where the data originated
		// if ( !isset($_POST[self::$slug."-meta-nonce"]) || !wp_verify_nonce( $_POST[self::$slug."-meta-nonce"], self::$slug."-metabox-" . $post_id ) ) {
		// 	echo "<div class='error'><p>Testimonial settings not updated.</p></div>";
		// 	return;
		// }

		update_option( $option_name, $testimonials_settings);
	}

	//* Manage admin scripts and stylesheets
	public function add_testimonials_settings_scripts_and_styles()
	{
		//* Settings Page
		if (isset($_GET['page']) && $_GET['page'] == 'testimonials-settings') {
			//* Settings page javascript
			wp_enqueue_script(self::$slug."-admin-settings-page-js", self::$uri ."js/admin-settings-page.js", array('jquery'));

			//* Settings page stylesheet
			wp_enqueue_style( self::$slug."-admin-settings-page-css", self::$uri ."css/admin-settings-page.css" );
		}
	}

	//* Returns the instance.
	public static function init() 
	{
		if ( !self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}