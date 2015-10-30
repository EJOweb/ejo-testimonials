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

	//*
	public function testimonials_settings()
	{
	?>
		<div class='wrap' style="max-width:960px;">
			<h2>Referentie Instellingen</h2>

			<?php 
			// Save testimonials data
			if (isset($_POST['submit']) ) {

				if (!empty($_POST['ejo-testimonials-settings'])) {
					update_option( "ejo_testimonials_settings", $_POST['ejo-testimonials-settings'] ); 
				}

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

		//* Archive
		$archive = (isset($ejo_testimonials_settings['archive'])) ? $ejo_testimonials_settings['archive'] : 'testimonials';

    	?>
    	<table class="form-table">
			<tbody>

				<?php 
				/*
				<tr>					
					<th scope="row" style="width: 140px">
						<label for="ejo-testimonials-settings-linktext">Linktekst</label>
					</th>
					<td>
						<input
							id="ejo-testimonials-settings-linktext"
							value="<?php echo $linktext; ?>"
							type="text"
							name="ejo-testimonials-settings[linktext]"
							class="text"
						>
						<span class="description">Tekst op de button wanneer er gelinkt wordt naar een referentie.</span>
					</td>
				</tr>
				*/
				?>

				<tr>					
					<th scope="row" style="width: 140px">
						<label for="ejo-testimonials-settings-archive">Archief url</label>
					</th>
					<td>
						<input
							id="ejo-testimonials-settings-archive"
							value="<?php echo $archive; ?>"
							type="text"
							name="ejo-testimonials-settings[archive]"
							class="text"
						>
						<span class="description">slug voor archief</span>
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