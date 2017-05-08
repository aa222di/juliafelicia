<?php
global $wp_version;
/**
 * Get which version of GD is installed, if any.
 *
 * Returns the version (1 or 2) of the GD extension.
 */
function foogallery_gdversion() {
	if ( ! extension_loaded( 'gd' ) ) {
		return '0';
	}

	// Use the gd_info() function if possible.
	if ( function_exists( 'gd_info' ) ) {
		$ver_info = gd_info();
		preg_match( '/\d/', $ver_info['GD Version'], $match );

		return $match[0];
	}
	// If phpinfo() is disabled use a specified / fail-safe choice...
	if ( preg_match( '/phpinfo/', ini_get( 'disable_functions' ) ) ) {
		return '?';
	}
	// ...otherwise use phpinfo().
	ob_start();
	phpinfo( 8 );
	$info = ob_get_contents();
	ob_end_clean();
	$info = stristr( $info, 'gd version' );
	preg_match( '/\d/', $info, $match );

	return $match[0];
}

if ( current_user_can( 'activate_plugins' ) ) {
	$instance     = FooGallery_Plugin::get_instance();
	$info         = $instance->get_plugin_info();
	$title        = apply_filters( 'foogallery_admin_systeminfo_title', sprintf( __( '%s System Information', 'foogallery' ), foogallery_plugin_name() ) );
	$support_text = apply_filters( 'foogallery_admin_systeminfo_supporttext', sprintf( __( 'Below is some information about your server configuration. You can use this info to help debug issues you may have with %s.' ), foogallery_plugin_name() ) );
	$api          = new FooGallery_Extensions_API();
	//clear any extenasion cache
	$api->clear_cached_extensions();
	$extension_slugs = $api->get_all_slugs();

	//get all gallery templates
	$template_slugs = array();
	foreach ( foogallery_gallery_templates() as $template ) {
		$template_slugs[] = $template['slug'];
	}

	//get all activated plugins
	$plugins = array();
	foreach ( get_option('active_plugins') as $plugin_slug => $plugin ) {
		$plugins[] = $plugin;
	}

	$current_theme = wp_get_theme();

	$foogallery = FooGallery_Plugin::get_instance();
	$settings = $foogallery->options()->get_all();

	$debug_info = array(
		__( 'FooGallery version', 'foogallery' )  => $info['version'],
		__( 'WordPress version', 'foogallery' )   => $wp_version,
		__( 'Activated Theme', 'foogallery' )     => $current_theme['Name'],
		__( 'WordPress URL', 'foogallery' )       => get_site_url(),
		__( 'PHP version', 'foogallery' )         => phpversion(),
		__( 'PHP GD Loaded', 'foogallery' )       => extension_loaded( 'gd' ) && function_exists( 'gd_info' ) ? foogallery_gdversion() : __( 'Not found!', 'foogallery' ),
		__( 'Extensions Endpoint', 'foogallery' ) => $api->get_extensions_endpoint(),
		__( 'Extensions Errors', 'foogallery' )   => $api->has_extension_loading_errors() == true ? $api->get_extension_loading_errors_response() : __( 'Nope, all good', 'foogallery' ),
		__( 'Extensions', 'foogallery' )          => $extension_slugs,
		__( 'Extensions Active', 'foogallery' )   => array_keys( $api->get_active_extensions() ),
		__( 'Gallery Templates', 'foogallery' )   => $template_slugs,
		__( 'Lightboxes', 'foogallery' )          => apply_filters( 'foogallery_gallery_template_field_lightboxes', array() ),
		__( 'Settings', 'foogallery' )            => $settings,
		__( 'Active Plugins', 'foogallery' )      => $plugins,
		__( 'Server Mime Types', 'foogallery' )	  => wp_get_mime_types()
	);

	$debug_info = apply_filters( 'foogallery_admin_debug_info', $debug_info );
	?>
	<style>
		.foogallery-debug {
			width: 100%;
			font-family: "courier new";
			height: 500px;
		}
	</style>
	<div class="wrap about-wrap">
		<h1><?php echo $title; ?></h1>

		<div class="about-text">
			<?php echo $support_text; ?>
		</div>
    <textarea class="foogallery-debug">
<?php foreach ( $debug_info as $key => $value ) {
	echo $key . ' : ';
	print_r( $value );
	echo "\n";
} ?>
    </textarea>
	</div>
<?php }