=== Plugin Name ===
Contributors: terrytsang
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=terry@terrytsang.com&item_name=Donation+for+TerryTsang+Wordpress+WebDev
Plugin Name: WooCommerce Facebook Share Like Button
Plugin URI:  http://terrytsang.com/shop/shop/woocommerce-facebook-share-like-button/
Tags: woocommerce, facebook, e-commerce, like
Requires at least: 3.8
Tested up to: 4.5.3
Stable tag: 2.2.3
Version: 2.2.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WooCommerce plugin that implements facebook share and like button on product page with flexible options.

== Description ==

This is a WooCommerce plugin that implements facebook share and like button on the product page with flexible options. After you activated the plugin, the default option is 'Enabled' for all the existing products.
You can use shortcode '[fbsharelike]' or function call 'fbsharelike()' to output the button.

Under WooCommerce sidebar panel, there will be a new child menu link called 'FBShareLike Settings' section.

The list of options for the section above:

*   Enable the plugin
*   Show in blog post/page (NEW FEATURE)
*   Replace default app id value with yours one, or else just leave it
*   Set width
*   Choose 'Button Alignment' - default(left) or right
*   Enable "Show button below product title" option
*   Enable 'Show Like button only' option
*   Enable 'Turn off Open Graph meta values' option
*   Choose 'Verb to display' for LIKE button - default(like) or recommend
*   Choose 'Color Scheme' - default(light) or dark button
*   Choose 'Font' - default or custom font (Arial, Lucida Grande, Segeo UI, Tahoma, Trebuchet MS, Verdana)
*   Select 'Language Setting' for the language for button. (77 languages supported)

= Demo Site =
Please feel free to visit my [online shop](http://shop.terrytsang.com) where you can test the features of WordPress and all of my extensions. http://shop.terrytsang.com

= GET PRO VERSION ( 9 Social Share Buttons ) =
*   [WooCommerce Social Buttons PRO](http://terrytsang.com/shop/shop/woocommerce-social-buttons-pro/)

= NEW =
* [Coming Soon Product](http://terrytsang.com/shop/shop/woocommerce-coming-soon-product/) - show 'Coming Soon' default message and countdown clock for pre launch product

= In addition to these features, over 20 free and premium WooCommerce extensions are available: =
* [WooCommerce Free Extensions Bundle](http://terrytsang.com/shop/shop/woocommerce-free-extensions-bundle/) - 5 free plugins in 1 download
* [WooCommerce Popular Extensions Bundle](http://terrytsang.com/shop/shop/woocommerce-popular-extensions-bundle/) - 5 unlimited licenses premium plugins with only $99
* [Custom Checkout Options](http://terrytsang.com/shop/shop/woocommerce-custom-checkout-options/) - implement customization for entire checkout process.
* [Direct Checkout](http://terrytsang.com/shop/shop/woocommerce-direct-checkout/) - skip shopping cart page and implement add to cart button redirect to checkout page.
* [Extra Fee Option](http://terrytsang.com/shop/shop/woocommerce-extra-fee-option/) - add multiple extra fee for any order with multiple options.
* [Custom Product Tabs](http://terrytsang.com/shop/shop/woocommerce-custom-product-tabs/) - add multiple tabs to WooCommerce product page.
* [Facebook Social Plugins](http://terrytsang.com/shop/shop/woocommerce-facebook-social-plugins/) - implement Facebook Social Plugins that let the users liked, commented or shared your site's contents.
* [Custom Payment Method](http://terrytsang.com/shop/shop/woocommerce-custom-payment-method/) - customise the custom payment method with flexible options.
* [Custom Shipping Method](http://terrytsang.com/shop/shop/woocommerce-custom-shipping-method/) - define own settings for custom shipping method.
* [Donation/Tip Checkout](http://terrytsang.com/shop/shop/woocommerce-donation-tip-checkout/) - add donation/tip amount option for their customers at WooCommerce checkout page.
* [Product Badge](http://terrytsang.com/shop/shop/woocommerce-product-badge/) - add mulitple badges to the products.
* [Facebook Connect Checkout](http://terrytsang.com/shop/shop/woocommerce-facebook-login-checkout/) - implement Facebook Login so that new customers can sign in woocommerce site by using their Facebook account.
* [Product Catalog](http://terrytsang.com/shop/shop/woocommerce-product-catalog/) - turn WooCommerce into a product catalog with a few clicks.

and many more...

== Installation ==

1. Upload the entire *woocommerce-facebook-share-like-button* folder to the */wp-content/plugins/* directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. A new menu link 'FbShareLike Setting' appeared under WooCommerce sidebar panel, you can click that to update options.
4. That's it. You're ready to go and cheers!

== Screenshots ==

1. Screenshot Admin Product Enabled Option 
2. Screenshot Frontend Product Detail Page
3. Screenshot Admin FbShareLike Settings Option Page
4. Screenshot Frontend - Chinese + Show After Product Title
5. Screenshot Frontend - French + Show Like Button Only + Button Align Right

== Frequently Asked Questions ==

= After activated the plugin, do i need to insert facebook javascript after <body> tag? =

No, you can straight away use the plugin as the plugin included the script.


= If i want to use my own facebook application id, what should i do? =

To update your facebook app id for the plugin, go to Admin left sidebar, menu 'FbShareLike Settings' under 'WoocCommerce' and update option there. (NOTE: This is not your facebook page URL)


= If i want to align button to right? =

You can select the option 'Right' at 'Button Alignment' section.


= If i want to change the default button position[bottom of product summary] to position after product title, what should i do? =

You can check the option 'Show button after product title' at 'FbShareLike Settings' section.


= If i only want to show facebook like button on product page, not the share button. =

You can check the option 'Show only like button' at 'FbShareLike Settings' section.


= If i only want to turn off or remove meta og values that were generated by this plugin to avoid conflict with my other plugin. =

You can check the option 'Turn off open graph meta values' at 'FbShareLike Settings' section.


= If i only want to show dark color button on product page, not the light color button. =

You can select 'Dark' at 'Color Scheme' option under 'FbShareLike Settings' section.


= If i only want to change font for the button text. =

You can select custom font at 'Font' option under 'FbShareLike Settings' section.


= If i only want to show different language for the button text? =

You can select at 'Language Settings' option under 'FbShareLike Settings' section. (77 Languages Supported Now)


= If i want to use the button on sidebar widget or in my php file? =

You can use shortcode [fbsharelike] in widget or function 'fbsharelike()'

= If i want to show buttons in blog post/page? =

You need to check option "Show in blog post/page"


== Upgrade Notice ==

coming soon...

== Changelog ==

= 2.2.3 =
* Tested compatibility with WordPress 4.5.3
* Added icons for plugin page
* Added additional new plugin link

= 2.2.2 =
* Added Great Deal Bundles links

= 2.2.1 =
* Fixed some og meta tags bugs (og:description and og:image)

= 2.2.0 =

* Added z-index for facebook Like popup box
* Updated og:description content

= 2.1.9 =

* Added WordPress 4.0 compatibility
* Turn off open graph meta tags as default

= 2.1.8 =

* Added "Show in blog post/page" option

= 2.1.7 =

* Fixed : Undefined php variables or options warning
* Updated Facebook Like Button to v2

= 2.1.6 =

* Fixed : Assigning the return value of new by reference is deprecated

= 2.1.5 =

* Updated CSS to fix facebook like popup box cropped issue
* Remove z-index and old facebook css styling

= 2.1.3 =

* Updated CSS

= 2.1.2 =

* Updated CSS for facebook like and send button box that was cropped
* Add "Turn off open graph meta values" option to avoid conflict with your other plugin
* Updated facebook like widget - added data-href parameter in facebook like link

= 2.1.1 =

* Fixed unchecked enabled box problem

= 2.1.0 =

* Add shortcode [fbsharelike] and function call <?php fbsharelike(); ?>
* Update css

= 2.0.11 =

* Fixed bugs

= 2.0.10 =

* Updated CSS for facebook like and send button box that was cropped

= 2.0.9 =

* Fixed Double add to cart issues

= 2.0.8 =

* Fixed bugs

= 2.0.7 =

* Updated : updated "overflow: visible;" css for social-button-container 
* NEW : add custom font option for the setting page

= 2.0.6 =

* Fixed : remove additional <br> below the button
* Updated : add "overflow: hidden;" css for social-button-container 
* NEW : add new link for "Get Social Buttons Pro" with more social media

= 2.0.5 =

* Fixed : remove additional bloginfo description output on top page
* NEW : add new option 'Button Alignment' where you can choose to align left or right

= 2.0.4 =

* Fixed : subtitle problem
* Updated : og:type set to 'product'


= 2.0.3 =

* Fixed undefined $post
* Updated facebook application id help description


= 2.0.2 =

* Fixed bugs


= 2.0.1 =

* Fixed missing variables
* Update Product Description (200 chars limit) for OG <Meta> description


= 2.0.0 =

* Change menu 'FbShareLike' sidebar to 'FBShareLike Settings' link under 'WooCommerce' sidebar panel
* Add more options for the setting page (width, languages, verb to display and color scheme)
* Add OG <Meta> tags for facebook graph apps


Screenshot WooCommerce Payment Gateways - Custom Payment Option
Screenshot Frontend Payment Gateways - Custom Payment Option
Screenshot Frontend Payment Gateways - After Ordering