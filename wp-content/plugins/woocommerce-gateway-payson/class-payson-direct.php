<?php

class WC_Gateway_Payson_Direct extends WC_Gateway_Payson {
	
	/**
     * Class for Payson Direct/Credit Card payment.
     *
     */
     
     public function __construct() {	
		parent::__construct();
		$this->id				= 'payson';
		$this->method_title 	= __('Payson Direct', 'payson');
		$this->icon 			= apply_filters( 'payson_direct_icon', plugins_url(basename(dirname(__FILE__))."/images/payson.png") );
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
		$this->guarantee			= $this->settings['guarantee'];
		$this->language				= $this->settings['language'];
		$this->debug				= $this->settings['debug'];
		$this->instructions 		= ( isset( $this->settings['instructions'] ) ) ? $this->settings['instructions'] : '';
		$this->show_receipt_page	= ( isset( $this->settings['show_receipt_page'] ) ) ? $this->settings['show_receipt_page'] : '';
		$this->testmode				= ( isset( $this->settings['testmode'] ) ) ? $this->settings['testmode'] : '';
		$this->send_order_total 	= ( isset( $this->settings['send_order_total'] ) ) ? $this->settings['send_order_total'] : '';
		
		// Apply filters so that we can modify these in other plugins
		$this->SellerEmail 			= apply_filters( 'payson_seller_email', $this->settings['SellerEmail'] );
		$this->AgentID 				= apply_filters( 'payson_agent_id', $this->settings['AgentID'] );
		$this->MD5	 				= apply_filters( 'payson_MD5', $this->settings['MD5'] );
		
				
		// Actions
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		add_action('woocommerce_receipt_payson_direct', array(&$this, 'receipt_page'));
		add_action( 'valid-payson-request', array(&$this, 'successful_request') );		
		add_action( 'woocommerce_api_wc_gateway_payson_direct', array($this, 'check_payson_response') );
			
	}
	
	/**
	* Check if this gateway is enabled and available in the user's country
	*/
	
	function is_available() {
		global $woocommerce;
		
		if ($this->enabled=="yes") :
			
			// Currency check
			if (!in_array(get_option('woocommerce_currency'), array('EUR', 'SEK'))) return false;
			
			// Base country check
			//if (!in_array(get_option('woocommerce_default_country'), array('FI', 'SE'))) return false;
			
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
							'label' => __( 'Enable Payson', 'payson' ), 
							'default' => 'yes'
						), 
			'title' => array(
							'title' => __( 'Title', 'payson' ), 
							'type' => 'text', 
							'description' => __( 'This controls the title which the user sees during checkout.', 'payson' ), 
							'default' => __( 'Payson', 'payson' )
						),
			'description' => array(
							'title' => __( 'Description', 'payson' ), 
							'type' => 'textarea', 
							'description' => __( 'This controls the title which the user sees during checkout.', 'payson' ), 
							'default' => __( 'Direct payment via Payson. Payson cooperates with Visa / Mastercard and the online banks Swedbank, Handelsbanken, SEB and Nordea.', 'payson' ),
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
			'send_order_total'       => array(
							'title'   => __( 'Send Order Total as one item', 'payson' ),
							'type'    => 'checkbox',
							'label'   => __( 'Send Order Total as one single item instead of each order row individually.', 'payson' ),
							'default' => 'no'
						),
			'guarantee' => array(
							'title' => __( 'Payson Guarantee', 'payson' ), 
							'type' => 'select',
							'options' => array('required'=>'Required', 'no'=>'No', 'optional'=>'Optional'),
							'description' => __( '<br/>Select whether Payson Guarantee is offered or not. More information about Payson Guarantee <a href="https://www.payson.se/tj%C3%A4nster/paysongaranti" target="_blank">can be found here</a>.', 'payson' ), 
							'default' => 'no'
						),
			'instructions' => array(
							'title' => __( 'Instructions', 'payson' ), 
							'type' => 'textarea', 
							'description' => __( 'Instructions that will be added to the order emails.', 'payson' ), 
							'default' => ''
						),
			'show_receipt_page' => array(
						'title' => __( 'Show Payson Receipt Page', 'payson' ), 
						'type' => 'checkbox', 
						'label' => __( 'Whether to show the receipt page in Paysons checkout after completed purchase.', 'payson' ), 
						'default' => 'yes'
					),
			'language' => array(
							'title' => __( 'Language', 'payson' ), 
							'type' => 'select',
							'options' => array('SV'=>'Swedish', 'EN'=>'English', 'FI'=>'Finnish'),
							'description' => __( 'Locale of pages displayed by Payson during payment.', 'payson' ), 
							'default' => 'SV'
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
		$ipnUrl = html_entity_decode( add_query_arg( 'wc-api', 'WC_Gateway_Payson_Direct', trailingslashit( site_url() ) ) );
		
		
		
		// Merchant email and Order amount
		$receiver = new Receiver(
			$this->SellerEmail, 	// The email of the account to receive the money
			$order->order_total	// The amount you want to charge the user, here in SEK (the default currency)
		); 
		
		$receivers = array($receiver);
		
		// Customer details
		$sender = new Sender($order->billing_email, $order->billing_first_name, $order->billing_last_name);
		
		
		// ------------------
		// Collect everything 
		$payData = new PayData($returnUrl, $cancelUrl, $ipnUrl, $description, $sender, $receivers);
		
		// Add Cart Contents, Shipping & Discount to Payson if selected in the plugin settings
		if ( 'no' == $this->send_order_total ) {
			$orderItems = $this->get_all_order_lines( $order );
			$payData->setOrderItems($orderItems);
		}
		
		// Order ID
		$payData->setTrackingId(ltrim( $order->get_order_number(), '#'));
		
		// Language
		$payData->setLocaleCode($this->language);
		
		// Show Receipt Page
		if($this->show_receipt_page == 'no') {
			$payData->setShowReceiptPage(FALSE);
		}
		
		// Currency
		$payData->setCurrencyCode(get_woocommerce_currency());

		// Set guarantee options
		if ( $this->guarantee == 'required' ):
			$payData->setGuaranteeOffered('REQUIRED');
		elseif ( $this->guarantee == 'no' ):
			$payData->setGuaranteeOffered('NO');
		else :
			$payData->setGuaranteeOffered('OPTIONAL');
		endif;
		
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
			
			if ($this->debug=='yes') $this->log->add( 'payson', 'Error response from Payson: ' . print_r($payResponse->getResponseEnvelope()->getErrors()) );
			$error = $payResponse->getResponseEnvelope()->getErrors();
			$message = __('Error Code ', 'payson') . $error[0]->getErrorId() . ' - ' . $error[0]->getMessage();
			wc_add_notice($message, 'error');
		}


	}


    /**
	 * Payment form on checkout page
	 */
    function payment_fields() {
    	global $woocommerce;
    	
    	if ($this->description) echo wpautop(wptexturize($this->description));
    	
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
			$this->log->add( 'payson', 'IPN callback');
		if (isset($_REQUEST['wc-api']) && $_REQUEST['wc-api'] == 'WC_Gateway_Payson_Direct'):
			
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
	 * Check for Payson Token 
	 **/
	function check_payson_token($posted) {
			
		if (isset($posted['TOKEN']) ) {
			
			error_reporting(E_ALL);
			ini_set("display_errors", 1);

			require_once(payson_LIB . 'paysonapi.php');

			// Set up API
			$credentials = new PaysonCredentials($this->AgentID, $this->MD5);
		
			/* Change TRUE == Paysons test sytem, FALSE == Live environment  */
			if ( $this->testmode == 'yes' ) {
			
				$payson_testmode = 'true';
			} else {
				$payson_testmode = '';
			}
			
			$api = new PaysonApi($credentials, $payson_testmode);
			$response = $api->paymentDetails(new PaymentDetailsData($posted['TOKEN']));
			$details = $response->getPaymentDetails();
			$status = $details->getStatus();
			
			$order = WC_Payson_Compatibility::wc_get_order( $this->get_order_id( $details->getTrackingId() ) );
			
			// Check if order is already set to processing or completed to avoid callbacks from two different orders in Payson that has got the same WC order ID 
			// (edge case where the purchase is failed the first time the customer tries to pay with Payson).
			if ( $order->status !== 'completed' && $order->status !== 'processing' ) {
				
				// Payment failed
				if ( $status == 'ERROR' ) {
					
					$order->update_status('failed', sprintf(__('Payson payment failed (triggered via token callback). Token: %s.', 'payson'), $posted['TOKEN'] ) );
				}
				
				// Payment valid
				if ( $status == 'COMPLETED' ) {
		
	    			// Payson calls the IPN address multiple times. This check prevents multiple order notes to be created.. 
		    		$order->add_order_note( __('Payson payment completed. Payson Transaction Reference: ', 'payson') . $details->getPurchaseId() );
		    		$order->payment_complete( $details->getPurchaseId() );
				}
				
				// Payment still processing by Payson
				if ( $status == 'PROCESSING' && $details->getType() !== 'INVOICE' ) {
					wc_add_notice( __( 'Your payment is being processed by Payson', 'payson' ), 'error' );
					$order->update_status('failed', sprintf(__('Redirected back to store while payment status is still set to PROCESSING in Paysons system.', 'payson'), $posted['TOKEN'] ) );
				}
				
				// Payment marked PENDING by Payson
				if ( $status == 'PENDING' && $details->getType() !== 'INVOICE' && $details->getType() !== 'GUARANTEE' ) {
					wc_add_notice( __( 'Something went wrong with the payment. Please, try a different payment method', 'payson' ), 'error' );
					$order->update_status('failed', sprintf(__('Redirected back to store while payment status is still set to PENDING in Paysons system.', 'payson'), $posted['TOKEN'] ) );
				}
				
				if ( $status == 'PENDING' && $details->getType() == 'GUARANTEE' ) {
					
					$order->update_status('on-hold', sprintf(__('Payson Guarantee payment created. When the order is shipped you must visit your Payson account and confirm that the order has shipped. Payson Transaction Reference: %s', 'payson'), $details->getPurchaseId() ) );
					
					// Reduce stock levels
					$order->reduce_order_stock();
		
					// Remove cart
					WC()->cart->empty_cart();
	
				}
			} // End if order status !== completed/processing
		}
		
	} // End check_payson_token()
	
	
	
	
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
		
		/* Change TRUE == Paysons test sytem, FALSE == Live environment  */
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
		
			// Error handling
			$error = $response->getResponseEnvelope()->getErrors();
			
			if ($this->debug=='yes') :
				 // Check details to find out what happened with the payment
		    	$this->log->add( 'payson', 'Error response: ' . $details );
		    	$this->log->add( 'payson', 'Error message: ' . $error );
		    	
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
			
			// Payson calls the IPN address multiple times. This check prevents multiple order notes to be created.
			// We also need this check to avoid callbacks from two different orders in Payson that has got the same WC order ID 
			// (edge case where the purchase is failed the first time the customer tries to pay with Payson).
			if ( $order->status !== 'completed' && $order->status !== 'processing' ) {

	    		// Update the order		
				switch ( strtolower($posted['type']) ) :
					
					// Direct payment (credit card)	
					case 'transfer':
		    			
		    			if ( strtolower($posted['status']) == 'completed') {
		    			
	    					// Payment valid
		    				$order->add_order_note( __('Payson Direct payment completed. Payson Transaction Reference: ', 'payson') . $posted['purchaseId'] );
		    				$order->payment_complete( $posted['purchaseId'] );
	    					
	    				
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
							$order->add_order_note( __('Payson Invoice created. Payson Transaction Reference: ', 'payson') . $posted['purchaseId'] );
		        			$order->payment_complete( $posted['purchaseId'] );
	        				
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
			
			$seq_onp = new WC_Seq_Order_Number_Pro();
			
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
	 * Get all order lines including shipping, fees and discount
	 *
	 * @param type $order
	 * @return type array
	 */
	function get_all_order_lines( $order ) {
		
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
		} // End fees
		
		return $orderItems;
	}
	
	
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
	    
} // End class woocommerce_payson_direct


/**
 *  Class for Payson callback, this because IPN sometimes fires after the buyer returns to the Thank you page. 
 *  This is not good if the transaction fails.
 *  @class 		WC_Gateway_Payson_Direct_Extra
 *  @since		1.4.6
 *
 **/

class WC_Gateway_Payson_Direct_Extra {
	
	public function __construct() {
		global $woocommerce;
		$this->log 			= new WC_Logger();
		
		// Actions
		add_action('init', array( $this, 'check_callback' ) );
	}
	
	/**
	* Check for Payson Response
	**/
	function check_callback() {
		
		// Check for and buyer-return-to-shop callback
		if ( isset($_REQUEST['key']) && isset($_REQUEST['TOKEN']) ) {
			$this->log->add( 'payson', 'Token Callback from Payson...' );
			$callback = new WC_Gateway_Payson_Direct;
			$callback->check_payson_token(stripslashes_deep($_REQUEST));
		} // End if
		
	} // End function check_callback()

} // End class WC_Gateway_Payson_Direct_Extra

$wc_gateway_payex_pm_extra = new WC_Gateway_Payson_Direct_Extra;
?>
