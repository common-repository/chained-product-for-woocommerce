<?php
/**
 * Cart page template.
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

		<tr class="ct-cpfw-selected-bundle-tr woocommerce-cart-form__cart-item cart_item" style="display:none;">

			<td class="product-remove"></td>

			<td class="product-thumbnail">
				<a href="<?php echo esc_url( $image_url ); ?>">
					<img width="324" height="324" src="<?php echo esc_attr( $image_url ); ?>" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" decoding="async" loading="lazy">
				</a> 
			</td>

			<td class="product-name" data-title="Product">

				<a href="<?php echo esc_url( $current_products_obj->get_permalink() ); ?>">
					<?php echo esc_attr( $current_products_obj->get_name() ); ?>
				</a>

			</td>

			<td class="product-price" data-title="Price">
				<?php echo wp_kses_post( wc_price( $price, get_woocommerce_currency_symbol() ) ); ?>
			</td>

			<td class="product-quantity" data-title="Quantity">
				<div class="quantity">

					<input type="number" readonly id="quantity_63fb0c911deb6" class="input-text qty text" step="1" min="<?php echo esc_attr( $selected_qty ); ?>" max="<?php echo esc_attr( $selected_qty ); ?>" name="cart[e1dc7d25786e01bd081d1e2949c37f70][qty]" value="<?php echo esc_attr( $selected_qty ); ?>" title="Qty" size="4" placeholder="" inputmode="numeric" autocomplete="off">

				</div>
			</td>

			<td class="product-subtotal" data-title="Subtotal">
				<?php echo wp_kses_post( wc_price( $price * $selected_qty, get_woocommerce_currency_symbol() ) ); ?>
			</td>

		</tr>

	<?php } ?>

</table>
<?php
