<?php

class EJO_Testimonials_Settings 
{
	//* Holds the instance of this class.
	private static $_instance;

	//* Returns the instance.
	public static function init() 
	{
		if ( !self::$_instance )
			self::$_instance = new self;
		return self::$_instance;
	}

	//* Plugin setup.
	public function __construct() 
	{
		//* Add Settings Page
		add_action( 'admin_menu', array( $this, 'add_testimonials_setting_menu' ) );

		//* Register Settings for Settings Page
		add_action( 'admin_init', array( $this, 'initialize_testimonials_settings' ) );

		//* Save settings (before init, because post type registers on init)
		//* I probably should be using Settings API..
		add_action( 'init', array( $this, 'save_testimonials_settings' ), 1 );

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
			"edit.php?post_type=".EJO_Testimonials::$post_type, 
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

	//* Save testimonials settings
	public function save_testimonials_settings()
	{
		if (isset($_POST['submit']) && !empty($_POST['ejo-testimonials-settings']) ) {

			//* Escape slug
			$_POST['ejo-testimonials-settings']['slug'] = sanitize_title( $_POST['ejo-testimonials-settings']['slug'] );

			//* Strip slashes
			$_POST['ejo-testimonials-settings']['description'] = stripslashes( $_POST['ejo-testimonials-settings']['description'] );

			//* Update settings
			update_option( "ejo_testimonials_settings", $_POST['ejo-testimonials-settings'] ); 				
		}
	}

	//*
	public function testimonials_settings()
	{
	?>
		<div class='wrap' style="max-width:960px;">
			<h2>Referentie Instellingen</h2>

			<?php 
			//* Let user know the settings are saved
			if (isset($_POST['submit']) && !empty($_POST['ejo-testimonials-settings']) ) {

				flush_rewrite_rules(); //* Flush rewrite rules because archive slug could have changed

				echo "<div class='updated'><p>Testimonial settings updated successfully.</p></div>";
			}
			?>

			<form action="<?php echo esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) ); ?>" method="post">
				<?php wp_nonce_field('testimonials-settings', 'testimonials-settings-nonce'); ?>

				<?php self::show_testimonials_settings(); ?>

				<?php submit_button( 'Wijzigingen opslaan' ); ?>
				<?php // submit_button( 'Standaard Instellingen', 'secondary', 'reset' ); ?>

			</form>

		</div>
	<?php
	}


    public function show_testimonials_settings() 
    {
    	//* Load settings
    	$ejo_testimonials_settings = get_option('ejo_testimonials_settings', array());

    	//* Linktext
		// $linktext = (isset($ejo_testimonials_settings['linktext'])) ? $ejo_testimonials_settings['linktext'] : 'Lees Meer';

		//* Archive title
		$title = (!empty($ejo_testimonials_settings['title'])) ? $ejo_testimonials_settings['title'] : EJO_Testimonials::$post_type_name;

		//* Archive description
		$description = (!empty($ejo_testimonials_settings['description'])) ? $ejo_testimonials_settings['description'] : '';

		//* Archive slug
		$slug = (!empty($ejo_testimonials_settings['slug'])) ? $ejo_testimonials_settings['slug'] : EJO_Testimonials::$slug;
		
    	?>
    	<table class="form-table">
			<tbody>

				<tr>					
					<th scope="row">
						<label for="ejo-testimonials-settings-title">Title</label>
					</th>
					<td>
						<input
							id="ejo-testimonials-settings-title"
							value="<?php echo $title; ?>"
							type="text"
							name="ejo-testimonials-settings[title]"
							class="text"
							style="width"
						>
						<p class="description">Wordt getoond op de archiefpagina, breadcrumbs en meta's tenzij anders aangegeven</p>
					</td>
				</tr>

				<tr>					
					<th scope="row">
						<label for="ejo-testimonials-settings-description">Beschrijving</label>
					</th>
					<td>
						<textarea
							id="ejo-testimonials-settings-description"
							name="ejo-testimonials-settings[description]"
							class="text"
						><?php echo $description; ?></textarea>
						<p class="description">De beschrijving kan getoond worden op de archiefpagina (afhankelijk van het thema)</p>
					</td>
				</tr>

				<tr>					
					<th scope="row">
						<label for="ejo-testimonials-settings-slug">Slug</label>
					</th>
					<td>
						<input
							id="ejo-testimonials-settings-slug"
							value="<?php echo $slug; ?>"
							type="text"
							name="ejo-testimonials-settings[slug]"
							class="text"
							style="width"
						>
						<p class="description">Bepaalt de url van de archiefpagina</p>
					</td>
				</tr>
				
			</tbody>
		</table>
		<?php
    }

	//* Manage admin scripts and stylesheets
	public function add_testimonials_settings_scripts_and_styles()
	{
		//* Settings Page
		if (isset($_GET['page']) && $_GET['page'] == 'testimonials-settings') {
			//* Settings page javascript
			wp_enqueue_script(EJO_Testimonials::$slug."-admin-settings-page-js", EJO_TS_PLUGIN_URL ."js/admin-settings-page.js", array('jquery'));

			//* Settings page stylesheet
			wp_enqueue_style( EJO_Testimonials::$slug."-admin-settings-page-css", EJO_TS_PLUGIN_URL ."css/admin-settings-page.css" );
		}
	}
}