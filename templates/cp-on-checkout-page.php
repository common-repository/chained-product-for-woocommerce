<?php
/**
 * Checkout page template.
 *
 * @package : chain-product-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><table class="ct-cpfw-selected-bundle-table" style="display:none">
	<?php
	foreach ( $selected_product as $current_products_id => $product_detail_array ) {

		$current_products_obj = wc_get_product( $current_products_id );

		$selected_qty = isset( $product_detail_array['selected_qty'] ) ? (int) $product_detail_array['selected_qty'] : 1;

		if ( isset( $product_detail_array['min_qty'] ) && ! empty( $product_detail_array['min_qty'] ) && $selected_qty < (int) $product_detail_array['min_qty'] ) {

			$selected_qty = (int) $product_detail_array['min_qty'];

		}
		if ( isset( $product_detail_array['max_qty'] ) && ! empty( $product_detail_array['max_qty'] ) && $selected_qty > (int) $product_detail_array['max_qty'] ) {

			$selected_qty = (int) $product_detail_array['max_qty'];

		}

		$qty_type = isset( $product_detail_array['qty_type'] ) ? $product_detail_array['qty_type'] : 'linked';

		if ( 'linked' === (string) $qty_type ) {

			$selected_qty *= $main_product_qty;

		}


		$price = $current_products_obj->get_price();


		$discount_type = isset( $product_detail_array['discount_type'] ) ? $product_detail_array['discount_type'] : 'same_price';

		$discount_amount = isset( $product_detail_array['discount_amount'] ) ? (float) $product_detail_array['discount_amount'] : 0;

		if ( 'free' === (string) $discount_type ) {

			$price = 0;

		} elseif ( 'fixed_discount' === (string) $discount_type ) {

			$price -= $discount_amount;


		} elseif ( 'percentage_discount' === (string) $discount_type ) {

			$price -= ( $discount_amount / 100 ) * $price;
		}
							$image_url = wp_get_attachment_url( $current_products_obj->get_image_id() ) ? wp_get_attachment_url( $current_products_obj->get_image_id() ) : wc_placeholder_img_src();

		?>

		<tr class="cart_item ct-cpfw-selected-bundle-tr" style="display:none;">
			<td class="product-name">
				<?php echo esc_attr( $current_products_obj->get_name() ); ?>                       

				<strong class="product-quantity">
					<?php echo esc_attr( 'x' . $selected_qty ); ?>
				</strong>
			</td>

			<td class="product-total">
				<?php echo wp_kses_post( wc_price( $price * $selected_qty, get_woocommerce_currency_symbol() ) ); ?>

			</td>
		</tr>

	<?php } ?>

</table>
<?php
