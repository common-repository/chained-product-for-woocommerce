<?php
/**
 * Ajax Class.
 *
 * @package : chain-product-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Cpfw_Ajax_Callback {

	public function __construct() {

		add_action( 'wp_ajax_ct_add_chain_prd', array( $this, 'ct_add_chain_prd' ) );

		add_action( 'wp_ajax_ct_delete_woo_bundle_product', array( $this, 'ct_delete_woo_bundle_product' ) );

		add_action( 'wp_ajax_ct_get_chain_product_bundles_on_variation', array( $this, 'ct_get_chain_product_bundles_on_variation' ) );
		add_action( 'wp_ajax_nopriv_ct_get_chain_product_bundles_on_variation', array( $this, 'ct_get_chain_product_bundles_on_variation' ) );
	}

	public function ct_add_chain_prd() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

		if ( ! wp_verify_nonce( $nonce, 'ctcpfw__nonce' ) ) {

			wp_die( esc_html__( 'Failed ajax security check!', 'chain-product-for-woocommerce' ) );

		}
		ob_start();
		if ( isset( $_POST['rule_id'] ) && isset( $_POST['product_ids'] ) ) {

			$rule_id          = sanitize_text_field( $_POST['rule_id'] );
			$selected_prd     = sanitize_meta( '', $_POST['product_ids'], '' );
			$selected_prd     = array_filter( $selected_prd );
			$old_selected_prd = (array) get_post_meta( $rule_id, 'ctcpfw__selected_chain_prd', true );
			$old_selected_prd = array_filter( $old_selected_prd );

			foreach ( $selected_prd as  $selected_prd_ids ) {

				if ( $selected_prd_ids && ! isset( $old_selected_prd[ $selected_prd_ids ] ) ) {

					$old_selected_prd[ $selected_prd_ids ] = array(
						'min_qty'         => 1,
						'max_qty'         => '',
						'discount_type'   => 'same_price',
						'discount_amount' => 0,
					);

					$discount_type = array(
						'same_price'          => 'Same Price',
						'free'                => 'Free',
						'percentage_discount' => 'Percentage Discount',
						'fixed_discount'      => 'Fixed Discount',
					);

					$product = wc_get_product( $selected_prd_ids );

					$product_link = 'variation' === (string) $product->get_type() ? get_edit_post_link( $product->get_parent_id() ) : get_edit_post_link( $product->get_id() );

					?>

					<tr class="ct-cp-table-row-product-data">


						<th class="ct-cp-product-name">
							<a href="<?php echo esc_url( $product_link ); ?>"><?php echo esc_attr( $product->get_name() . '(' . $product->get_stock_status() . ')' ); ?></a>
						</th>
						<td class="ct-cp-product-price-type">
							<?php
							$qty_type = array( 'linked', 'fixed' );

							?>
							<select class="selected_prd_discounted_type" name="selected_chain_prd_detail[<?php echo esc_attr( $selected_prd_ids ); ?>][qty_type]">
								<?php foreach ( $qty_type as $value ) : ?>
									<option value="<?php echo esc_attr( $value ); ?>" <?php echo esc_attr( $value === $selected_discount_type ? 'selected' : '' ); ?>>
										<?php echo esc_attr( ucfirst( $value ) ); ?>
									</option>
								<?php endforeach ?>
							</select>

						</td>

						<td class="ct-cp-product-qty-type">
							<input type="number" min="1" name="selected_chain_prd_detail[<?php echo esc_attr( $selected_prd_ids ); ?>][min_qty]" value="">
						</td>
						<td class="ct-cp-product-qty-type">
							<input type="number" min="1" name="selected_chain_prd_detail[<?php echo esc_attr( $selected_prd_ids ); ?>][max_qty]" value="">
						</td>
						<td class="ct-cp-product-price-type">
							<?php
							$discount_type = array(
								'same_price'          => 'Same Price',
								'free'                => 'Free',
								'percentage_discount' => 'Percentage Discount',
								'fixed_discount'      => 'Fixed Discount',
							);

							?>
							<select class="selected_prd_discounted_type" name="selected_chain_prd_detail[<?php echo esc_attr( $selected_prd_ids ); ?>][discount_type]">
								<?php foreach ( $discount_type as $key => $value ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>">
										<?php echo esc_attr( $value ); ?>
									</option>
								<?php endforeach ?>
							</select>

						</td>

						<td class="ct-cp-discount-amount">
							<input type="number" min="0" class="selected_prd_detail_discount_amount" name="selected_chain_prd_detail[<?php echo esc_attr( $selected_prd_ids ); ?>][discount_amount]" value="">

						</td>

						<td class="ct-remove-woo-product-bundle-td">
							<i class="ct-remove-woo-product-bundle" data-product_id="<?php echo esc_attr( $selected_prd_ids ); ?>" data-main_product_id="<?php echo esc_attr( $rule_id ); ?>" >X</i>
						</td>

					</tr>
					<?php

				}
			}

			$result = ob_get_clean();

			update_post_meta( $rule_id, 'ctcpfw__selected_chain_prd', $old_selected_prd );

			wp_send_json_success( array( 'new_html' => $result ) );

			wp_die();

		}
	}

	public function ct_delete_woo_bundle_product() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

		if ( ! wp_verify_nonce( $nonce, 'ctcpfw__nonce' ) ) {

			wp_die( esc_html__( 'Failed ajax security check!', 'chain-product-for-woocommerce' ) );

		}

		if ( isset( $_POST['rule_id'] ) && isset( $_POST['product_id'] ) ) {

			$rule_id    = sanitize_text_field( $_POST['rule_id'] );
			$product_id = sanitize_text_field( $_POST['product_id'] );

			$old_selected_prd = (array) get_post_meta( $rule_id, 'ctcpfw__selected_chain_prd', true );
			$old_selected_prd = array_filter( $old_selected_prd );

			if ( isset( $old_selected_prd[ $product_id ] ) ) {

				unset( $old_selected_prd[ $product_id ] );

				update_post_meta( $rule_id, 'ctcpfw__selected_chain_prd', $old_selected_prd );

				wp_send_json_success( array( 'delete' => 'yes' ) );

			}
		}
				wp_die();
	}


	public function ct_get_chain_product_bundles_on_variation() {

		if ( isset( $_POST['variation_id'] ) ) {

			$product_id = sanitize_text_field( $_POST['variation_id'] );

			$product = wc_get_product( $product_id );

			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

			if ( ! wp_verify_nonce( $nonce, 'ctcpfw__nonce' ) ) {

				wp_die( esc_html__( 'Failed ajax security check!', 'chain-product-for-woocommerce' ) );

			}

			$bundle_detail = (array) check_product_match_all_rule_or_not( $product_id );

			if ( isset( $bundle_detail['product_array'] ) && count( $bundle_detail['product_array'] ) >= 1 && isset( $bundle_detail['rule_id'] ) ) {

				if ( ctcp_cpfw_Front::user_is_able_to_get_chain_product( $bundle_detail['rule_id'], $product->get_id() ) ) {

					ob_start();

					wp_nonce_field( 'ctcpfw__nonce', 'ctcpfw__nonce' );

					$rule_id = $bundle_detail['rule_id'];

					$final_bundle_product = $bundle_detail['product_array'];

					?>
					<div>
						<h3><?php echo esc_attr( get_post_meta( $rule_id, 'ctcp_cpfw_bundle_title', true ) ); ?></h3>
						<?php if ( 'ul_li_template' === (string) get_option( 'ctcp_cpfw_bundle_product_template' ) ) { ?>
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
												?>

												<li>
													<div class="ct-prod-img">
														<img src="<?php echo esc_url( wp_get_attachment_url( $currenct_product->get_image_id() ) ); ?>" class="image">
													</div>
													<div class="ct-product-detail">

														<h3><?php echo esc_attr( $currenct_product->get_name() ); ?></h3>

														<small>
															<?php if ( get_option( 'ctcp_cpfw_show_product_price' ) ) { ?>


																<del>
																	<?php echo wp_kses_post( wc_price( $currenct_product->get_price() ) ); ?>
																</del>
																<?php echo wp_kses_post( wc_price( $price ) ); ?>

															<?php } ?>
														</small>

														<div class="ct-product-qty-box">
															<input style="width: 50px" type="number" class="ctcp_cpfw_selected_qty" name="ctcp_cpfw_selected_qty<?php echo esc_attr( $currenct_product_id ); ?>" min="<?php echo esc_attr( $min_qty ); ?>" value="<?php echo esc_attr( $min_qty ); ?>"

															<?php if ( isset( $product_detail['max_qty'] ) && $product_detail['max_qty'] >= 1 ) { ?>

																max="<?php echo esc_attr( $product_detail['max_qty'] ); ?>"

															<?php } ?>
															data-ctcp_cpfw_product_price="<?php echo esc_attr( $price ); ?>">

														</div>

														<?php if ( get_option( 'ctcp_cpfw_show_product_des' ) ) { ?>

															<span>
																<?php echo wp_kses_post( substr( $currenct_product->get_description(), 0, get_option( 'ctcp_cpfw_show_product_des_max_charater' ) ) ); ?>
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

											<?php if ( get_option( 'ctcp_cpfw_show_product_des' ) ) { ?>
												<th>
													<?php echo esc_html__( 'Description', 'chain-product-for-woocommerce' ); ?>
												</th>

											<?php } ?>

											<?php if ( get_option( 'ctcp_cpfw_show_product_price' ) ) { ?>
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

														<input style="width: 50px" type="number" class="ctcp_cpfw_selected_qty" name="ctcp_cpfw_selected_qty<?php echo esc_attr( $currenct_product_id ); ?>" min="<?php echo esc_attr( $min_qty ); ?>" value="<?php echo esc_attr( $min_qty ); ?>"

														<?php if ( isset( $product_detail['max_qty'] ) && $product_detail['max_qty'] >= 1 ) { ?>

															max="<?php echo esc_attr( $product_detail['max_qty'] ); ?>"

														<?php } ?>
														data-ctcp_cpfw_product_price="<?php echo esc_attr( $price ); ?>"
														>

													</td>


													<?php
													if ( get_option( 'ctcp_cpfw_show_product_des' ) ) {
														?>
														<td>
															<span><?php echo wp_kses_post( substr( $currenct_product->get_description(), 0, get_option( 'ctcp_cpfw_show_product_des_max_charater' ) ) ); ?></span>
														</td>

														<?php
													}

													if ( get_option( 'ctcp_cpfw_show_product_price' ) ) {
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

						<input type="hidden" name="ctcp_cpfw_rule_id" value="<?php echo esc_attr( $rule_id ); ?>">

						<input type="hidden" name="ctcp_cpfw_final_prds" value="<?php echo esc_attr( implode( ',', array_keys( $final_bundle_product ) ) ); ?>">

						<input type="hidden" name="ctcp_cpfw_min_purchase" class="ctcp_cpfw_min_purchase" value="<?php echo esc_attr( (int) get_post_meta( $rule_id, 'ctcp_cpfw_min_purchase', true ) ? get_post_meta( $rule_id, 'ctcp_cpfw_min_purchase', true ) : 1 ); ?>">

						<input type="hidden" name="ctcp_cpfw_max_purchase" class="ctcp_cpfw_max_purchase" value="<?php echo esc_attr( (int) get_post_meta( $rule_id, 'ctcp_cpfw_max_purchase', true ) ); ?>">


						<input type="hidden" class="ctcp_cpfw_main_prd_price" value="<?php echo esc_attr( (int) $product->get_price() ); ?>">
					</div>
					<?php

					$result = ob_get_clean();

					wp_send_json_success( array( 'new_html' => $result ) );
				}
			} else {

				wp_send_json_success( array( 'new_html' => ' No bundle found ' ) );

			}
		}

		wp_die();
	}
}
new Cpfw_Ajax_Callback();
