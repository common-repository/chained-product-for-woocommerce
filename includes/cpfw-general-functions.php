<?php
/**
 * General Class.
 *
 * @package : chain-product-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * General Class.
 *
 * @param string $post_type is used for post type.
 * @param string $parent_post_id is used for parant post id.
 * @return array post ids.
 */
function ctcpfw_get_post( $post_type, $parent_post_id = '' ) {

	$aurgs = array(
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'post_parent'    => $parent_post_id,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	);

	$post_data = get_posts( $aurgs );

	return $post_data;
}


/**
 * Get all chain product of current rule.
 *
 * @param int $rule_id is used to get detail about rule.
 */
function chain_product_bundle_table( $rule_id ) {

	$old_selected_prd = (array) get_post_meta( $rule_id, 'ctcpfw__selected_chain_prd', true );

	foreach ( $old_selected_prd as $selected_prd_ids => $selected_prd_dtail ) {

		if ( is_array( $selected_prd_dtail ) ) {

			$product = wc_get_product( $selected_prd_ids );

			$discount_type = array(
				'same_price'          => 'Same Price',
				'free'                => 'Free',
				'percentage_discount' => 'Percentage Discount',
				'fixed_discount'      => 'Fixed Discount',
			);

			$min_qty           = isset( $selected_prd_dtail['min_qty'] ) ? $selected_prd_dtail['min_qty'] : 1;
			$selected_qty_type = isset( $selected_prd_dtail['qty_type'] ) ? $selected_prd_dtail['qty_type'] : 1;

			$max_qty = isset( $selected_prd_dtail['max_qty'] ) ? $selected_prd_dtail['max_qty'] : '';

			$selected_discount_type = isset( $selected_prd_dtail['discount_type'] ) ? $selected_prd_dtail['discount_type'] : 'same_price';

			$discount_amount = isset( $selected_prd_dtail['discount_amount'] ) ? $selected_prd_dtail['discount_amount'] : 0;

			$required = isset( $selected_prd_dtail['required'] ) ? $selected_prd_dtail['required'] : '';

			$product_link = 'variation' === (string) $product->get_type() ? get_edit_post_link( $product->get_parent_id() ) : get_edit_post_link( $product->get_id() );

			$class_for_out_of_stock = 'outofstock' === (string) $product->get_stock_status() ? 'ct-out-of-stock' : '';

			?>

			<tr class="ct-cp-table-row-product-data <?php echo esc_attr( $class_for_out_of_stock ); ?>">


				<th class="ct-cp-product-name">
					<a href="<?php echo esc_url( $product_link ); ?>">
						<?php echo esc_attr( $product->get_name() ); ?>
					</a>
					<?php echo esc_attr( ' ( ' . $product->get_stock_status() . ' )' ); ?>

				</th>

				<td class="ct-cp-product-price-type">
					<?php
					$qty_type = array( 'linked', 'fixed' );

					?>
					<select class="selected_prd_discounted_type" name="selected_chain_prd_detail[<?php echo esc_attr( $selected_prd_ids ); ?>][qty_type]">
						<?php foreach ( $qty_type as $value ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php echo esc_attr( $value === $selected_qty_type ? 'selected' : '' ); ?>>
								<?php echo esc_attr( ucfirst( $value ) ); ?>
							</option>
						<?php endforeach ?>
					</select>

				</td>


				<td class="ct-cp-product-qty-type">
					<input type="number" min="1" name="selected_chain_prd_detail[<?php echo esc_attr( $selected_prd_ids ); ?>][min_qty]" value="<?php echo esc_attr( $min_qty ); ?>">
				</td>
				<td class="ct-cp-product-qty-type">
					<input type="number" min="1" name="selected_chain_prd_detail[<?php echo esc_attr( $selected_prd_ids ); ?>][max_qty]" value="<?php echo esc_attr( $max_qty ); ?>">
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
							<option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $key === $selected_discount_type ? 'selected' : '' ); ?>>
								<?php echo esc_attr( $value ); ?>
							</option>
						<?php endforeach ?>
					</select>

				</td>

				<td class="ct-cp-discount-amount">
					<input type="number" min="0" class="selected_prd_detail_discount_amount" name="selected_chain_prd_detail[<?php echo esc_attr( $selected_prd_ids ); ?>][discount_amount]" value="<?php echo esc_attr( $discount_amount ); ?>">

				</td>

				<td class="ct-remove-woo-product-bundle-td">
					<i class="ct-remove-woo-product-bundle" data-product_id="<?php echo esc_attr( $selected_prd_ids ); ?>" data-main_product_id="<?php echo esc_attr( $rule_id ); ?>" >X</i>
				</td>

			</tr>

			<?php
		}
	}
}

/**
 * Checking is this product id is match with any rule.
 *
 * @param int $product_id product id of current product.
 * @return array which will return rule id of match rule.
 */
function check_product_match_all_rule_or_not( $product_id ) {

	$user_role = is_user_logged_in() ? current( wp_get_current_user()->roles ) : 'guest';

	$old_selected_prd = (array) get_post_meta( $product_id, 'ctcpfw__selected_chain_prd', true );
	$old_selected_prd = array_filter( $old_selected_prd );

	$main_product = wc_get_product( $product_id );

	$flag = false;

	if ( count( $old_selected_prd ) >= 1 ) {

		$selected_user_role = get_post_meta( $product_id, 'ctcpfw__select_user_from_switch', true ) ? (array) get_post_meta( $product_id, 'ctcpfw__select_user_from_switch', true ) : array( $user_role );

		$start_date = get_post_meta( $product_id, 'ctcpfw__discount_start_date', true ) ? gmdate( 'Y-m-d', strtotime( get_post_meta( $product_id, 'ctcpfw__discount_start_date', true ) ) ) : gmdate( 'Y-m-d' );
		$start_date = strtotime( $start_date );

		$end_date = get_post_meta( $product_id, 'ctcpfw__discount_end_date', true ) ? gmdate( 'Y-m-d', strtotime( get_post_meta( $product_id, 'ctcpfw__discount_end_date', true ) ) ) : gmdate( 'Y-m-d' );

		$end_date = strtotime( $end_date );

		$current_date = strtotime( gmdate( 'Y-m-d' ) );

		if ( $current_date < $start_date || $current_date > $end_date ) {
			return;
		}

		if ( ! in_array( (string) $user_role, $selected_user_role, true ) ) {

			return;
		}

		foreach ( $old_selected_prd as $bndle_prd_id => $data_arra ) {

			if ( is_array( $data_arra ) && count( $data_arra ) >= 1 ) {

				$current_product = wc_get_product( $bndle_prd_id );

				if ( ! $current_product->is_in_stock() ) {

					if ( 'remove_all_product' === (string) get_option( 'ctcpfw__out_of_stock_product' ) ) {

						return array();

					}

					unset( $old_selected_prd[ $bndle_prd_id ] );

				}
			}
		}

		$old_selected_prd = array_filter( $old_selected_prd );

		if ( count( $old_selected_prd ) >= 1 ) {

			return array(
				'rule_id'       => $product_id,
				'product_array' => $old_selected_prd,
			);

		}
	} else {

		$get_rules = (array) ctcpfw_get_post( 'ct_chain_product' );

		if ( count( $get_rules ) >= 1 ) {

			foreach ( $get_rules as $current_rule_id ) {

				if ( $current_rule_id ) {

					$selected_user_role = get_post_meta( $current_rule_id, 'ctcpfw__select_user_from_switch', true ) ? (array) get_post_meta( $current_rule_id, 'ctcpfw__select_user_from_switch', true ) : array( $user_role );

					$excluded_product = (array) get_post_meta( $current_rule_id, 'ctcpfw__product_exclusion_list', true );
					$excluded_product = array_filter( $excluded_product );

					$included_product = (array) get_post_meta( $current_rule_id, 'ctcpfw__product_included_list', true );
					$included_product = array_filter( $included_product );

					$selected_categorie = (array) get_post_meta( $current_rule_id, 'ctcpfw__included_category', true );
					$selected_categorie = array_filter( $selected_categorie );

					$selected_tags = (array) get_post_meta( $current_rule_id, 'ctcpfw__product_tags', true );
					$selected_tags = array_filter( $selected_tags );

					$start_date = get_post_meta( $current_rule_id, 'ctcpfw__discount_start_date', true ) ? gmdate( 'Y-m-d', strtotime( get_post_meta( $current_rule_id, 'ctcpfw__discount_start_date', true ) ) ) : gmdate( 'Y-m-d' );

					$start_date = strtotime( $start_date );

					$end_date = get_post_meta( $current_rule_id, 'ctcpfw__discount_end_date', true ) ? gmdate( 'Y-m-d', strtotime( get_post_meta( $current_rule_id, 'ctcpfw__discount_end_date', true ) ) ) : gmdate( 'Y-m-d' );

					$end_date = strtotime( $end_date );

					$current_date = strtotime( gmdate( 'Y-m-d' ) );

					if ( $current_date < $start_date || $current_date > $end_date ) {

						continue;
					}

					if ( in_array( (string) $product_id, $excluded_product, true ) ) {

						continue;
					}

					if ( ! in_array( (string) $user_role, $selected_user_role, true ) ) {

						continue;
					}

					if ( count( $selected_categorie ) < 1 && count( $included_product ) < 1 && count( $selected_tags ) < 1 ) {

						$flag = true;
					}

					if ( in_array( (string) $product_id, $included_product, true ) ) {

						$flag = true;
					}

					foreach ( $selected_categorie as $cat_id ) {

						if ( $cat_id && has_term( $cat_id, 'product_cat', $product_id ) ) {

							$flag = true;

						}
					}

					if ( count( $selected_tags ) >= 1 && has_term( $selected_tags, 'product_tag', $product_id ) ) {

						$flag = true;

					}

					if ( $flag ) {

						$old_selected_prd = (array) get_post_meta( $current_rule_id, 'ctcpfw__selected_chain_prd', true );
						$old_selected_prd = array_filter( $old_selected_prd );

						foreach ( $old_selected_prd as $bndle_prd_id => $data_arra ) {

							if ( is_array( $data_arra ) && count( $data_arra ) >= 1 ) {

								$current_product = wc_get_product( $bndle_prd_id );

								if ( ! $current_product->is_in_stock() ) {

									if ( 'remove_all_product' === (string) get_option( 'ctcpfw__out_of_stock_product' ) ) {

										$old_selected_prd = array();

										continue 2;

									} else {

										unset( $old_selected_prd[ $bndle_prd_id ] );

									}
								}
							}
						}

						$old_selected_prd = array_filter( $old_selected_prd );

						if ( count( $old_selected_prd ) >= 1 ) {

							return array(
								'rule_id'       => $current_rule_id,
								'product_array' => $old_selected_prd,
							);

						}
					}
				}
			}
		}
	}

	return array();
}
