<?php
/**
 * Shipping Methods Display
 *
 * Novitell Storefront: Modified this template to only show free shipping and not both free shipping and flat rate when both are available
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr class="shipping">
	<th>Frakt</th>
	<td data-title="<?php echo esc_attr( $package_name ); ?>">
		<?php if ( 1 < count( $available_methods ) ) : ?>


				<?php // Collect all id:s in an array and check if free shipping is in array

				foreach ( $available_methods as $method ) {
					$method_id_array[] = $method->method_id;
				}
				?>

				<?php if(!in_array('free_shipping', $method_id_array)): ?>
				<ul id="shipping_method">
					<?php foreach ( $available_methods as $method ) : ?>
						<li>
							<?php
								printf( '<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s />
									<label for="shipping_method_%1$d_%2$s">%5$s</label>',
									$index, sanitize_title( $method->id ), esc_attr( $method->id ), checked( $method->id, $chosen_method, false ), wc_cart_totals_shipping_method_label( $method ) );

								do_action( 'woocommerce_after_shipping_rate', $method, $index );
							?>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php else : ?>
					<?php foreach ( $available_methods as $method ) : ?>

						<?php if ($method->method_id == "free_shipping") : ?>

						<?php printf( '%3$s <input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d" value="%2$s" class="shipping_method" />', $index, esc_attr( $method->id ), wc_cart_totals_shipping_method_label( $method ) );
						do_action( 'woocommerce_after_shipping_rate', $method, $index ); ?>


						<?php endif; ?>

					<?php endforeach; ?>
				<?php endif; ?>

		<?php elseif ( 1 === count( $available_methods ) ) :  ?>
			<?php
				$method = current( $available_methods );
				printf( '%3$s <input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d" value="%2$s" class="shipping_method" />', $index, esc_attr( $method->id ), wc_cart_totals_shipping_method_label( $method ) );
				do_action( 'woocommerce_after_shipping_rate', $method, $index );
			?>
		<?php elseif ( ! WC()->customer->has_calculated_shipping() ) : ?>
			<?php echo wpautop( __( 'Shipping costs will be calculated once you have provided your address.', 'woocommerce' ) ); ?>
		<?php else : ?>
			<?php echo apply_filters( is_cart() ? 'woocommerce_cart_no_shipping_available_html' : 'woocommerce_no_shipping_available_html', wpautop( __( 'There are no shipping methods available. Please double check your address, or contact us if you need any help.', 'woocommerce' ) ) ); ?>
		<?php endif; ?>

		<?php if ( $show_package_details ) : ?>
			<?php echo '<p class="woocommerce-shipping-contents"><small>' . esc_html( $package_details ) . '</small></p>'; ?>
		<?php endif; ?>

		<?php if ( is_cart() && ! $index ) : ?>
			<?php woocommerce_shipping_calculator(); ?>
		<?php endif; ?>
	</td>
</tr>
