<?php

class WC_Gateway_Payson_Invoice extends WC_Gateway_Payson {
	
	/**
     * Class for Payson Invoice payment.
     *
     */
     
     public function __construct() {	
		parent::__construct();
		$this->id				= 'payson_invoice';
		$this->method_title 	= __('Payson Invoice', 'payson');
		$this->icon 			= apply_filters( 'payson_invoice_icon', plugins_url(basename(dirname(__FILE__))."/images/payson.png") );
		$this->log 				= new WC_Logger();
		$this->application_id	= 'Krokedil';
		
		// Load the form fields.
		$this->init_form_fields();
		
		// Load the settings.
		$this->init_settings();
		
		// Define user set variables
		$this->enabled				= $this->settings['enabled'];
		$this->title 				= $this->settings['title'];
		$this->description  		= $this->settings['description'];
		$this->invoice_fee_id		= $this->settings['invoice_fee_id'];
		$this->language				= $this->settings['language'];
		$this->debug				= $this->settings['debug'];
		$this->instructions 		= ( isset( $this->settings['instructions'] ) ) ? $this->settings['instructions'] : '';
		$this->testmode				= ( isset( $this->settings['testmode'] ) ) ? $this->settings['testmode'] : '';
		$this->show_receipt_page	= ( isset( $this->settings['show_receipt_page'] ) ) ? $this->settings['show_receipt_page'] : '';
		
		// Apply filters so that we can modify these in other plugins
		$this->SellerEmail 			= apply_filters( 'payson_seller_email', $this->settings['SellerEmail'] );
		$this->AgentID 				= apply_filters( 'payson_agent_id', $this->settings['AgentID'] );
		$this->MD5	 				= apply_filters( 'payson_MD5', $this->settings['MD5'] );
		
		
		if ( $this->invoice_fee_id == "") $this->invoice_fee_id = 0;
		
		if ( $this->invoice_fee_id > 0 ) :
			
			$product = WC_Payson_Compatibility::wc_get_product( $this->invoice_fee_id );
	
			if ( $product ) :
			
				$this->invoice_fee_price 	= $product->get_price_including_tax();
				$this->invoice_fee_name 	= $product->get_title();
				
			else :
			
				$this->invoice_fee_price 	= 0;
				$this->invoice_fee_name 	= '';
							
			endif;
		
		else :
		
		$this->invoice_fee_price	= 0;
		$this->invoice_fee_name 	= '';
		
		endif;
		
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		
		add_action('woocommerce_receipt_payson_invoice', array(&$this, 'receipt_page'));
		
		add_action( 'valid-payson-request', array(&$this, 'successful_request') );
		add_action( 'woocommerce_api_wc_gateway_payson_invoice', array($this, 'check_payson_response') );
		add_action('wp_print_footer_scripts', array( $this, 'print_invoice_fee_updater'));
	}
	
		
		
	/**
	* Check if this gateway is enabled and available in the user's country
	*/
	
	function is_available() {
		global $woocommerce;
		
		if ($this->enabled=="yes") :
						
			// Currency check
			if (!in_array(get_option('woocommerce_currency'), array('SEK'))) return false;
			
			// Base country check
			//if (!in_array(get_option('woocommerce_default_country'), array('SE'))) return false;
			
			// Required fields check
			if (!$this->AgentID || !$this->MD5) return false;
			
			return true;
					
		endif;	
	
		return false;
	}
	
	
	/**
     * Initialise Gateway Settings Form Fields
     */
    function init_form_fields() {
    
    	$this->form_fields = array(
			'enabled' => array(
							'title' => __( 'Enable/Disable', 'payson' ), 
							'type' => 'checkbox', 
							'label' => __( 'Enable Payson Invoice', 'payson' ), 
							'default' => 'yes'
						), 
			'title' => array(
							'title' => __( 'Title', 'payson' ), 
							'type' => 'text', 
							'description' => __( 'This controls the title which the user sees during checkout.', 'payson' ), 
							'default' => __( 'Payson Invoice', 'payson' )
						),
			'description' => array(
							'title' => __( 'Description', 'payson' ), 
							'type' => 'textarea', 
							'description' => __( 'This controls the title which the user sees during checkout.', 'payson' ), 
							'default' => __( 'Payson Invoice - pay within 10 days.', 'payson' ),
						),
			'SellerEmail' => array(
							'title' => __( 'Payson email address', 'payson' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Payson email address; this is needed in order to take payment!', 'payson' ), 
							'default' => __( '', 'payson' )
						),
			'AgentID' => array(
							'title' => __( 'Agent ID', 'payson' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Payson Agent ID; this is needed in order to take payment!', 'payson' ), 
							'default' => __( '', 'payson' )
						),
			'MD5' => array(
							'title' => __( 'MD5', 'payson' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Payson MD5-Hash; this is needed in order to take payment!', 'payson' ), 
							'default' => __( '', 'payson' )
						),
			'language' => array(
							'title' => __( 'Language', 'payson' ), 
							'type' => 'select',
							'options' => array('SV'=>'Swedish', 'EN'=>'English', 'FI'=>'Finnish'),
							'description' => __( 'Locale of pages displayed by Payson during payment.', 'payson' ), 
							'default' => 'SV'
						),
			'instructions' => array(
							'title' => __( 'Instructions', 'payson' ), 
							'type' => 'textarea', 
							'description' => __( 'Instructions that will be added to the order emails.', 'payson' ), 
							'default' => ''
						),
			'invoice_fee_id' => array(
							'title' => __( 'Invoice Fee', 'payson' ), 
							'type' => 'text', 
							'description' => __( 'Create a hidden (simple) product that acts as the invoice fee. Enter the ID number in this textfield. The price must be in the range 0 to 30 SEK. Leave blank to disable. ', 'payson' ), 
							'default' => __( '', 'payson' )
						),
			'show_receipt_page' => array(
						'title' => __( 'Show Payson Receipt Page', 'payson' ), 
						'type' => 'checkbox', 
						'label' => __( 'Whether to show the receipt page in Paysons checkout after completed purchase.', 'payson' ), 
						'default' => 'yes'
					),
			'testmode' => array(
						'title' => __( 'Test Mode', 'payson' ), 
						'type' => 'checkbox', 
						'label' => __( 'Enable Payson Test Mode. Read more about the <a href="http://api.payson.se/#Testing" target="_blank">Payson test process here</a>.', 'payson' ), 
						'default' => 'no'
					),
			'debug' => array(
							'title' => __( 'Debug', 'payson' ), 
							'type' => 'checkbox', 
							'label' => __( 'Enable logging (<code>woocommerce/logs/payson.txt</code>)', 'payson' ), 
							'default' => 'no'
						)
			);
    
    } // End init_form_fields()
    
    
    
    
    /**
	 * Process the payment and return the result
	 **/
	function process_payment( $order_id ) {
		global $woocommerce;
		
		$order = WC_Payson_Compatibility::wc_get_order( $order_id );
		
		// Minimum order amount check
		if ( $order->order_total < 30 ) : 
			wc_add_notice(
				__( 'Order Total must be minimum 30 SEK to pay with Payson Invoice', 'payson' ),
				'error'
			);
			return false;
		endif;
		
		// Include Payson API library
		require_once(payson_LIB . 'paysonapi.php');
		

		
		/*
		 * Step 1: Set up details
		 */
			
		// ------------------ 
		// Set up
			 
		// Agent ID & MD5
		$credentials = new PaysonCredentials($this->AgentID, $this->MD5, $this->application_id);
		
		/* Change TRUE == Paysons test sytem, '' == Live environment  */
		if ( $this->testmode == 'yes' ) {	
			$payson_testmode = 'true';
		} else {
			$payson_testmode = '';
		}
		
		$api = new PaysonApi($credentials, $payson_testmode);
		
		
			
		// Common info
		$description = get_bloginfo('name') . ' order ' . ltrim( $order->get_order_number(), '#');
		$returnUrl = html_entity_decode( $this->get_return_url( $order ) );
		$cancelUrl = html_entity_decode( $order->get_cancel_order_url() );
		$ipnUrl = html_entity_decode( add_query_arg( 'wc-api', 'WC_Gateway_Payson_Invoice', trailingslashit( site_url() ) ) );
		
		// Customer details
		$sender = new Sender($order->billing_email, $order->billing_first_name, $order->billing_last_name);
		
		// Merchant email and Order amount
		$receiver = new Receiver(
			$this->SellerEmail, 	// The email of the account to receive the money
			$order->order_total 	// The amount you want to charge the user
		); 
		
		$receivers = array($receiver);
		
		// Tell Payson that this is an invoice
		$constraints = array(FundingConstraint::INVOICE);
		
		
		// ------------------
		// Collect everything 
		$payData = new PayData($returnUrl, $cancelUrl, $ipnUrl, $description, $sender, $receivers);
		
		// Invoice
		$payData->setFundingConstraints($constraints);
		
		// Show Receipt Page
		if($this->show_receipt_page == 'no') {
			$payData->setShowReceiptPage(FALSE);
		}
		
		// Cart Contents
		$orderItems = array();
		if (sizeof($order->get_items())>0) : foreach ($order->get_items() as $item) :
				
			$_product = $order->get_product_from_item($item);
				
			// Get SKU or product id
			if ( $_product->get_sku() ) {
				$tmp_sku = $_product->get_sku();
			} else {
				$tmp_sku = $_product->id;
			}
					
								
			if ($item['qty']) :
				
				// Change tax format from 25.00 to 0.25
				$item_tax_decimal = number_format( ( $order->get_item_tax($item, false) / $order->get_item_total( $item, false, false ) ), 2, '.', '');
						
				$temp_item = array(new OrderItem(
					$this->get_formatted_product_title( $item['name'] ), // Item name
					number_format($order->get_item_total( $item, false ), 2, '.', ''), // Price excl vat
					$item['qty'], // Quantity
					$item_tax_decimal, // Tax
					$tmp_sku // SKU
				));
			
			// Collect cart items in arrays
			$orderItems = array_merge($orderItems, $temp_item);
			
								
			endif;
		endforeach; 
		endif;
		
		
		// Shipping Cost
		if ($order->get_total_shipping()>0) :
			// Change tax format from 25.00 to 0.25
			//$shipping_tax_decimal = number_format( ( $order->order_shipping_tax ), 2, '.', '')/100;
			 $shipping_tax_decimal = $order->order_shipping_tax / $order->get_total_shipping();
			 
			$temp_item = array(new OrderItem(
					__('Shipping cost', 'payson'), // Name
					number_format( ( $order->get_total_shipping() ), 2, '.', ''), // Price
					'1', // Quantity
					number_format( ( $shipping_tax_decimal ), 2, '.', ''), // Tax
					'00' // SKU
			));
			
			
			// Collect cart items in arrays
			$orderItems = array_merge($orderItems, $temp_item);
			
		endif;
		
		
		// Fees
		if ( sizeof( $order->get_fees() ) > 0 ) {
			foreach ( $order->get_fees() as $item ) {
				
				// Invoice fee or regular fee
				if( $this->invoice_fee_name == $item['name'] ) {
					
					// Add the invoice fee to Payson
					$payData->setFeesPayer('PRIMARYRECEIVER');
					$payData->setInvoiceFee( $this->invoice_fee_price );
				
				} else {
					
					// Change tax format from 25.00 to 0.25
					// We manually calculate the tax percentage here
					if ($order->get_total_tax() >0) :
						$item_tax_decimal = number_format( ( $order->get_item_tax($item, false) / $order->get_item_total( $item, false, false ) ), 2, '.', '');
					else :
						$item_tax_decimal = 0.00;
					endif;		
				
					$temp_item = array(new OrderItem(
						$item['name'], // Item name
						number_format($order->get_item_total( $item, false ), 2, '.', ''), // Price excl vat
						1, // Quantity
						$item_tax_decimal, // Tax
						$tmp_sku // SKU
					));
			
					// Collect cart items in arrays
					$orderItems = array_merge($orderItems, $temp_item);
				
				}
		    
			}
		} // End fees
		
		
		
		// Add Cart Contents, Shipping & Discount
		$payData->setOrderItems($orderItems);
		
		// Order ID
		$payData->setTrackingId(ltrim( $order->get_order_number(), '#'));
		
		// Language
		$payData->setLocaleCode($this->language);
		
		// Currency
		$payData->setCurrencyCode(get_woocommerce_currency());
		
		/*
		echo '<pre>';
		print_r($payData);
		echo '</pre>';
		die();
		*/
		
		/*
		 * Step 2 initiate payment
		 */
		$payResponse = $api->pay($payData);
		if ($this->debug=='yes') $this->log->add( 'payson', 'Sending order details to Payson...' );
		
		/*
			* Step 3: verify that it suceeded
			*/
		if ($payResponse->getResponseEnvelope()->wasSuccessful()) {
			
			/*
 			 * Step 4: forward user
 			 */
 			
 			return array(
				'result' 	=> 'success',
				'redirect'	=> $api->getForwardPayUrl($payResponse)
			);
						
			
		} else {
			wc_add_notice(var_export($payResponse->getResponseEnvelope()->getErrors(), true), 'error');
		}
		
		

	}

    
    /**
	 * Payment form on checkout page
	 */
    function payment_fields() {
    	global $woocommerce;
    	
    	// Description
		if ($this->description) :	
			echo '<p>' . $this->description . '</p>';
		endif;

    }
    
    
    /**
	 * receipt_page
	 **/
	function receipt_page( $order ) {
		
		echo '<p>'.__('Thank you for your order.', 'payson').'</p>';
		
	}
	
	
	/**
	 * Check for Payson Response
	 **/
	function check_payson_response() {
			
		if (isset($_REQUEST['wc-api']) && $_REQUEST['wc-api'] == 'WC_Gateway_Payson_Invoice'):
			
        	$_POST = stripslashes_deep($_POST);
        	
        	header('HTTP/1.1 200 OK', true, 200);
        					
			if ($this->check_payson_request_is_valid()) :
				if ($this->debug=='yes') $this->log->add( 'payson', 'Payson IPN Request is valid.' );
				do_action("valid-payson-request", $_POST);
			else:
				if ($this->debug=='yes') :
    				$this->log->add( 'payson', ' Payson IPN Request Failure: ' . $order );
    				wp_die("Payson IPN Request Failure");
    			endif;
			endif;
       		
       	endif;
			
	} // End check_payson_response()
	
	
	
	
	/**
 	* Check Payson Response validity
 	**/
	function check_payson_request_is_valid() {
		global $woocommerce;
	
		if ($this->debug=='yes') $this->log->add( 'payson', 'Checking Payson response is valid...' );
		
		
		error_reporting(E_ALL);
		ini_set("display_errors", 1);

		require_once(payson_LIB . 'paysonapi.php');

		// Get the POST data
		$postData = file_get_contents("php://input");

		// Set up API
		$credentials = new PaysonCredentials($this->AgentID, $this->MD5);
		
		/* Change TRUE == Paysons test sytem, '' == Live environment  */
		if ( $this->testmode == 'yes' ) {
			
			$payson_testmode = 'true';
		} else {
			$payson_testmode = '';
		}
		
		$api = new PaysonApi($credentials, $payson_testmode);

		// Validate the request
		$response =  $api->validate($postData);
		$details = $response->getPaymentDetails();

		if($response->isVerified()) {
		    // IPN request is verified with Payson
			if ($this->debug=='yes') $this->log->add( 'payson', 'Received valid response from Payson. Details: ' . $details );
        	return true;

		} else {
		
			if ($this->debug=='yes') :
				 // Check details to find out what happened with the payment
		    	$this->log->add( 'payson', 'Error response: ' . $details );
		    endif;
			return false;
		}
				
	} // End check_payson_request_is_valid()
	


	/**
 	* Successful Payment!
 	* TODO - this function was earlier in the parent class with a joint callback listener.
 	* We will slim down this one since it only needs a check for direct payments.
 	**/
	function successful_request( $posted ) {
		global $woocommerce;
		
		if ($this->debug=='yes') :
			
			// For debug purposes
			$tmp_log = '';
			foreach ( $posted as $key => $value ) {
				$tmp_log .= $key . '=' . $value . "\r\n";
			}
		
    		$this->log->add( 'payson', 'Returning values from Payson: ' . $tmp_log );	
    	endif;
    	
		// trackingId holds post ID
	    if ( !empty($posted['trackingId']) ) {
	    
			$order_id 	  	= $this->get_order_id( $posted['trackingId'] );
			$order 			= WC_Payson_Compatibility::wc_get_order( $order_id );
			$order_key		= $order->order_key;
							
			if ($order->status !== 'completed') {

	    		// Update the order
									
				switch ( strtolower($posted['type']) ) :
					
					// Direct payment (credit card)	
					case 'transfer':
		    			
		    			if ( strtolower($posted['status']) == 'completed') {
		    			
	    					// Payment valid
	    					// Payson calls the IPN address multiple times. This check prevents multiple order notes to be created.. 
	    					if ($order->status !== 'processing') {
		    					$order->add_order_note( __('Payson Direct payment completed. Payson Transaction Reference: ', 'payson') . $posted['purchaseId'] );
		    					$order->payment_complete( $posted['purchaseId'] );
	    					}
	    				
	    				} elseif ( strtolower($posted['status']) == 'error' ) {
						
							// Payment failed
							$order->update_status('failed', sprintf(__('Payson Direct payment failed. Payson Transaction Reference: %s, Payment status: %s', 'payson'), $posted['purchaseId'], $posted['status'] ) );
	    				
	    				} else {
	    				
	    					// Unexpected status value
	    					$order->add_order_note( sprintf(__('Payson Direct payment, unexpected status value. Payson Transaction Reference: %s, Payment status: %s', 'payson'), $posted['purchaseId'], $posted['status'] ) );
	    				
	    				}
		    			
	    			break;
	    			
	    			
	    			// Invoice payment
					case 'invoice':
						
						if ( strtolower($posted['status']) == 'pending') {
							
							// Payment valid
							// Payson calls the IPN address multiple times. This check prevents multiple order notes to be created.. 
	    					if ($order->status !== 'processing') {
								$order->add_order_note( __('Payson Invoice created. Payson Transaction Reference: ', 'payson') . $posted['purchaseId'] );
		        				$order->payment_complete( $posted['purchaseId'] );
	        				}
	        				
	        			} elseif ( strtolower($posted['status']) == 'error' ) {
						
							// Payment failed
							$order->update_status('failed', sprintf(__('Payson Invoice payment failed. Payson Transaction Reference: %s, Payment status: %s', 'payson'), $posted['purchaseId'], $posted['status'] ) );
							
	        			} else {
	        			
	    					// Unexpected status value
	    					$order->add_order_note( sprintf(__('Payson Invoice payment, unexpected status value. Payson Transaction Reference: %s, Payment status: %s', 'payson'), $posted['purchaseId'], $posted['status'] ) );
	    				
	    				}
	    			
	    			break;
	    			
	    			
	    			// Guarantee payment (credit card)
	    			case 'guarantee':
	    				
	    				if ( strtolower($posted['status']) == 'pending') {
							
							// Payment valid
	        				// Mark as on-hold (we're awaiting the guarantee payment)
	        				// Payson calls the IPN address multiple times. This check prevents multiple order notes to be created.. 
	    					if ($order->status !== 'on-hold') {
	    					
								$order->update_status('on-hold', __('Payson Guarantee payment created. When the order is shipped you must visit your Payson account and confirm that the order has shipped. Payson Transaction Reference: ', 'payson') . $posted['purchaseId']);
	
								// Reduce stock levels
								$order->reduce_order_stock();
	
								// Remove cart
								$woocommerce->cart->empty_cart();
							}
							
						} elseif ( strtolower($posted['status']) == 'error' ) {
						
							// Payment failed
							$order->update_status('failed', sprintf(__('Payson Guarantee payment failed. Payson Transaction Reference: %s, Payment status: %s', 'payson'), $posted['purchaseId'], $posted['status'] ) );
						
						} else {
						
	    					// Unexpected status value
	    					$order->add_order_note( sprintf(__('Payson Guarantee payment, unexpected status value. Payson Transaction Reference: %s, Payment status: %s', 'payson'), $posted['purchaseId'], $posted['status'] ) );
	    				
	    				}
	    			
	    			break;	
	 
	    		endswitch;    	
		        	
			}

	    }
	
	} // End successful_request()
	
	
	/**
	 * print_invoice_fee_updater()
	 * Adds inline javascript in the footer that updates the totals on the checkout form when a payment method is selected.
	**/
	function print_invoice_fee_updater () {
		if ( is_checkout() && $this->enabled=="yes" && $this->invoice_fee_id > 0 ) {
		?>
			<script type="text/javascript">
				//<![CDATA[
				jQuery(document).ready(function($){
					$(document.body).on('change', 'input[name="payment_method"]', function() {
						$('body').trigger('update_checkout');
					});
				});
				//]]>
			</script>
			<?php
		}
	} // end print_invoice_fee_updater

	
	
	/**
	 * get_payson_invoice_fee_product()
	 * Helper function for getting the invoice fee ID.
	**/
	function get_payson_invoice_fee_product() {
		return $this->invoice_fee_id;
	}
	
	
	/**
	 * Get the order ID. Check to see if SON and SONP is enabled and
	 *
	 * @global type $wc_seq_order_number
	 * @global type $wc_seq_order_number_pro
	 * @param type $order_number
	 * @return type
	 */
	private function get_order_id( $order_number ) {

		// Get Order ID by order_number() if the Sequential Order Number plugin is installed
		if ( class_exists( 'WC_Seq_Order_Number' ) ) {

			global $wc_seq_order_number;

			$order_id = $wc_seq_order_number->find_order_by_order_number( $order_number );

			if ( 0 === $order_id ) {
				$order_id = $order_number;
			}
			
		// Get Order ID by order_number() if the Sequential Order Number Pro plugin is installed
		} elseif ( class_exists( 'WC_Seq_Order_Number_Pro' ) ) {
			
			$order_id = wc_seq_order_number_pro()->find_order_by_order_number( $order_number );

			if ( 0 === $order_id ) {
				$order_id = $order_number;
			}

		} else {
		
			$order_id = $order_number;
		}

		return $order_id;

	} // end function
	
	
	/**
	 * Get article name limited to max 128 characters (125 + 3 dots)
	 *
	 * @param type $order
	 * @return type string
	 */
	public function get_formatted_product_title( $title, $length = 125 ) {
		if (strlen($title) > $length) {
			$title = substr($title, 0, $length) . '...'; 
		}
		return $title;
	}
		
	    
} // End class WC_Gateway_Payson_Invoice



/**
 * Class WC_Gateway_Payson_Invoice_Extra
 * Extra class for functions that needs to be executed outside the payment gateway class.
 * Since version 1.4.3 (WooCommerce version 2.0)
**/

class WC_Gateway_Payson_Invoice_Extra {

	public function __construct() {
		
		global $woocommerce;
		$this->log = new WC_Logger();
		
		// Actions
		// Add Invoice fee via the new Fees API
		//add_action( 'woocommerce_checkout_process', array($this, 'payson_add_invoice_fee_process') );
		
		// Add Invoice fee via the new Fees API
		//add_action( 'woocommerce_before_calculate_totals', array( $this, 'calculate_totals' ), 10, 1 );
		// Add Invoice fee via the Fees API
		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'calculate_fees' ));
	}
	

	
	/**
	 * Calculate fees on checkout form.
	 **/ 
	public function calculate_fees( $cart ) {
		global $woocommerce;
    	
    	if(is_checkout() || defined('WOOCOMMERCE_CHECKOUT') ) {
			$available_gateways = $woocommerce->payment_gateways->get_available_payment_gateways();
		
			$current_gateway = '';
			if ( ! empty( $available_gateways ) ) {
				// Chosen Method
				if ( isset( $woocommerce->session->chosen_payment_method ) && isset( $available_gateways[ $woocommerce->session->chosen_payment_method ] ) ) {
					$current_gateway = $available_gateways[ $woocommerce->session->chosen_payment_method ];
				} elseif ( isset( $available_gateways[ get_option( 'woocommerce_default_gateway' ) ] ) ) {
            		$current_gateway = $available_gateways[ get_option( 'woocommerce_default_gateway' ) ];
				} else {
            		$current_gateway =  current( $available_gateways );
				}
			
			}
		
			if(is_object($current_gateway) && $current_gateway->id=='payson_invoice'){
        		$current_gateway_id = $current_gateway -> id;
				$this->add_fee_to_cart( $cart );
			}
		
		} // End if is checkout
	
	} // calculate_totals
	
	
	/**
	 * Add the fee to the cart if Payson is selected payment method and if a fee is used.
	 **/
	 function add_fee_to_cart( $cart ) {
		 global $woocommerce;
		 
		 $invoice_fee = new WC_Gateway_Payson_Invoice;
		 $this->invoice_fee_id = $invoice_fee->get_payson_invoice_fee_product();
		 
		 // Only run this if Klarna is the choosen payment method and this is WC +2.0
		 //if ($_POST['payment_method'] == 'klarna' && version_compare( WOOCOMMERCE_VERSION, '2.0', '>=' )) {
		 			 	
		 	if ( $this->invoice_fee_id > 0 ) {
		 		$product = WC_Payson_Compatibility::wc_get_product( $this->invoice_fee_id );
		 	
		 		if ( $product ) :
		 		
		 			// Is this a taxable product?
		 			if ( $product->is_taxable() ) {
			 			$product_tax = true;
			 		} else {
				 		$product_tax = false;
				 	}
    	   	 	
				 	$woocommerce->cart->add_fee($product->get_title(),$product->get_price_excluding_tax(),$product_tax,$product->get_tax_class());
				 	
    	    
				endif;
			} // End if invoice_fee_id > 0
		
		//}
	} // End function add_fee_to_cart
	
	
}
$wc_payson_invoice_extra = new WC_Gateway_Payson_Invoice_Extra;
?>