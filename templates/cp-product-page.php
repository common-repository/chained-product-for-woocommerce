<?php
/**
 * Product page template.
 *
 * @package : chain-product-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_nonce_field( 'ctcpfw__nonce', 'ctcpfw__nonce' );

$rule_id = $bundle_detail['rule_id'];

$final_bundle_product = $bundle_detail['product_array'];

?>
<div>
	<h3><?php echo esc_attr( get_post_meta( $rule_id, 'ctcpfw__bundle_title', true ) ); ?></h3>
	<?php if ( 'ul_li_template' === (string) get_option( 'ctcpfw__bundle_product_template' ) ) { ?>
		<div class="main-popup">
			<div class="ct-product-items">
				<ul>
					<?php
					foreach ( $final_bundle_product as $currenct_product_id => $product_detail ) {

						if ( is_array( $product_detail ) && count( $product_detail ) ) {

							$currenct_product = wc_get_product( $currenct_product_id );
							$min_qty          = isset( $product_detail['min_qty'] ) && $product_detail['min_qty'] >= 1 ? $product_detail['min_qty'] : 1;

							$price = 0;



							$discount_amount = isset( $product_detail['discount_amount'] ) ? (float) $product_detail['discount_amount'] : 0;

							if ( isset( $product_detail['discount_type'] ) && 'same_price' === (string) $product_detail['discount_type'] ) {


								$price = $currenct_product->get_price();

							} elseif ( isset( $product_detail['discount_type'] ) && 'percentage_discount' === (string) $product_detail['discount_type'] ) {

								$price = $currenct_product->get_price() - ( ( $discount_amount / 100 ) * $currenct_product->get_price() );


							} elseif ( isset( $product_detail['discount_type'] ) && 'fixed_discount' === (string) $product_detail['discount_type'] ) {
								$price = $currenct_product->get_price() - $discount_amount;

							}

							$image_url = wp_get_attachment_url( $currenct_product->get_image_id() ) ? wp_get_attachment_url( $currenct_product->get_image_id() ) : wc_placeholder_img_src();
							?>

							<li>
								<div class="ct-prod-img">
									<img src="<?php echo esc_url( $image_url ); ?>" class="image">
								</div>
								<div class="ct-product-detail">

									<h3><?php echo esc_attr( $currenct_product->get_name() ); ?></h3>

									<small>
										<?php if ( get_option( 'ctcpfw__show_product_price' ) ) { ?>


											<del>
												<?php echo wp_kses_post( wc_price( $currenct_product->get_price() ) ); ?>
											</del>
											<?php echo wp_kses_post( wc_price( $price ) ); ?>

										<?php } ?>
									</small>

									<div class="ct-product-qty-box">
										<input style="width: 50px" type="number" class="ctcpfw__selected_qty" name="ctcpfw__selected_qty<?php echo esc_attr( $currenct_product_id ); ?>" min="<?php echo esc_attr( $min_qty ); ?>" value="<?php echo esc_attr( $min_qty ); ?>"

										<?php if ( isset( $product_detail['max_qty'] ) && $product_detail['max_qty'] >= 1 ) { ?>

											max="<?php echo esc_attr( $product_detail['max_qty'] ); ?>"

										<?php } ?>
										data-ctcpfw__product_price="<?php echo esc_attr( $price ); ?>">

									</div>

									<?php if ( get_option( 'ctcpfw__show_product_des' ) ) { ?>

										<span>
											<?php echo wp_kses_post( substr( $currenct_product->get_description(), 0, get_option( 'ctcpfw__show_product_des_max_charater' ) ) ); ?>
										</span>


									<?php } ?>

								</div>
							</li>
							<?php
						}
					}
					?>

				</ul>
			</div>
		</div>
	<?php } else { ?>

		<div class="ct-cpfw-bundle-product-table">
			<table>
				<thead>
					<tr>
						<th>
							<?php echo esc_html__( 'Product', 'chain-product-for-woocommerce' ); ?>
						</th>

						<th>
							<?php echo esc_html__( 'Qty', 'chain-product-for-woocommerce' ); ?>
						</th>

						<?php if ( get_option( 'ctcpfw__show_product_des' ) ) { ?>
							<th>
								<?php echo esc_html__( 'Description', 'chain-product-for-woocommerce' ); ?>
							</th>

						<?php } ?>

						<?php if ( get_option( 'ctcpfw__show_product_price' ) ) { ?>
							<th>
								<?php echo esc_html__( 'Price', 'chain-product-for-woocommerce' ); ?>
							</th>

						<?php } ?>
					</tr>

				</thead>
				<tbody>

					<?php
					foreach ( $final_bundle_product as $currenct_product_id => $product_detail ) {

						if ( is_array( $product_detail ) && count( $product_detail ) ) {

							$currenct_product = wc_get_product( $currenct_product_id );
							$min_qty          = isset( $product_detail['min_qty'] ) && $product_detail['min_qty'] >= 1 ? $product_detail['min_qty'] : 1;

							$discount_amount = isset( $product_detail['discount_amount'] ) ? (float) $product_detail['discount_amount'] : 0;
							$price           = $currenct_product->get_price();

							if ( isset( $product_detail['discount_type'] ) && 'free' === (string) $product_detail['discount_type'] ) {


								$price = 0;

							} elseif ( isset( $product_detail['discount_type'] ) && 'percentage_discount' === (string) $product_detail['discount_type'] ) {

								$price = $currenct_product->get_price() - ( ( $discount_amount / 100 ) * $currenct_product->get_price() );


							} elseif ( isset( $product_detail['discount_type'] ) && 'fixed_discount' === (string) $product_detail['discount_type'] ) {
								$price = $currenct_product->get_price() - $discount_amount;

							}

							?>

							<tr>

								<td>
									<img width="50px" height="50px" src="<?php echo esc_url( wp_get_attachment_url( $currenct_product->get_image_id() ) ); ?>"><a href="<?php echo esc_attr( $currenct_product->get_permalink() ); ?>" target="_blank"><?php echo esc_attr( $currenct_product->get_name() ); ?></a>
								</td>
								<td>

									<input style="width: 50px" type="number" class="ctcpfw__selected_qty" name="ctcpfw__selected_qty<?php echo esc_attr( $currenct_product_id ); ?>" min="<?php echo esc_attr( $min_qty ); ?>" value="<?php echo esc_attr( $min_qty ); ?>"

									<?php if ( isset( $product_detail['max_qty'] ) && $product_detail['max_qty'] >= 1 ) { ?>

										max="<?php echo esc_attr( $product_detail['max_qty'] ); ?>"

									<?php } ?>
									data-ctcpfw__product_price="<?php echo esc_attr( $price ); ?>"
									>

								</td>


								<?php
								if ( get_option( 'ctcpfw__show_product_des' ) ) {
									?>
									<td>
										<span><?php echo wp_kses_post( substr( $currenct_product->get_description(), 0, get_option( 'ctcpfw__show_product_des_max_charater' ) ) ); ?></span>
									</td>

									<?php
								}

								if ( get_option( 'ctcpfw__show_product_price' ) ) {
									?>

									<td>
										<del>
											<?php echo wp_kses_post( wc_price( $currenct_product->get_price() ) ); ?>
										</del>
										<?php echo wp_kses_post( wc_price( $price ) ); ?>
									</td>
								<?php } ?>

							</tr>

							<?php

						}
					}
					?>
				</tbody>
			</table>
		</div>
		<?php
	}
	?>

	<table>

		<tr>

			<td></td>
			<td></td>
			<th><?php echo esc_html__( 'Chain Product Price', 'chain-product-for-woocommerce' ); ?></th>
			<td class="ct-cpfw-selected-options_total"><?php echo wp_kses_post( wc_price( 0 ) ); ?></td>

		</tr>

		<tr>

			<td></td>
			<td></td>
			<th><?php echo esc_html__( 'Total', 'chain-product-for-woocommerce' ); ?></th>
			<td class="ct-cpfw-selected-total"><?php echo wp_kses_post( wc_price( $product->get_price() ) ); ?></td>

		</tr>

	</table>

	<input type="hidden" name="ctcpfw__rule_id" value="<?php echo esc_attr( $rule_id ); ?>">

	<input type="hidden" name="ctcpfw__final_prds" value="<?php echo esc_attr( implode( ',', array_keys( $final_bundle_product ) ) ); ?>">

	<input type="hidden" name="ctcpfw__min_purchase" class="ctcpfw__min_purchase" value="<?php echo esc_attr( (int) get_post_meta( $rule_id, 'ctcpfw__min_purchase', true ) ? get_post_meta( $rule_id, 'ctcpfw__min_purchase', true ) : 1 ); ?>">

	<input type="hidden" name="ctcpfw__max_purchase" class="ctcpfw__max_purchase" value="<?php echo esc_attr( (int) get_post_meta( $rule_id, 'ctcpfw__max_purchase', true ) ); ?>">


	<input type="hidden" class="ctcpfw__main_prd_price" value="<?php echo esc_attr( (int) $product->get_price() ); ?>">
</div>
<?php
