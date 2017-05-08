<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/*
 * Theme setup
 */

function eden_theme_setup() {

    // ENQUEUE SCRIPTS, STYLES AND FONTS, ADD IMAGES SIZES AND WIDGETS
    // Enqueue styles and fonts
    add_action( 'wp_enqueue_scripts', 'eden_theme_enqueue_styles' );
    add_action( 'wp_enqueue_scripts', 'eden_theme_enqueue_scripts' );
    add_action('wp_print_styles', 'eden_load_fonts');

    // Add our widgets
    add_action( 'widgets_init', 'eden_widgets_init' );

    // Add our image sizes
    eden_images_sizes();

    // TEMPLATE SPECIFIC HOOKS AND ACTIONS
    // Remove post meta - these to lines should preferably be put in the specific template
    // but stays here as we haven't done any other changes to that template
    remove_action( 'storefront_loop_post',         'storefront_post_meta',            20 );
    remove_action( 'storefront_single_post',       'storefront_post_meta',            20 );
}

/*
 * Adding styles
 */
function eden_theme_enqueue_styles() {

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
}

/*
 * Adding scripts
 */
function eden_theme_enqueue_scripts() {
    wp_register_script( 'searchwidget-js', get_stylesheet_directory_uri() . '/js/searchwidget.js' , '', '', true );
    wp_enqueue_script( 'searchwidget-js' );
    wp_register_script( 'novitell-fontawesome', 'https://use.fontawesome.com/a96a126bd1.js' , '', '', false );
    wp_enqueue_script( 'novitell-fontawesome' );

}

/*
 * Adding fonts
 */

function eden_load_fonts() {
    wp_register_style('Raleway', 'https://fonts.googleapis.com/css?family=Raleway:300,300i,400,400i,500,500i,600,600i');
    wp_enqueue_style( 'Raleway');

    wp_register_style('OpenSans', 'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i&subset=latin-ext');
    wp_enqueue_style( 'OpenSans');
}

/**
 * Register our sidebars and widgetized areas.
 *
 */
function eden_widgets_init() {


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
function eden_images_sizes() {

    add_image_size( 'puff-image', 700, 700, false );
    add_image_size( 'shop_catalog', 400, 400, false );
    add_image_size( 'small-catalog', 150, 150, false );

}



add_action('after_setup_theme', 'eden_theme_setup', 11 );

// Remove WooCommerce Updater
remove_action('admin_notices', 'woothemes_updater_notice');








