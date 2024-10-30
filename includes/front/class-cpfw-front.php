<?php
/**
 * Front class start.
 *
 * @package : chain-product-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Cpfw_Front' ) ) {
	class Cpfw_Front {

		public function __construct() {

			add_action( 'wp_loaded', array( $this, 'cpfw_wp_loaded' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'cpfw_enque_scripts' ) );

			add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'cpfw_update_product_data' ), 10, 4 );

			add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'cpfw_woocommerce_add_to_cart_button_text' ), 10, 2 );
			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'cpfw_filter_loop_add_to_cart_link' ), 10, 2 );

			// calculate fee.
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'cpfw_get_cart_item_from_session' ), 20, 2 );
			add_filter( 'woocommerce_add_cart_item', array( $this, 'cpfw_caluclute_fee' ), 20 );
		}

		public function cpfw_enque_scripts() {

			wp_enqueue_style( 'front_css1', CTCP_PLUGIN_URL . '/assets/css/ct-cpfw-front.css', false, '1.1.0' );
			wp_enqueue_script( 'front_js', CTCP_PLUGIN_URL . '/assets/js/ct-cpfw-front.js', array( 'jquery' ), '1.1.0', false );

			$scripts = array(
				'admin_url' => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'ctcpfw__nonce' ),
			);
			wp_localize_script( 'front_js', 'php_var', $scripts );
		}

		public function cpfw_wp_loaded() {

			if ( 'before_add_to_cart' == (string) get_option( 'ctcpfw__bundle_postions' ) ) {

				add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'cpfw_show_bundle_product' ), 20 );

			} else {

				add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'cpfw_show_bundle_product' ) );

			}

			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'cpfw_add_chain_product' ), 5, 3 );

			add_filter( 'woocommerce_get_price_html', array( $this, 'cpfw_woocommerce_get_price_html' ), 10, 2 );

			add_filter( 'woocommerce_get_item_data', array( $this, 'cpfw_woocommerce_get_item_data' ), 10, 2 );
		}

		public function cpfw_filter_loop_add_to_cart_link( $button, $product ) {

			$bundle_detail = (array) check_product_match_all_rule_or_not( $product->get_id() );

			if ( isset( $bundle_detail['product_array'] ) && count( $bundle_detail['product_array'] ) >= 1 && isset( $bundle_detail['rule_id'] ) && get_post_meta( $bundle_detail['rule_id'], 'ctcpfw__add_to_cart_button_text', true ) ) {

				$rule_id = $bundle_detail['rule_id'];
				if ( self::user_is_able_to_get_chain_product( $bundle_detail['rule_id'], $product->get_id() ) ) {

					$icon = '';

					if ( ! empty( get_post_meta( $rule_id, 'ctcpfw__add_to_cart_button_icon', true ) ) ) {

						$icon = '<i class=" fa ' . esc_attr( get_post_meta( $rule_id, 'ctcpfw__add_to_cart_button_icon', true ) ) . '"></i>';

						if ( 'upload_custom_icon' == (string) get_post_meta( $rule_id, 'ctcpfw__add_to_cart_button_icon', true ) && get_post_meta( $rule_id, 'ctcpfw__add_to_crt_btn_uploaded_icon', true ) ) {

							$icon = '<img src="' . esc_url( get_post_meta( $rule_id, 'ctcpfw__add_to_crt_btn_uploaded_icon', true ) ) . '" style="width:50px;height: 50px;">';

						}

						if ( 'custom_icon_class' == (string) get_post_meta( $rule_id, 'ctcpfw__add_to_cart_button_icon', true ) && get_post_meta( $rule_id, 'ctcpfw__add_to_crt_btn_icon_class', true ) ) {
							$icon = '<i class=" fa ' . esc_attr( get_post_meta( $rule_id, 'ctcpfw__add_to_crt_btn_icon_class', true ) ) . '"></i>';

						}
					}

					$button_text = str_replace( '{icon}', $icon, get_post_meta( $rule_id, 'ctcpfw__add_to_cart_button_text', true ) );

					if ( ! empty( get_post_meta( $rule_id, 'ctcpfw__enable_add_to_cart_button_styling', true ) ) ) {

						$ctcpfw__add_to_cart_styling = (array) get_post_meta( $rule_id, 'ctcpfw__add_to_cart_styling', true );

						$button_text_color = isset( $ctcpfw__add_to_cart_styling['button_text_color'] ) ? $ctcpfw__add_to_cart_styling['button_text_color'] : '';

						$button_text_bgcolor = isset( $ctcpfw__add_to_cart_styling['button_text_bgcolor'] ) ? $ctcpfw__add_to_cart_styling['button_text_bgcolor'] : '';

						$button_text_hover_color   = isset( $ctcpfw__add_to_cart_styling['button_text_hover_color'] ) ? $ctcpfw__add_to_cart_styling['button_text_hover_color'] : '';
						$button_text_hover_bgcolor = isset( $ctcpfw__add_to_cart_styling['button_text_hover_bgcolor'] ) ? $ctcpfw__add_to_cart_styling['button_text_hover_bgcolor'] : '';

						$button_text_border_color = isset( $ctcpfw__add_to_cart_styling['button_text_border_color'] ) ? $ctcpfw__add_to_cart_styling['button_text_border_color'] : '';

						$button_font_size = isset( $ctcpfw__add_to_cart_styling['button_font_size'] ) ? $ctcpfw__add_to_cart_styling['button_font_size'] : 14;

						$button_font_weight = isset( $ctcpfw__add_to_cart_styling['button_font_weight'] ) ? $ctcpfw__add_to_cart_styling['button_font_weight'] : '100';

						$button_border_radius = isset( $ctcpfw__add_to_cart_styling['button_border_radius'] ) ? $ctcpfw__add_to_cart_styling['button_border_radius'] : '';

						$button_border = isset( $ctcpfw__add_to_cart_styling['button_border'] ) ? $ctcpfw__add_to_cart_styling['button_border'] : '';

						$button_border_padding_left = isset( $ctcpfw__add_to_cart_styling['button_border_padding_left'] ) ? $ctcpfw__add_to_cart_styling['button_border_padding_left'] : '';

						$button_border_padding_top = isset( $ctcpfw__add_to_cart_styling['button_border_padding_top'] ) ? $ctcpfw__add_to_cart_styling['button_border_padding_top'] : '';

						$button_border_padding_bottom = isset( $ctcpfw__add_to_cart_styling['button_border_padding_bottom'] ) ? $ctcpfw__add_to_cart_styling['button_border_padding_bottom'] : '';

						$button_border_padding_right = isset( $ctcpfw__add_to_cart_styling['button_border_padding_right'] ) ? $ctcpfw__add_to_cart_styling['button_border_padding_right'] : '';

						$button_border_margin_left = isset( $ctcpfw__add_to_cart_styling['button_border_margin_left'] ) ? $ctcpfw__add_to_cart_styling['button_border_margin_left'] : '';

						$button_border_margin_top = isset( $ctcpfw__add_to_cart_styling['button_border_margin_top'] ) ? $ctcpfw__add_to_cart_styling['button_border_margin_top'] : '';

						$button_border_margin_bottom = isset( $ctcpfw__add_to_cart_styling['button_border_margin_bottom'] ) ? $ctcpfw__add_to_cart_styling['button_border_margin_bottom'] : '';

						$button_border_margin_right = isset( $ctcpfw__add_to_cart_styling['button_border_margin_right'] ) ? $ctcpfw__add_to_cart_styling['button_border_margin_right'] : '';

						?>
						<style type="text/css">
							.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?> {

								color: <?php echo esc_attr( $button_text_color ); ?> !important;
								background-color: <?php echo esc_attr( $button_text_bgcolor ); ?> !important;
								font-size: <?php echo esc_attr( $button_font_size ); ?>px;
								font-weight: <?php echo esc_attr( $button_font_weight ); ?>;
								border: <?php echo esc_attr( $button_border ); ?>px;
								border-radius: <?php echo esc_attr( $button_border_radius ); ?>px;
								padding: <?php echo esc_attr( $button_border_padding_top . ' ' . $button_border_padding_right . ' ' . $button_border_padding_bottom . ' ' . $button_border_padding_left ); ?>;
								margin: <?php echo esc_attr( $button_border_margin_top . ' ' . $button_border_margin_right . ' ' . $button_border_margin_bottom . ' ' . $button_border_margin_left ); ?>;

							}
							.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?>:hover,
							.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?>:active {

								color: <?php echo esc_attr( $button_text_hover_color ); ?> !important;
								background-color: <?php echo esc_attr( $button_text_hover_bgcolor ); ?> !important;
							}
						</style>
						<?php

						$button_text = '<i data-hover_color="' . esc_attr( $button_text_hover_color ) . '" data-rule_id="' . esc_attr( $rule_id ) . '" class="ct-cpfw-add-to-cart-custom-button" >'
						. esc_attr( $button_text ) . '</i>';

					}

					$button = '<a href="?add-to-cart' . $product->get_id() . '=" data-quantity="1" class="button catcb_css product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . $product->get_id() . '" data-product_sku="' . $product->get_sku() . '" aria-label="Add “Album” to your cart" rel="nofollow">' . $button_text . '</a>';

				}
			}

			if ( 'variable' == (string) $product->get_type() ) {

				foreach ( $product->get_children() as $product_id ) {

					$bundle_detail = (array) check_product_match_all_rule_or_not( $product_id );

					if ( isset( $bundle_detail['product_array'] ) && count( $bundle_detail['product_array'] ) >= 1 && isset( $bundle_detail['rule_id'] ) && get_post_meta( $bundle_detail['rule_id'], 'ctcpfw__add_to_cart_button_text', true ) ) {

						$rule_id = $bundle_detail['rule_id'];
						if ( self::user_is_able_to_get_chain_product( $bundle_detail['rule_id'], $product_id ) ) {

							$icon = '';

							if ( ! empty( get_post_meta( $rule_id, 'ctcpfw__add_to_cart_button_icon', true ) ) ) {
								$icon = '<i class=" fa ' . esc_attr( get_post_meta( $rule_id, 'ctcpfw__add_to_cart_button_icon', true ) ) . '"></i>';

								if ( 'upload_custom_icon' == (string) get_post_meta( $rule_id, 'ctcpfw__add_to_cart_button_icon', true ) && get_post_meta( $rule_id, 'ctcpfw__add_to_crt_btn_uploaded_icon', true ) ) {

									$icon = '<img src="' . esc_url( get_post_meta( $rule_id, 'ctcpfw__add_to_crt_btn_uploaded_icon', true ) ) . '" style="width:50px;height: 50px;">';

								}

								if ( 'custom_icon_class' == (string) get_post_meta( $rule_id, 'ctcpfw__add_to_cart_button_icon', true ) && get_post_meta( $rule_id, 'ctcpfw__add_to_crt_btn_icon_class', true ) ) {
									$icon = '<i class=" fa ' . esc_attr( get_post_meta( $rule_id, 'ctcpfw__add_to_crt_btn_icon_class', true ) ) . '"></i>';

								}
							}

							$button_text = str_replace( '{icon}', $icon, get_post_meta( $rule_id, 'ctcpfw__add_to_cart_button_text', true ) );

							if ( ! empty( get_post_meta( $rule_id, 'ctcpfw__enable_add_to_cart_button_styling', true ) ) ) {

								$ctcpfw__add_to_cart_styling = (array) get_post_meta( $rule_id, 'ctcpfw__add_to_cart_styling', true );

								$button_text_color = isset( $ctcpfw__add_to_cart_styling['button_text_color'] ) ? $ctcpfw__add_to_cart_styling['button_text_color'] : '';

								$button_text_bgcolor = isset( $ctcpfw__add_to_cart_styling['button_text_bgcolor'] ) ? $ctcpfw__add_to_cart_styling['button_text_bgcolor'] : '';

								$button_text_hover_color   = isset( $ctcpfw__add_to_cart_styling['button_text_hover_color'] ) ? $ctcpfw__add_to_cart_styling['button_text_hover_color'] : '';
								$button_text_hover_bgcolor = isset( $ctcpfw__add_to_cart_styling['button_text_hover_bgcolor'] ) ? $ctcpfw__add_to_cart_styling['button_text_hover_bgcolor'] : '';

								$button_text_border_color = isset( $ctcpfw__add_to_cart_styling['button_text_border_color'] ) ? $ctcpfw__add_to_cart_styling['button_text_border_color'] : '';

								$button_font_size = isset( $ctcpfw__add_to_cart_styling['button_font_size'] ) ? $ctcpfw__add_to_cart_styling['button_font_size'] : 14;

								$button_font_weight = isset( $ctcpfw__add_to_cart_styling['button_font_weight'] ) ? $ctcpfw__add_to_cart_styling['button_font_weight'] : '100';

								$button_border_radius = isset( $ctcpfw__add_to_cart_styling['button_border_radius'] ) ? $ctcpfw__add_to_cart_styling['button_border_radius'] : '';
								$button_border        = isset( $ctcpfw__add_to_cart_styling['button_border'] ) ? $ctcpfw__add_to_cart_styling['button_border'] : '';

								$button_border_padding_left = isset( $ctcpfw__add_to_cart_styling['button_border_padding_left'] ) ? $ctcpfw__add_to_cart_styling['button_border_padding_left'] : '';

								$button_border_padding_top = isset( $ctcpfw__add_to_cart_styling['button_border_padding_top'] ) ? $ctcpfw__add_to_cart_styling['button_border_padding_top'] : '';

								$button_border_padding_bottom = isset( $ctcpfw__add_to_cart_styling['button_border_padding_bottom'] ) ? $ctcpfw__add_to_cart_styling['button_border_padding_bottom'] : '';

								$button_border_padding_right = isset( $ctcpfw__add_to_cart_styling['button_border_padding_right'] ) ? $ctcpfw__add_to_cart_styling['button_border_padding_right'] : '';

								$button_border_margin_left = isset( $ctcpfw__add_to_cart_styling['button_border_margin_left'] ) ? $ctcpfw__add_to_cart_styling['button_border_margin_left'] : '';

								$button_border_margin_top = isset( $ctcpfw__add_to_cart_styling['button_border_margin_top'] ) ? $ctcpfw__add_to_cart_styling['button_border_margin_top'] : '';

								$button_border_margin_bottom = isset( $ctcpfw__add_to_cart_styling['button_border_margin_bottom'] ) ? $ctcpfw__add_to_cart_styling['button_border_margin_bottom'] : '';

								$button_border_margin_right = isset( $ctcpfw__add_to_cart_styling['button_border_margin_right'] ) ? $ctcpfw__add_to_cart_styling['button_border_margin_right'] : '';

								?>
								<style type="text/css">
									.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?> {

										color: <?php echo esc_attr( $button_text_color ); ?> !important;
										background-color: <?php echo esc_attr( $button_text_bgcolor ); ?> !important;
										font-size: <?php echo esc_attr( $button_font_size ); ?>px;
										font-weight: <?php echo esc_attr( $button_font_weight ); ?>;
										border: <?php echo esc_attr( $button_border ); ?>px;
										border-radius: <?php echo esc_attr( $button_border_radius ); ?>px;
										padding: <?php echo esc_attr( $button_border_padding_top . ' ' . $button_border_padding_right . ' ' . $button_border_padding_bottom . ' ' . $button_border_padding_left ); ?>;
										margin: <?php echo esc_attr( $button_border_margin_top . ' ' . $button_border_margin_right . ' ' . $button_border_margin_bottom . ' ' . $button_border_margin_left ); ?>;

									}
									.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?>:hover,
									.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?>:active {

										color: <?php echo esc_attr( $button_text_hover_color ); ?> !important;
										background-color: <?php echo esc_attr( $button_text_hover_bgcolor ); ?> !important;
									}
								</style>
								<?php

								$button_text = '<i data-hover_color="' . esc_attr( $button_text_hover_color ) . '" data-rule_id="' . esc_attr( $rule_id ) . '" class="ct-cpfw-add-to-cart-custom-button ">'
								. esc_attr( $button_text ) . '</i>';

							}

							$button = '<a href="?add-to-cart' . $product->get_id() . '=" data-quantity="1" class="button catcb_css product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . $product->get_id() . '" data-product_sku="' . $product->get_sku() . '" aria-label="Add “Album” to your cart" rel="nofollow">' . $button_text . '</a>';

							break;
						}
					}
				}
			}

			return $button;
		}
		public function cpfw_woocommerce_add_to_cart_button_text( $text ) {

			global $product;

			$bundle_detail = (array) check_product_match_all_rule_or_not( $product->get_id() );

			if ( isset( $bundle_detail['product_array'] ) && count( $bundle_detail['product_array'] ) >= 1 && isset( $bundle_detail['rule_id'] ) && get_post_meta( $bundle_detail['rule_id'], 'ctcpfw__add_to_cart_button_text', true ) ) {
				if ( self::user_is_able_to_get_chain_product( $bundle_detail['rule_id'], $product->get_id() ) ) {

					$rule_id = $bundle_detail['rule_id'];

					ob_start();

					echo wp_kses_post( $this->get_add_cart_button_text( $bundle_detail['rule_id'] ) );

					$text = ob_get_clean();

				}
			}

			if ( 'variable' == (string) $product->get_type() ) {

				foreach ( $product->get_children() as $product_id ) {

					$bundle_detail = (array) check_product_match_all_rule_or_not( $product_id );

					if ( isset( $bundle_detail['product_array'] ) && count( $bundle_detail['product_array'] ) >= 1 && isset( $bundle_detail['rule_id'] ) && get_post_meta( $bundle_detail['rule_id'], 'ctcpfw__add_to_cart_button_text', true ) ) {
						if ( self::user_is_able_to_get_chain_product( $bundle_detail['rule_id'], $product_id ) ) {

							$rule_id = $bundle_detail['rule_id'];

							ob_start();

							echo wp_kses_post( $this->get_add_cart_button_text( $bundle_detail['rule_id'] ) );

							$text = ob_get_clean();

							break;
						}
					}
				}
			}

			return $text;
		}
		public function cpfw_woocommerce_get_price_html( $price, $product ) {

			$bundle_detail = (array) check_product_match_all_rule_or_not( $product->get_id() );

			if ( isset( $bundle_detail['product_array'] ) && count( $bundle_detail['product_array'] ) >= 1 && isset( $bundle_detail['rule_id'] ) && get_post_meta( $bundle_detail['rule_id'], 'ctcpfw__custom_price_name', true ) ) {
				if ( self::user_is_able_to_get_chain_product( $bundle_detail['rule_id'], $product->get_id() ) ) {

					$price = get_post_meta( $bundle_detail['rule_id'], 'ctcpfw__custom_price_name', true );
				}
			}

			return $price;
		}

		public function cpfw_show_bundle_product() {
			global $product;

			wp_nonce_field( 'ctcpfw__nonce', 'ctcpfw__nonce' );

			?>
			<div class="ct-cpfw-final-product-bundle-main-div">
				<?php

				if ( 'simple' == (string) $product->get_type() ) {

					$bundle_detail = (array) check_product_match_all_rule_or_not( $product->get_id() );

					if ( isset( $bundle_detail['product_array'] ) && count( $bundle_detail['product_array'] ) >= 1 && isset( $bundle_detail['rule_id'] ) ) {
						if ( self::user_is_able_to_get_chain_product( $bundle_detail['rule_id'], $product->get_id() ) ) {

							include CTCP_PLUGIN_DIR . 'templates/cp-product-page.php';
						}
					}
				}

				?>
			</div>
			<?php
		}


		public function cpfw_get_cart_item_from_session( $cart_item, $values ) {

			if ( ! empty( $values['ctcp_chain_product_bundle'] ) ) {
				$cart_item['ctcp_chain_product_bundle'] = $values['ctcp_chain_product_bundle'];
				$cart_item                              = $this->cpfw_caluclute_fee( $cart_item );
			}
			return $cart_item;
		}

		public function cpfw_caluclute_fee( $cart_item_data ) {

			$files_total = 0;

			if ( isset( $cart_item_data['ctcp_chain_product_bundle'] ) && ! empty( $cart_item_data['ctcp_chain_product_bundle']['rule_id'] ) ) {

				$main_product_qty = $cart_item_data['quantity'];

				$selected_product = $cart_item_data['ctcp_chain_product_bundle'];

				unset( $selected_product['rule_id'] );

				foreach ( $selected_product as $current_products_id => $product_detail_array ) {

					$current_products_obj = wc_get_product( $current_products_id );
					$selected_qty         = isset( $product_detail_array['selected_qty'] ) ? (int) $product_detail_array['selected_qty'] : 1;

					if ( isset( $product_detail_array['min_qty'] ) && ! empty( $product_detail_array['min_qty'] ) && $selected_qty < (int) $product_detail_array['min_qty'] ) {

						$selected_qty = (int) $product_detail_array['min_qty'];

					}
					if ( isset( $product_detail_array['max_qty'] ) && ! empty( $product_detail_array['max_qty'] ) && $selected_qty > (int) $product_detail_array['max_qty'] ) {

						$selected_qty = (int) $product_detail_array['max_qty'];

					}

					$qty_type = isset( $product_detail_array['qty_type'] ) ? $product_detail_array['qty_type'] : 'linked';

					if ( 'linked' == (string) $qty_type ) {

						$selected_qty *= $main_product_qty;

					}

					$price = $current_products_obj->get_price();

					$discount_type = isset( $product_detail_array['discount_type'] ) ? $product_detail_array['discount_type'] : 'same_price';

					$discount_amount = isset( $product_detail_array['discount_amount'] ) ? (float) $product_detail_array['discount_amount'] : 0;

					if ( 'free' == (string) $discount_type ) {

						$price = 0;

					} elseif ( 'fixed_discount' == (string) $discount_type ) {

						$price -= $discount_amount;

					} elseif ( 'percentage_discount' == (string) $discount_type ) {

						$price -= ( $discount_amount / 100 ) * $price;
					}

					$files_total += $price * $selected_qty;

				}

				$prd_price = $cart_item_data['data']->get_price() * $cart_item_data['quantity'];
				$cart_item_data['data']->set_price( ( $files_total + $prd_price ) / $cart_item_data['quantity'] );

			}
			return $cart_item_data;
		}


		public function cpfw_add_chain_product( $cart_item_data, $product_id, $variation_id = 0 ) {

			$bundle_detail = (array) check_product_match_all_rule_or_not( $product_id );

			$product_have_chain_product_or_not = false;

			if ( isset( $bundle_detail['product_array'] ) && count( $bundle_detail['product_array'] ) >= 1 && isset( $bundle_detail['rule_id'] ) ) {

				if ( self::user_is_able_to_get_chain_product( $bundle_detail['rule_id'], $product_id ) ) {

					$rule_id = $bundle_detail['rule_id'];

					$final_bundle_product = $bundle_detail['product_array'];

					$cart_item_data['ctcp_chain_product_bundle']['rule_id'] = $rule_id;

					foreach ( $final_bundle_product as $bundle_product_id => $bundle_product_detail ) {
						$product_have_chain_product_or_not = true;

						$product_detail                 = $final_bundle_product[ $bundle_product_id ];
						$product_detail['selected_qty'] = isset( $_POST[ 'ctcpfw__selected_qty' . $bundle_product_id ] ) ? sanitize_text_field( $_POST[ 'ctcpfw__selected_qty' . $bundle_product_id ] ) : 1;

						$cart_item_data['ctcp_chain_product_bundle'][ $bundle_product_id ] = $product_detail;

					}
				}
			}

			$bundle_detail = (array) check_product_match_all_rule_or_not( $variation_id );

			if ( ! isset( $cart_item_data['ctcp_chain_product_bundle'] ) && ! $product_have_chain_product_or_not && isset( $bundle_detail['product_array'] ) && count( $bundle_detail['product_array'] ) >= 1 && isset( $bundle_detail['rule_id'] ) ) {

				if ( self::user_is_able_to_get_chain_product( $bundle_detail['rule_id'], $variation_id ) ) {

					$rule_id = $bundle_detail['rule_id'];

					$final_bundle_product = $bundle_detail['product_array'];

					$cart_item_data['ctcp_chain_product_bundle']['rule_id'] = $rule_id;

					foreach ( $final_bundle_product as $bundle_product_id => $bundle_product_detail ) {

						$product_detail = $final_bundle_product[ $bundle_product_id ];

						$product_detail['selected_qty'] = isset( $_POST[ 'ctcpfw__selected_qty' . $bundle_product_id ] ) ? sanitize_text_field( $_POST[ 'ctcpfw__selected_qty' . $bundle_product_id ] ) : 1;

						$nonce = isset( $_POST[ 'ctcpfw__selected_qty' . $bundle_product_id ] ) && isset( $_POST['ctcpfw__nonce'] ) ? sanitize_text_field( $_POST['ctcpfw__nonce'] ) : '';

						if ( ! wp_verify_nonce( $nonce, 'ctcpfw__nonce' ) ) {
							wp_die( esc_html__( 'Security Violate!', 'chain-product-for-woocommerce' ) );
						}

						$cart_item_data['ctcp_chain_product_bundle'][ $bundle_product_id ] = $product_detail;

					}
				}
			}

			return $cart_item_data;
		}

		public function cpfw_woocommerce_get_item_data( $item_data, $cart_item ) {

			if ( isset( $cart_item['ctcp_chain_product_bundle'] ) && isset( $cart_item['ctcp_chain_product_bundle']['rule_id'] ) ) {

				$selected_product = $cart_item['ctcp_chain_product_bundle'];

				$rule_id = $selected_product['rule_id'];

				$main_product_qty = isset( $cart_item['quantity'] ) ? (int) $cart_item['quantity'] : 1;

				unset( $selected_product['rule_id'] );

				if ( is_cart() && ! empty( get_post_meta( $rule_id, 'ctcpfw__show_bundle_on_cart_pg', true ) ) ) {
					include CTCP_PLUGIN_DIR . 'templates/cp-on-cart-page.php';
				}

				if ( is_checkout() && ! empty( get_post_meta( $rule_id, 'ctcpfw__show_bundle_on_checkout_pg', true ) ) ) {
					include CTCP_PLUGIN_DIR . 'templates/cp-on-checkout-page.php';
				}
			}

			return $item_data;
		}

		public function cpfw_update_product_data( $item, $cart_item_key, $values, $order ) {

			foreach ( WC()->cart->get_cart() as $item_key => $value_check ) {

				if ( $item_key == $cart_item_key && ! empty( $value_check['ctcp_chain_product_bundle'] ) ) {

					$get_data_of_files = $value_check['ctcp_chain_product_bundle'];

					$get_data_of_files = array_filter( $get_data_of_files );

					if ( count( $get_data_of_files ) >= 1 ) {

						if ( isset( $get_data_of_files['rule_id'] ) ) {

							$newpost_id = wp_insert_post(
								array(
									'post_type'   => 'ct_who_buy_chain_prd',
									'post_status' => 'publish',
									'post_parent' => $get_data_of_files['rule_id'],
								)
							);

							update_post_meta(
								$newpost_id,
								'ct_who_purchase_this_bundle',
								array(
									'rule_id'      => $get_data_of_files['rule_id'],
									'current_user' => get_current_user_id(),
									'product_id'   => $value_check['product_id'],
									'variation_id' => $value_check['variation_id'],
								)
							);

						}

						$item->add_meta_data( 'ctcp_chain_product_bundle', $get_data_of_files );
					}
				}
			}
		}

		public function get_add_cart_button_text( $rule_id ) {

			$icon = '';

			if ( ! empty( get_post_meta( $rule_id, 'ctcpfw__add_to_cart_button_icon', true ) ) ) {

				$icon = '<i class=" fa ' . esc_attr( get_post_meta( $rule_id, 'ctcpfw__add_to_cart_button_icon', true ) ) . '"></i>';

				if ( 'upload_custom_icon' == (string) get_post_meta( $rule_id, 'ctcpfw__add_to_cart_button_icon', true ) && get_post_meta( $rule_id, 'ctcpfw__add_to_crt_btn_uploaded_icon', true ) ) {

					$icon = '<img src="' . esc_url( get_post_meta( $rule_id, 'ctcpfw__add_to_crt_btn_uploaded_icon', true ) ) . '" style="width:50px;height: 50px;">';

				}

				if ( 'custom_icon_class' == (string) get_post_meta( $rule_id, 'ctcpfw__add_to_cart_button_icon', true ) && get_post_meta( $rule_id, 'ctcpfw__add_to_crt_btn_icon_class', true ) ) {
					$icon = '<i class=" fa ' . esc_attr( get_post_meta( $rule_id, 'ctcpfw__add_to_crt_btn_icon_class', true ) ) . '"></i>';

				}
			}

			$button_text = str_replace( '{icon}', $icon, get_post_meta( $rule_id, 'ctcpfw__add_to_cart_button_text', true ) );

			if ( ! empty( get_post_meta( $rule_id, 'ctcpfw__enable_add_to_cart_button_styling', true ) ) ) {

				$ctcpfw__add_to_cart_styling = (array) get_post_meta( $rule_id, 'ctcpfw__add_to_cart_styling', true );

				$button_text_color = isset( $ctcpfw__add_to_cart_styling['button_text_color'] ) ? $ctcpfw__add_to_cart_styling['button_text_color'] : '';

				$button_text_bgcolor = isset( $ctcpfw__add_to_cart_styling['button_text_bgcolor'] ) ? $ctcpfw__add_to_cart_styling['button_text_bgcolor'] : '';

				$button_text_hover_color   = isset( $ctcpfw__add_to_cart_styling['button_text_hover_color'] ) ? $ctcpfw__add_to_cart_styling['button_text_hover_color'] : '';
				$button_text_hover_bgcolor = isset( $ctcpfw__add_to_cart_styling['button_text_hover_bgcolor'] ) ? $ctcpfw__add_to_cart_styling['button_text_hover_bgcolor'] : '';

				$button_text_border_color = isset( $ctcpfw__add_to_cart_styling['button_text_border_color'] ) ? $ctcpfw__add_to_cart_styling['button_text_border_color'] : '';

				$button_font_size = isset( $ctcpfw__add_to_cart_styling['button_font_size'] ) ? $ctcpfw__add_to_cart_styling['button_font_size'] : 14;

				$button_font_weight = isset( $ctcpfw__add_to_cart_styling['button_font_weight'] ) ? $ctcpfw__add_to_cart_styling['button_font_weight'] : '100';

				$button_border_radius = isset( $ctcpfw__add_to_cart_styling['button_border_radius'] ) ? $ctcpfw__add_to_cart_styling['button_border_radius'] : '';
				$button_border        = isset( $ctcpfw__add_to_cart_styling['button_border'] ) ? $ctcpfw__add_to_cart_styling['button_border'] : '';

				$button_border_padding_left = isset( $ctcpfw__add_to_cart_styling['button_border_padding_left'] ) ? $ctcpfw__add_to_cart_styling['button_border_padding_left'] : '';

				$button_border_padding_top = isset( $ctcpfw__add_to_cart_styling['button_border_padding_top'] ) ? $ctcpfw__add_to_cart_styling['button_border_padding_top'] : '';

				$button_border_padding_bottom = isset( $ctcpfw__add_to_cart_styling['button_border_padding_bottom'] ) ? $ctcpfw__add_to_cart_styling['button_border_padding_bottom'] : '';

				$button_border_padding_right = isset( $ctcpfw__add_to_cart_styling['button_border_padding_right'] ) ? $ctcpfw__add_to_cart_styling['button_border_padding_right'] : '';

				?>
				<style type="text/css">
					.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?> {

						color: <?php echo esc_attr( $button_text_color ); ?> !important;
						background-color: <?php echo esc_attr( $button_text_bgcolor ); ?> !important;
						font-size: <?php echo esc_attr( $button_font_size ); ?>px;
						font-weight: <?php echo esc_attr( $button_font_weight ); ?>;
						border: <?php echo esc_attr( $button_border ); ?>px;
						border-radius: <?php echo esc_attr( $button_border_radius ); ?>px;
						padding: <?php echo esc_attr( $button_border_padding_top . ' ' . $button_border_padding_right . ' ' . $button_border_padding_bottom . ' ' . $button_border_padding_left ); ?>;
					}

					.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?>:hover,
					.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?>:active {

						color: <?php echo esc_attr( $button_text_hover_color ); ?> !important;
						background-color: <?php echo esc_attr( $button_text_hover_bgcolor ); ?> !important;
					}
				</style>
				<?php

				$button_text = '<i data-hover_color="' . esc_attr( $button_text_hover_color ) . '" data-rule_id="' . esc_attr( $rule_id ) . '" class="ct-cpfw-add-to-cart-custom-button">'
				. esc_attr( $button_text ) . '</i>';

				return $button_text;

			}

			return $button_text;
		}

		public static function user_is_able_to_get_chain_product( $rule_id, $product_id ) {

			$user_is_able = true;

			$ctcpfw_get_post_id = get_posts(
				array(
					'post_type'      => 'ct_who_buy_chain_prd',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'fields'         => 'ids',
				)
			);

			if ( empty( get_post_meta( $rule_id, 'ctcpfw__can_user_get_this_bundle_again', true ) ) ) {

				foreach ( $ctcpfw_get_post_id as $current_rule_id ) {

					$what_user_purchase = (array) get_post_meta( $current_rule_id, 'ct_who_purchase_this_bundle', true );

					if ( in_array( $rule_id, $what_user_purchase ) && in_array( $product_id, $what_user_purchase ) ) {

						$user_is_able = false;

					}
				}
			}

			return $user_is_able;
		}
	}

	new Cpfw_Front();
}
