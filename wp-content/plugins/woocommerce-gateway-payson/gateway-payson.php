<?php
/*
Plugin Name: WooCommerce Payson Gateway
Plugin URI: http://woothemes.com/woocommerce
Description: Extends WooCommerce. Provides a <a href="http://www.payson.se" target="_blank">Payson</a> gateway for WooCommerce.
Version: 1.6.4
Author: Krokedil
Author URI: http://krokedil.com
*/

/*  Copyright 2012-2016  Krokedil Produktionsbyrå AB  (email : info@krokedil.se)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    
*/

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'f8e01b987491668ec43e18d6f6b7f11f', '18589' );

add_action('plugins_loaded', 'init_payson_gateway', 0);
		
function init_payson_gateway() {

	// If the WooCommerce payment gateway class is not available, do nothing
	if ( !class_exists( 'WC_Payment_Gateway' ) ) return;

	/**
	 * Localisation
	 */
	load_plugin_textdomain('payson', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');

	// Define payson API lib
	define('payson_LIB', dirname(__FILE__) . '/lib/');
	
	class WC_Gateway_Payson extends WC_Payment_Gateway {
			
		public function __construct() { 
			global $woocommerce;
			
			$this->shop_country	= get_option('woocommerce_default_country');
	
			// Actions
			
	    } 
    
		
			    
	    
		/**
		 * Admin Panel Options 
		 * - Options for bits like 'title' and availability on a country-by-country basis
		 *
		 * @since 1.0.0
		 */
		public function admin_options() {
	
	    	?>
	    	<h3><?php _e('Payson', 'payson'); ?></h3>
	    		
	    	<?php
	    	if (!in_array(get_option('woocommerce_currency'), array('EUR', 'SEK'))) {
    			?>
    			<tr valign="top">
				<th scope="row" class="titledesc">Payson disabled</th>
				<td class="forminp">
				<fieldset><legend class="screen-reader-text"><span>Payson disabled</span></legend>
				<?php _e('Choose Swedish Krona or Euro as currency in Pricing Options and Sweden or Finland as Base Country to enable the Payson Gateway.', 'payson'); ?><br>
				</fieldset></td>
				</tr>
    			<?php
			} else { 
			
				// Generate the HTML For the settings form.
				?>		
		    	<p><?php _e('Payson works by sending the user to <a href="http://www.payson.se">Payson</a> to enter their payment information. With Payson your customers can pay by credit card, via instant bank transactions and by invoice. Note that Payson will only take payments in Swedish Krona and Euros and must have Sweden or Finland set as Base Country. Payson Invoice only works if currency is set to Swedish Krona.', 'payson'); ?></p>
	    		<table class="form-table">
	    		<?php
	    		// Generate the HTML For the settings form.
	    		$this->generate_settings_html();
	    		?>
				</table><!--/.form-table-->
			
			<?php
	    	} // End check currency
	    
	    } // End admin_options()
	
		
	
	} // End class
	
	
	// Include the WooCommerce Compatibility Utility class
	// The purpose of this class is to provide a single point of compatibility functions for dealing with supporting multiple versions of WooCommerce (currently 2.1.x and 2.2)
	require_once 'classes/class-wc-payson-compatibility.php';
	
	// Include our Credit Card purchase class
	require_once 'class-payson-direct.php';
	
	// Include our Invoice purchase class
	require_once 'class-payson-invoice.php';
	
	// Include Payson order email instructions class
	require_once 'classes/class-wc-payson-email-instructions.php';


} // End init_payson_gateway

/**
 * Add the gateway to WooCommerce
 **/
function add_payson_gateway( $methods ) {
	$methods[] = 'WC_Gateway_Payson_Invoice';
	$methods[] = 'WC_Gateway_Payson_Direct'; 
	return $methods;
}

add_filter('woocommerce_payment_gateways', 'add_payson_gateway' );