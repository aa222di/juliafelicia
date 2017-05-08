=== FooBox Image Lightbox WordPress Plugin ===
Contributors: bradvin, fooplugins, freemius
Donate link: http://fooplugins.com
Tags: lightbox,media,images,gallery,modal
Requires at least: 3.5.1
Tested up to: 4.7.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A responsive image lightbox for WordPress galleries, WordPress attachments & FooGallery

== Description ==

FooBox was the first lightbox to take responsive layout seriously. Not only does it scale images to look better on phones, but it rearranges it's button controls to look great in both portrait or landscape orientation.

Works with most image gallery plugins, but works best with our [FooGallery Image Gallery WordPress Plugin](http://foo.gallery).

**Now includes a 7-day free trial of FooBox Pro!**

**FooBox Image Lightbox Features:**

*	Responsive design
*	Sexy lightbox design
*	Zero configuration!
*	Works with WordPress galleries
*	Works with WordPress captioned images

**[FooBox PRO](http://fooplugins.com/plugins/foobox/?utm_source=fooboxfreeplugin&utm_medium=fooboxfreeprolink&utm_campaign=foobox_free_wprepo) Features:**

*	Social sharing (10+ networks)
*	Video support
*	HTML support
*	iFrame support
*	Deeplinking
*	Fullscreen and slideshow modes
*	Metro lightbox style
*	5 color schemes, 12 buttons icons and 11 loader icons
*	85+ settings to customize

**[FooBox PRO](http://fooplugins.com/plugins/foobox/?utm_source=fooboxfreeplugin&utm_medium=fooboxfreeprolink&utm_campaign=foobox_free_wprepo) Works With:**

*	[The Best Image Gallery Plugin for WordPress](http://foo.gallery)
*	NextGen
*	[Justified Image Grid](http://codecanyon.net/item/justified-image-grid-premium-wordpress-gallery/2594251)
*	Envira Gallery
*	WooCommerce product images
*	JetPack Tiled Gallery
*	AutOptimize

Check out the [full feature comparison](http://fooplugins.com/foobox-feature-comparison/?utm_source=fooboxfreeplugin&utm_medium=fooboxcomparelink&utm_campaign=foobox_free_wprepo).

**Translations**

* [Serbo-Croatian by Borisa Djuraskovic](http://www.webhostinghub.com/)

== Installation ==

1. Upload `foobox-free` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= FooBox is not working. There is an error in the console "Uncaught ReferenceError: FooBox is not defined" =

Some plugins or themes defer javascript in the page, which causes the FooBox initialization code to run BEFORE the FooBox main script is loaded. This has been fixed in version 1.2.24. Please upgrade.

= My theme has a built-in lightbox and it shows under FooBox. What can I do? =

There is a setting to try and disable hard coded lightboxes, but this is not a sure-fire solution for every scenario. If that setting does not work for you, you might need to deregister certain javascript files, or uncomment certain lines of code in your theme to remove it's lightbox.

== Screenshots ==

1. Frontend example
2. Phone example

== Changelog ==

= 1.2.24 =
* Added better browser support for defer javascript loading added in 1.2.23

= 1.2.23 =
* Added support for plugins that defer javascript loading, e.g. AutOptimize

= 1.2.22 =
* Updated to latest JS and CSS fixing multiple issues and bugs

= 1.1.11 =
* Updated to latest JS and CSS fixing some bugs
* Updated to latest Freemius 1.2.1.5 SDK
* Free trial for PRO now included in getting started page

= 1.1.10 =
* Fix deactivation issue when PRO is activated

= 1.1.9 =
* New setting for dropping IE7 support (for valid CSS)
* Fix for when multiple jQuery versions loaded on page!
* Fix for not including scripts for setting 'disable other lightboxes'

= 1.1.8 =
* IMPORTANT : clear your site cache when updating - if you use a caching plugin.
* Added clear cache message to getting started page
* Removed duplicate settings page
* Updated opt-in message
* Fix : loosing scroll position when scrollbars are hidden

= 1.1.7 =
* Integrated Freemius tracking and upgrade system
* Moved FooBox into top-level menu item
* Complete overhaul of Getting Started page, including demo
* Updated to use latest FooBox JS and CSS

= 1.0.14 =
* Hide foo admin notice on mobile devices
* More CSS tweaks for admin on smaller screen sizes

= 1.0.13 =
* Updated settings page to be responsive
* Tested with WP 4.6

= 1.0.12 =
* Updated to use latest FooBox JS and CSS
* Removed discount for FooBox PRO

= 1.0.11 =
* Updated to use latest FooBox JS and CSS
* Updated settings to include demo tab
* Updated admin screens be responsive on phones

= 1.0.10 =
* Updated to use latest FooBox JS fixing few bugs
* Smarter admin warnings when using with FooGallery

= 1.0.9 =
* Updated to use latest FooBox JS fixing few bugs
* Reorder selectors so FooGallery can take preference in some cases

= 1.0.8 =
* Updated to use latest FooBox JS
* Added new Getting Started landing page on activation
* Added support for wp.org language packs
* Better FooGallery integration

= 1.0.7 =
* Updated to latest version of javascript and CSS files
* Plays better with FooBox PRO now

= 1.0.6 =
* Fixed navbar issues in Chrome on IOS

= 1.0.5 =
* Fixed very minor vulnerability with add_query_arg function used in admin plugins page

= 1.0.4 =
* Improved FooGallery support
* Added keyboard navigation support!
* 50% offer included for PRO version

= 1.0.3 =
* Added FooGallery support
* Added .nolightbox to exclusions
* Added .pot translation file
* Added Bottomless design banner to "FooBot Says..." tab

= 1.0.2.1 =
* Fixed jQuery dependency issue with themes that do not load jQuery by default

= 1.0.2 =
* Added setting "Show Captions On Hover"
* Added "FooBot Says..." tab on settings page

= 1.0.1 =
* first version!