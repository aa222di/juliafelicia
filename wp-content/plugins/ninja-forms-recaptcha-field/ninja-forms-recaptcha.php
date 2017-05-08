<?php
/*
Plugin Name: Ninja Forms reCAPTCHA
Description: Adds reCAPTCHA field to Ninja Forms.
Author: Aman Saini
Author URI: http://amansaini.me
Plugin URI: http://amansaini.me
Version: 1.2.5
Requires at least: 3.5
Tested up to: 4.3

 */

// don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );


add_action( 'plugins_loaded', array( 'Ninja_Forms_Recaptcha_Field', 'setup' ) );


class Ninja_Forms_Recaptcha_Field {

	//Plugin starting point.
	public static function setup() {
		if ( ! self::is_ninjaform_installed() ) {
			return;
		}
		$class = __CLASS__;
		new $class;
	}

	/**
	 * Add necessary hooks and filters functions
	 *
	 * @author Aman Saini
	 * @since  1.0
	 */
	function __construct() {


		//Register the Upload field
		add_action( 'init', array( $this, 'recaptcha_field_register' ) );

		// Add Recaptcha Settings submenu under Forms
		add_action( 'admin_menu', array( $this, 'register_recaptcha_submenu_page' ) );

		add_action( 'admin_init', array( $this, 'initialize_recaptcha_options' ) );

	}
	function recaptcha_field_register() {
		$args = array(
			'name' => 'reCAPTCHA',
			'display_function' => array( $this, 'ninja_forms_recaptcha_display' ),
			'group' => '',
			'edit_label' => true,
			'req' => true,
			'edit_label_pos' => true,
			'edit_req' => false,
			'edit_custom_class' => false,
			'edit_help' => false,
			'edit_meta' => false,
			'sidebar' => 'template_fields',
			'edit_conditional' => false,
			'process_field' => false,
			'pre_process' => array( $this, 'ninja_forms_field_recaptcha_pre_process' ),
		);
		ninja_forms_register_field( 'g_recaptcha', $args );

	}

	public function ninja_forms_recaptcha_display( $field_id, $data, $form_id = '' ) {

		$settings = get_option( 'nf_recaptcha_settings' );
		$randnumber = rand();
		$siteKey = $settings['site_key'];
		$lang = !empty( $settings['lang'] )?$settings['lang']:'en';
		if ( !empty( $siteKey ) ) { ?>

	<input id="ninja_forms_field_<?php echo $field_id;?>" name="ninja_forms_field_<?php echo $field_id;?>" type="hidden" class="<?php echo $field_class;?>" value="" rel="<?php echo $field_id;?>" />
		<div class="g-recaptcha" data-callback="recaptcha_set_value_<?php echo $randnumber; ?>" data-sitekey="<?php echo $siteKey; ?>"></div>
            <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?php echo $lang; ?>">
            </script>
            <script type="text/javascript">
            function recaptcha_set_value_<?php echo $randnumber; ?>(inpval){
            	jQuery("#ninja_forms_field_<?php echo $field_id;?>").val(inpval)

            }
            </script>

		<?php
		}else {
			echo 'Please enter Site key for Google reCAPTCHA';
		}
	}

	function ninja_forms_field_recaptcha_pre_process() {

		global $ninja_forms_processing;

		if ( !isset( $_POST['g-recaptcha-response'] ) ) {
			return;
		}

		$settings = get_option( 'nf_recaptcha_settings' );

		$url = 'https://www.google.com/recaptcha/api/siteverify?secret='.$settings['secret_key'].'&response='.$_POST['g-recaptcha-response'];

		$resp = wp_remote_get( $url, array( 'sslverify'=>true ) );

		if ( !is_wp_error( $resp ) ) {

			$body = wp_remote_retrieve_body( $resp );

			$response = json_decode( $body );

			if ( $response->success===false ) {

				if ( !empty( $response->{'error-codes'} ) && $response->{'error-codes'}[0]!='missing-input-response' ) {

					$error= __('Please check if you have entered Site & Secret key correctly','ninja-forms-recaptcha');

				}else {

					$error=  __('ReCaptchan validerar inte. Testa att fylla i den igen.','ninja-forms-recaptcha');

				}

				$ninja_forms_processing->add_error( 'error_recaptcha', $error );
			}

		}

	}


	function register_recaptcha_submenu_page() {
		add_submenu_page( 'ninja-forms', 'reCAPTCHA Settings', 'reCAPTCHA', 'manage_options', 'nf_recaptcha_settings', array( $this, 'nf_recaptcha_settings_callback' ) );
	}

	function nf_recaptcha_settings_callback() {
?>
		<!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">

        <!-- Make a call to the WordPress function for rendering errors when settings are saved. -->
        <?php settings_errors(); ?>

        <!-- Create the form that will be used to render our options -->
        <form method="post" action="options.php">
            <?php settings_fields( 'nf_recaptcha_settings' ); ?>
            <?php do_settings_sections( 'nf_recaptcha_settings' ); ?>
            <?php submit_button(); ?>
        </form>

    </div><!-- /.wrap -->
<?php
	}




	/**
	 * Register settings and fields
	 *
	 * @author Aman Saini
	 * @since  1.0
	 * @return [type] [description]
	 */
	function initialize_recaptcha_options() {

		// If settings don't exist, create them.
		if ( false == get_option( 'nf_recaptcha_settings' ) ) {
			add_option( 'nf_recaptcha_settings' );
		} // end if

		add_settings_section(
			'recaptcha_settings_section',         // ID used to identify this section and with which to register options
			'Ninja Forms reCAPTCHA Settings',                  // Title to be displayed on the administration page
			array( $this, 'recaptcha_settings_callback' ), // Callback used to render the description of the section
			'nf_recaptcha_settings'                           // Page on which to add this section of options
		);

		// Dwolla ID
		add_settings_field(
			'site_key',                      // ID used to identify the field throughout the theme
			'reCAPTCHA Site Key',                           // The label to the left of the option interface element
			array( $this, 'sitekey_callback' ),   // The name of the function responsible for rendering the option interface
			'nf_recaptcha_settings',                          // The page on which this option will be displayed
			'recaptcha_settings_section'

		);

		// Dwolla API Key
		add_settings_field(
			'secret_key',
			'reCAPTCHA Secret Key',
			array( $this, 'secret_callback' ),
			'nf_recaptcha_settings',
			'recaptcha_settings_section'

		);

		add_settings_field(
			'lang',
			'reCAPTCHA Language',
			array( $this, 'lang_callback' ),
			'nf_recaptcha_settings',
			'recaptcha_settings_section'

		);
		//register settings
		register_setting( 'nf_recaptcha_settings', 'nf_recaptcha_settings' );

	}

	function recaptcha_settings_callback() {

		echo '';
	}

	function sitekey_callback() {
		$options = get_option( 'nf_recaptcha_settings' );

		$site_key = !empty( $options['site_key'] )?$options['site_key']:'';

		// Render the output
		echo '<input type="text" class="regular-text" id="site_key" name="nf_recaptcha_settings[site_key]" value="' . $site_key. '" />';

	}

	function secret_callback() {
		$options = get_option( 'nf_recaptcha_settings' );
		$secret_key = !empty( $options['secret_key'] )?$options['secret_key']:'';

		// Render the output
		echo '<input type="text" class="regular-text" id="secret_key" name="nf_recaptcha_settings[secret_key]" value="' . $secret_key . '" />';

	}

function lang_callback() {
		$options = get_option( 'nf_recaptcha_settings' );
		$lang = !empty( $options['lang'] )?$options['lang']:'';

		// Render the output
		echo '<input type="text" class="regular-text" id="lang" name="nf_recaptcha_settings[lang]" value="' . $lang . '" />';
		echo '<p>You can find all language code <a href="https://developers.google.com/recaptcha/docs/language?hl=en" target="_blank">here</a></p>';
	}


	/*
	 * Check if Ninja form is  installed
	 */
	private static function is_ninjaform_installed() {
		return defined( 'NINJA_FORMS_VERSION' );
	}

}
