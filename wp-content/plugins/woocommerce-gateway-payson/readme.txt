=== WooCommerce Payson Gateway ===
Contributors: krokedil, niklashogefjord
Tags: ecommerce, e-commerce, woocommerce, payson
Requires at least: 3.8
Tested up to: 4.0
Requires WooCommerce at least: 2.1
Tested WooCommerce up to: 2.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

By Krokedil - http://krokedil.com/



== DESCRIPTION ==
Payson is a gateway plugin that extends WooCommerce, allowing you to take payments via Payson. Payson cooperates with Visa, MasterCard and the Swedish online banks Swedbank, Handelsbanken, SEB and Nordea. 

When the order goes through, the user is taken to Payson to make a secure payment. No SSL certificate is required on your site. After payment the order is confirmed and the user is taken to the thank you page.

The Payson plugin acts as two separate payment gateways, one for Payson Direct and one for Payson Invoice. 

PAYSON DIRECT
Direct Payments means that you easily can take credit card payments as well as direct payments via any of the associated banks. 

Direct Payments now supports Payson Guarantee (this can be turned on and off). Payson Guarantee means that the buyer will have the opportunity to inspect and approve or deny the merchandise before the payment is finally transferred to the seller.

PAYSON INVOICE
The Payson gateway also offers Payson Invoice - let your customers receive their order first and pay by invoice later.



== IMPORTANT NOTE ==
In order to use the Payson gateway you need a Payson merchant account and also request a AgentID and a MD5-hash from Payson. 

To utilize Payson Invoice you need to activate this feature in your Payson account. Contact Payson for further information about this.

Note that the plugin only works if the currency is set to Swedish Krona or Euros and the Base country is set to Sweden or Finland. To use the Payson Invoice feature, your store currency must be set to Swedish Krona.


== INSTALLATION	 ==
1. Download and unzip the latest release zip file.
2. If you use the WordPress plugin uploader to install this plugin skip to step 4.
3. Upload the entire plugin directory to your /wp-content/plugins/ directory.
4. Activate the plugin through the 'Plugins' menu in WordPress Administration.
5. Go WooCommerce Settings --> Payment Gateways and configure your Payson settings.

Documentation can be found at http://docs.woothemes.com/document/payson/.