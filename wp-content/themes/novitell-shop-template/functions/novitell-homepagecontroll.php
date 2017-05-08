<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Display Product Categories
 * Hooked into the `homepage` action in the homepage template
 *
 * Difference between storefront and novitell: unlimited categories
 *
 * @since  1.0.0
 * @param array $args the product section args.
 * @return void
 */
function novitell_product_categories( $args ) {

    if ( is_woocommerce_activated() ) {

        $args = apply_filters( 'storefront_product_categories_args', array(
            'limit' 			=> 0,
            'columns' 			=> 3,
            'child_categories' 	=> 0,
            'orderby' 			=> 'name',
            'title'				=> __( 'Shop by Category', 'storefront' ),
        ) );

        echo '<section class="storefront-product-section storefront-product-categories">';

        do_action( 'storefront_homepage_before_product_categories' );

        echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

        do_action( 'storefront_homepage_after_product_categories_title' );

        echo storefront_do_shortcode( 'product_categories', array(
            'number'  => intval( $args['limit'] ),
            'columns' => intval( $args['columns'] ),
            'orderby' => esc_attr( $args['orderby'] ),
            'parent'  => esc_attr( $args['child_categories'] ),
        ) );

        do_action( 'storefront_homepage_after_product_categories' );

        echo '</section>';
    }
}




/*
 * Adding widget area to render in the homepage flow
 */

function novitell_homepage_widget_1() {
    if ( is_active_sidebar( 'homepage_control_widget_1' ) ) : ?>
        <div class="novitell-homepage-widget__container">
            <?php dynamic_sidebar( 'homepage_control_widget_1' ); ?>
        </div>
    <?php endif;
}

/*
 * Adding widget area to render in the homepage flow
 */

function novitell_homepage_widget_2() {
    if ( is_active_sidebar( 'homepage_control_widget_2' ) ) : ?>
        <div class="novitell-homepage-widget__container">
            <?php dynamic_sidebar( 'homepage_control_widget_2' ); ?>
        </div>
    <?php endif;
}
