<?php
/**
 * WooCommerce Payson Gateway
 * By Niklas Högefjord (niklas@krokedil.se)
 * Based on PayPal Standard Gateway by WooCommerce
 * 
 * Uninstall - removes all Payson options from DB when user deletes the plugin via WordPress backend.
 * @since 0.3
 **/
 
if ( !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}
	delete_option( 'woocommerce_payson_settings' );
	delete_option( 'woocommerce_payson_invoice_settings' );
		
?>