<?php
/**
 * Novitell template functions.
 * Collection of specific template functions for novitell theme
 * @package novitell
 */


/*
 * Wrap elements
 *
 */

function novitell_double_wrapper_start() {
    ?>
    <div class="container">
        <div class="col-full">


    <?php
}

function novitell_double_wrapper_end() {
    ?>
        </div>
    </div>
    <?php
}

/*
 * Product info wrapper
 */

function novitell_wrap_product_info_start() {
    ?>
    <div class="product__info-container">
    <?php
}

function novitell_wrap_product_info_end() {
    ?>
    </div>
    <?php
}

/*
 * Archive info wrapper
 */

function novitell_archive_description_wrap_start() {
    ?>
    <div class="categorydescription__info-container">
    <?php
}

function novitell_archive_description_wrap_end() {
    ?>
    </div>
    <?php
}

/*
 * Adding top zone widget area
 * echoes a ul with each category listed in li
 */

function novitell_top_zone_widget() {
    if ( is_active_sidebar( 'top_zone_widget' ) ) : ?>
        <div class="top-zone-widget-container" role="complementary">
            <?php dynamic_sidebar( 'top_zone_widget' ); ?>
        </div>
    <?php endif;
}

/*
 * List all subcategories
 */
function novitell_product_subcats_from_parentcat_by_ID($parent_cat_ID) {

    $args = array(
        'hierarchical' => 1,
        'show_option_none' => '',
        'hide_empty' => 0,
        'parent' => $parent_cat_ID,
        'taxonomy' => 'product_cat'
    );
    $subcats = get_categories($args);

    if(!empty($subcats)) {
        echo '<ul class="subcategories-menu">';
        foreach ($subcats as $sc) {
            $link = get_term_link( $sc->slug, $sc->taxonomy );
            echo '<li class="subcategories-menu__item" ><a class="subcategories-menu__link" href="'. $link .'">'.$sc->name.'</a></li>';
        }
        echo '</ul>';
    }
    // If no subcats,  get sibblings
    else {
        $parentCats = get_ancestors( $parent_cat_ID, 'category', 'taxonomy' );
        if(isset($parentCats[0])) {
            $nearestParent = get_term($parentCats[0], 'product_cat');
            $args = array(
                'hierarchical' => 1,
                'show_option_none' => '',
                'hide_empty' => 0,
                'parent' => $nearestParent->term_id,
                'taxonomy' => 'product_cat'
            );
            $subcats = get_categories($args);
            if(!empty($subcats)) {
                echo '<ul class="subcategories-menu">';
                foreach ($subcats as $sc) {
                    $link = get_term_link( $sc->slug, $sc->taxonomy );
                    $class = ($sc->term_id === $parent_cat_ID) ? 'subcategories-menu__link--selected ' : NULL;
                    echo '<li class="subcategories-menu__item" ><a class="' . $class . 'subcategories-menu__link" href="'. $link .'">'.$sc->name.'</a></li>';

                }
                echo '</ul>';
            }
            else {
                echo "fail";
            }
        }

    }

}

/*
 * Newsletter form
 *
 */

function novitell_newsletter_form() {
    ?>
        <div class="newsletter-subscription-form-full-width">
        <h3>Prenumerera på vårt nyhetsbrev för intressanta erbjudanden!</h3>
        <?php
        echo do_shortcode("[newsletter_form layout=html5 form=1]");
        ?>
        </div>
    <?php
}





/**
 * Display the post header with a link to the single post
 * This function simply overrides the function with the same name in the storefront theme
 * @since 1.0.0
 */
function storefront_post_header() {
    ?>
    <header class="entry-header">
        <?php
        if ( is_single() ) {

            the_title( '<h1 class="entry-title">', '</h1>' );

            // POST DATE
            storefront_posted_on();

            // COMMENTS
             if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
                    <span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'storefront' ), __( '1 Comment', 'storefront' ), __( '% Comments', 'storefront' ) ); ?></span>
            <?php endif;
        } else {
            the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' );

            // POST DATE
            if ( 'post' == get_post_type() ) {
                storefront_posted_on();
            }
            // COMMENTS
            if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
                <span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'storefront' ), __( '1 Comment', 'storefront' ), __( '% Comments', 'storefront' ) ); ?></span>
            <?php endif;
        }
        ?>
    </header><!-- .entry-header -->
    <?php
}

/*
 * Archive page title
 *
 */
function novitell_archive_page_title() {

    if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

        <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>

    <?php endif;
}

