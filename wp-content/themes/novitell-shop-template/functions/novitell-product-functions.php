<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


/**
 * Added small change to WC orginal function in class product variation
 * THIS FUNCTION HAS TO BE REVIEWED AGAIN AS WOOCOMMERCE HAS CHANGED IT IN THE NEW VERSION
 * @return string containing the formatted price
 */
function novitell_get_price_html( $product ) {

    $display_price         = $product->get_display_price();
    $display_regular_price = $product->get_display_price( $product->get_regular_price() );
    $display_sale_price    = $product->get_display_price( $product->get_sale_price() );


    /*
     * Nilu 161215:
     * Added this, to handle also variable product pricing.
     * Very impoertant in all woocommerce solutions.
     * See:
     * http://webroxtar.com/2013/07/get-regular-and-sale-prices-of-product-variations-in-woocommerce/
     */

    if($product->product_type=='variable') {

        $available_variations = $product->get_available_variations();

        #Step 2: Get product variation id
        $variation_id=$available_variations[0]['variation_id']; // Getting the variable id of just the 1st product. You can loop $available_variations to get info about each variation.

        #Step 3: Create the variable product object
        $variable_product1= new WC_Product_Variation( $variation_id );

        #Step 4: You have the data. Have fun :)
        $display_regular_price = $variable_product1 ->regular_price;
        $display_sale_price = $variable_product1 ->sale_price;

    }

        if ($product->get_price() !== '') {
            if ($product->is_on_sale()) {
                $price = apply_filters('woocommerce_variation_sale_price_html', '<del>' . wc_price($display_regular_price) . '</del> <ins>' . wc_price($display_sale_price) . '</ins>' . $product->get_price_suffix(), $product);
            } elseif ($product->get_price() > 0) {
                $price = apply_filters('woocommerce_variation_price_html', wc_price($display_price) . $product->get_price_suffix(), $product);
            } else {
                $price = '<span class="no-price">Lägg till i varukorg så kontaktar vi Dig för prisförslag</span>';
            }
        } else {

            $price = apply_filters('woocommerce_variation_empty_price_html', '', $product);
        }

        return apply_filters('woocommerce_get_variation_price_html', $price, $product);


}

/**
 * Get the product row price per item.
 * This function was built for Alems jarnhandel in order to change 0 to "Prisförfrågan"
 * @param WC cart class object
 * @param WC_Product $_product
 * @return string formatted price
 */
function novitell_get_product_price( $WC ,$_product ) {
    if ( $WC->tax_display_cart == 'excl' ) {
        $product_price = $_product->get_price_excluding_tax();
    } else {
        $product_price = $_product->get_price_including_tax();
    }

    if ($product_price == 0) {
        return "<span class='price-request'>Prisförfrågan</span>";
    }
    else {
        return apply_filters( 'woocommerce_cart_product_price', wc_price( $product_price ), $_product );
    }
}




/**
 * Use the following snippet to remove specific tabs
 */

function novitell_remove_product_tabs( $tabs ) {

    unset( $tabs['description'] );      	// Remove the description tab
    unset( $tabs['reviews'] ); 			// Remove the reviews tab
    unset( $tabs['additional_information'] );  	// Remove the additional information tab

    return $tabs;

}


/**
 * Adds image to category description
 * Could be hooked on in the archive template
 */
function novitell_category_image() {
    if ( is_product_category() ){
        global $wp_query;
        $cat = $wp_query->get_queried_object();
        $thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
        $image = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail' );
        if ( $image ) {
           echo '<div class="categorydescription__image"><img src="' . $image[0] . '" alt="' . $cat->name . '" /></div>';
            //var_dump($image);
        }
    }
}



/**
 * Returns string with product details
 */

function novitell_get_product_details () {
    global $product;
    $details = $product->get_attribute( 'produktdetaljer' );
    echo "<p class='product__details'>" . $details . "</p>";
}

/*
 * Include short product description
 *
 */

function novitell_short_product_description() {
    global $post;
    ?>
    <span class="short-product-description">
        <?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>
    </span>
    <?php
}
