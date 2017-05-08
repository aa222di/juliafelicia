<?php
/**
 * Created by PhpStorm.
 * User: nicklas
 * Date: 2016-07-18
 * Time: 18:48
 */

@include_once('functions/debug.php');
@require_once('functions/novitell-template-functions.php');
@require_once('functions/novitell-product-functions.php');
@require_once('functions/novitell-homepagecontroll.php');

/*
 * Theme setup
 */

function novitell_theme_setup() {

    // ENQUEUE SCRIPTS, STYLES AND FONTS, ADD IMAGES SIZES AND WIDGETS
    // Enqueue styles and fonts
    add_action( 'wp_enqueue_scripts', 'novitell_theme_enqueue_styles' );
    add_action( 'wp_enqueue_scripts', 'novitell_theme_enqueue_scripts' );
    add_action('wp_print_styles', 'novitell_load_fonts');

    // Add our widgets
    add_action( 'widgets_init', 'novitell_widgets_init' );

    // Add our image sizes
    novitell_images_sizes();


    // HOMEPAGE CONTROL, ADDING FUNCTIONS
    // Remove homepage hooks
    remove_action( 'homepage', 'pangolin_add_slider_storefront',         5 );
    remove_action( 'homepage', 'storefront_product_categories',         20 );

    // Add new novitell puff block for recent products

    add_action( 'homepage', 'novitell_homepage_widget_1', 0 );

    add_action( 'homepage', 'novitell_homepage_widget_2', 0 );

    // Add newsletter subscription form to homepage hook (that way it is part of the front page blocks)
   // add_action( 'homepage', 'novitell_newsletter_form', 0 );


    // TEMPLATE SPECIFIC HOOKS AND ACTIONS
    // Remove post meta - these to lines should preferably be put in the specific template
    // but stays here as we haven't done any other changes to that template
    remove_action( 'storefront_loop_post',         'storefront_post_meta',            20 );
    remove_action( 'storefront_single_post',       'storefront_post_meta',            20 );
}

/*
 * Adding styles
 */
function novitell_theme_enqueue_styles() {

    $parent_style = 'parent-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
                        get_stylesheet_directory_uri() . '/style.css',
                        array( $parent_style )
                    );

    wp_enqueue_style( 'typography',
        get_stylesheet_directory_uri() . '/css/typography.css',
        array( $parent_style )
    );

    wp_enqueue_style( 'elements',
        get_stylesheet_directory_uri() . '/css/elements.css',
        array( $parent_style )
    );

    wp_enqueue_style( 'header',
        get_stylesheet_directory_uri() . '/css/header.css',
        array( $parent_style )
    );

    wp_enqueue_style( 'blog',
        get_stylesheet_directory_uri() . '/css/blog.css',
        array( $parent_style )
    );

    wp_enqueue_style( 'products',
        get_stylesheet_directory_uri() . '/css/products.css',
        array( $parent_style )
    );

    wp_enqueue_style( 'frontpage',
        get_stylesheet_directory_uri() . '/css/frontpage.css',
        array( $parent_style )
    );

    wp_enqueue_style( 'layout',
        get_stylesheet_directory_uri() . '/css/layout.css',
        array( $parent_style )
    );

    wp_enqueue_style( 'footer',
        get_stylesheet_directory_uri() . '/css/footer.css',
        array( $parent_style )
    );

    wp_enqueue_style( 'slider',
        get_stylesheet_directory_uri() . '/css/slider.css',
        array( $parent_style )
    );

    wp_enqueue_style( 'newsletter',
        get_stylesheet_directory_uri() . '/css/newsletter.css',
        array( $parent_style )
    );

    wp_enqueue_style( 'responsive',
        get_stylesheet_directory_uri() . '/css/responsive.css',
        array( $parent_style )
    );
}

/*
 * Adding scripts
 */
function novitell_theme_enqueue_scripts() {
    wp_register_script( 'searchwidget-js', get_stylesheet_directory_uri() . '/js/searchwidget.js' , '', '', true );
    wp_enqueue_script( 'searchwidget-js' );
    wp_register_script( 'novitell-fontawesome', 'https://use.fontawesome.com/a96a126bd1.js' , '', '', false );
    wp_enqueue_script( 'novitell-fontawesome' );

}

/*
 * Adding fonts
 */

function novitell_load_fonts() {
    wp_register_style('Raleway', 'https://fonts.googleapis.com/css?family=Raleway:300,300i,400,400i,500,500i,600,600i');
    wp_enqueue_style( 'Raleway');

    wp_register_style('OpenSans', 'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i&subset=latin-ext');
    wp_enqueue_style( 'OpenSans');
}

/**
 * Register our sidebars and widgetized areas.
 *
 */
function novitell_widgets_init() {

    register_sidebar( array(
        'name'          => 'Sidowidget för blogg',
        'id'            => 'blog_left_1',
        'before_widget' => '<aside class="widget">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h2>',
        'after_title'   => '</h2>',
    ) );

    register_sidebar( array(
        'name'          => 'Widgetarea för topplist',
        'id'            => 'top_zone_widget',
        'before_widget' => '<div class="top-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5>',
        'after_title'   => '</h5>',
    ) );

    register_sidebar( array(
        'name'          => 'Widgetblock 1 för första sidan',
        'id'            => 'homepage_control_widget_1',
        'before_widget' => '',
        'after_widget'  => '',
        'before_title'  => '',
        'after_title'   => '',
    ) );

    register_sidebar( array(
        'name'          => 'Widgetblock 2 för första sidan',
        'id'            => 'homepage_control_widget_2',
        'before_widget' => '',
        'after_widget'  => '',
        'before_title'  => '',
        'after_title'   => '',
    ) );

}

/**
 * Register our image sizes and/or change existing ones
 *
 */
function novitell_images_sizes() {

    add_image_size( 'puff-image', 700, 700, false );
    add_image_size( 'shop_catalog', 400, 400, false );
    add_image_size( 'small-catalog', 150, 150, false );

}



add_action('after_setup_theme', 'novitell_theme_setup', 11 );

// Remove WooCommerce Updater
remove_action('admin_notices', 'woothemes_updater_notice');








