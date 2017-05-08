<?php
/**
 * Helper class for Klarna KPM
 *
 * @link http://www.woothemes.com/products/payson-form/
 * @since 1.6
 *
 * @package WC_Gateway_Payson
 */
 
class WC_Gateway_Payson_Email_Instructions {
	
	public function __construct() {
		
		// Instructions in Order Emails
    	add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
	}
	
	/**
     * Add Instructions to the WC emails.
     *
     * @access public
     * @param WC_Order $order
     * @param bool $sent_to_admin
     * @param bool $plain_text
     * @return void
     */
    public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		
		// Invoice payment method
    	if ( ! $sent_to_admin && 'payson_invoice' === $order->payment_method ) {
	    	$settings = get_option( 'woocommerce_payson_invoice_settings' );
			if ( isset( $settings['instructions'] ) ) {
				echo wpautop( wptexturize( $settings['instructions'] ) );
			}
		}
		
		// Card payment method
    	if ( ! $sent_to_admin && 'payson' === $order->payment_method ) {
	    	$settings = get_option( 'woocommerce_payson_settings' );
			if ( isset( $settings['instructions'] ) ) {
				echo wpautop( wptexturize( $settings['instructions'] ) );
			}
		}
    } // End function
}

$wc_payson_email_instructions = new WC_Gateway_Payson_Email_Instructions;