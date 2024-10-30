<?php

/**
 * Ajax Class.
 *
 * @package : chain-product-for-woocommerce
 */


/**
 * ASBPATH.
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define Class.
 *
 */
class Cpfw_Admin {

	/**
	 * Define constructor .
	 *
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'cpfw_admin_menu' ) );

		add_action( 'all_admin_notices', array( $this, 'cpfw_tabs' ), 5 );

		add_action( 'admin_enqueue_scripts', array( $this, 'cpfw_order_renew_enque_scripts' ) );

		add_action( 'admin_init', array( $this, 'cpfw_adding_settings' ) );
		add_action( 'woocommerce_after_order_itemmeta', array( $this, 'cpfw_line_item' ), 10, 3 );

		// live search.

		add_action( 'wp_ajax_ctcpfw__product_search', array( $this, 'cpfw___product_search' ) );
		add_action( 'wp_ajax_ctcpfw__category_search', array( $this, 'cpfw_category_search' ) );
	}

	/**
	 * Add submenu.
	 *
	 */
	public function cpfw_admin_menu() {

		add_submenu_page(
			'woocommerce', // parent slug.
			'Configuration', // Page title.
			esc_html__( 'Chain Product', 'chain-product-for-woocommerce' ), // Title.
			'manage_options', // Capability.
			'ctcpfw__submenu', // slug.
			array( $this, 'create_configuration_page' )
		);

		global $pagenow, $typenow;

		if ( ( 'edit.php' === $pagenow && 'ct_chain_product' === $typenow ) || ( 'post-new.php' === $pagenow && 'ct_chain_product' === $typenow )
			|| ( 'post.php' === $pagenow && isset( $_GET['post'] ) && 'ct_chain_product' === get_post_type( sanitize_text_field( $_GET['post'] ) ) ) ) {

			remove_submenu_page( 'woocommerce', 'ctcpfw__submenu' );
			remove_submenu_page( 'woocommerce', 'edit.php?post_type=ct_scd_user_mail' );

		} elseif ( ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'ctcpfw__submenu' === sanitize_text_field( $_GET['page'] ) ) ) {

			remove_submenu_page( 'woocommerce', 'edit.php?post_type=ct_scd_user_mail' );
			remove_submenu_page( 'woocommerce', 'edit.php?post_type=ct_chain_product' );

		} else {

			remove_submenu_page( 'woocommerce', 'edit.php?post_type=ct_chain_product' );

		}
	}

	/**
	 * Creating tabs.
	 *
	 */
	public function cpfw_tabs() {

		global $post, $typenow;

		$screen = get_current_screen();
		// handle tabs on the relevant WooCommerce pages
		if ( $screen && in_array( $screen->id, $this->get_tab_screen_ids(), true ) ) {

			$tabs = array(
				'rules'           => array(
					'title' => __( 'Rules', 'addify_cog' ),
					'url'   => admin_url( 'edit.php?post_type=ct_chain_product' ),
				),
				'general_setting' => array(
					'title' => __( 'Settings', 'addify_cog' ),
					'url'   => admin_url( 'admin.php?page=ctcpfw__submenu&tab=general_setting' ),
				),
			);

			$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general_setting';

			if ( empty( get_option( 'ctcpfw__over_purchasing_error_meassage' ) ) ) {

				update_option( 'ctcpfw__over_purchasing_error_meassage', 'Please Do not  Over Purchase.Please select on {max_prodcut} product.' );

			}

			if ( empty( get_option( 'ctcpfw__low_purchasing_error_meassage' ) ) ) {

				update_option( 'ctcpfw__low_purchasing_error_meassage', 'Please select minimum {min_prodcut} product to get product bundle.' );

			}

			if ( empty( get_option( 'ctcpfw__must_select_required_product_error_meassage' ) ) ) {

				update_option( 'ctcpfw__must_select_required_product_error_meassage', 'Please must select {product_name}  product to get product bundle.' );

			}

			if ( empty( get_option( 'ctcpfw__do_not_cross_max_quantity' ) ) ) {

				update_option( 'ctcpfw__do_not_cross_max_quantity', 'Please add {max_qty} quantity only of product {product_name}.' );

			}

			if ( empty( get_option( 'ctcpfw__do_not_cross_min_quantity' ) ) ) {

				update_option( 'ctcpfw__do_not_cross_min_quantity', 'Please add {min_qty} quantity only of product {product_name}.' );

			}

			if ( is_array( $tabs ) ) {
				?>
				<div class="wrap woocommerce">
					<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
						<?php
						$current_tab = 'general_setting' != $this->get_current_tab() ? $this->get_current_tab() : $active_tab;

						foreach ( $tabs as $id => $tab_data ) {

							$class = $id === $current_tab ? 'nav-tab nav-tab-active' : 'nav-tab';

							?>
							<a href="<?php echo esc_url( $tab_data['url'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_html( $tab_data['title'] ); ?></a>
							<?php
						}
						?>
					</h2>
				</div>
				<?php
			}
		}
	}

	/**
	 * Register Setting.
	 *
	 */
	public function create_configuration_page() {

		global $active_tab;
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general_setting';

		?>
		<br class="clear">

		<form method="post" action="options.php">
			<?php

			// Session Expire Settings Tab.

			if ( 'general_setting' === $active_tab ) {
				settings_fields( 'ctcpfw__general_settings_fields' );
				do_settings_sections( 'ctcpfw__general_settings_sections' );
				submit_button();

			}

			if ( 'styling' === $active_tab ) {

				settings_fields( 'ctcpfw__styling_fields' );
				do_settings_sections( 'ctcpfw__styling_sections' );
				submit_button();
			}

			?>
		</form>
		<?php
	}

	/**
	 * Define Screen.
	 *	@return string .
	 */
	public function get_current_tab() {

		$screen = get_current_screen();

		$active_tab = $screen->id;

		switch ( $active_tab ) {
			case 'woocommerce_page_ctcpfw__submenu':
				return 'general_setting';
			case 'ct_chain_product':
			case 'edit-ct_chain_product':
				return 'rules';
		}
	}

	public function get_tab_screen_ids() {
		$tabs_screens = array(
			'woocommerce_page_ctcpfw__submenu',
			'edit-ct_chain_product',
			'ct_chain_product',
		);

		return $tabs_screens;
	}

	public function cpfw_order_renew_enque_scripts() {

		wp_enqueue_media();

		wp_enqueue_script( 'admin_js', CTCP_PLUGIN_URL . '/assets/js/ct-cpfw-admin.js', array( 'jquery' ), '1.1.0', false );
		wp_enqueue_style( 'admin_css', CTCP_PLUGIN_URL . '/assets/css/ct-cpfw-admin.css', false, '1.1.0' );

		wp_enqueue_style( 'select2', plugins_url( 'assets/css/select2.css', WC_PLUGIN_FILE ), array(), '5.7.2' );
		wp_enqueue_script( 'select2', plugins_url( 'assets/js/select2/select2.min.js', WC_PLUGIN_FILE ), array( 'jquery' ), '4.0.3', true );

		$order_renew = array(
			'admin_url' => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'ctcpfw__nonce' ),

		);
		wp_localize_script( 'admin_js', 'php_var', $order_renew );
	}

	public function cpfw___product_search() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;
		if ( ! wp_verify_nonce( $nonce, 'ctcpfw__nonce' ) ) {
			die( 'Failed ajax security check!' );
		}
		if ( isset( $_POST['q'] ) ) {

			$pro = sanitize_text_field( wp_unslash( $_POST['q'] ) );
		} else {
			$pro = '';
		}
		$data_array = array();
		$args       = array(
			'post_type'   => array( 'product', 'product_variation' ),
			'post_status' => 'publish',
			'numberposts' => 100,
			's'           => $pro,
			'type'        => array( 'simple', 'variation' ),
			'orderby'     => 'relevance',
			'order'       => 'ASC',

		);
		$pros = wc_get_products( $args );

		if ( ! empty( $pros ) ) {
			foreach ( $pros as $proo ) {
				$title            = ( mb_strlen( $proo->get_name() ) > 50 ) ? mb_substr( $proo->get_name(), 0, 49 ) . '...' : $proo->get_name();
					$data_array[] = array( $proo->get_id(), $title ); // array( Post ID, Post Title ).
			}
		}
			echo wp_json_encode( $data_array );
			die();
	}

	public function cpfw_category_search() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;
		if ( ! wp_verify_nonce( $nonce, 'ctcpfw__nonce' ) ) {
			die( 'Failed ajax security check!' );
		}
		if ( isset( $_POST['q'] ) ) {

			$pro = sanitize_text_field( wp_unslash( $_POST['q'] ) );
		} else {
			$pro = '';
		}
		$data_array = array();
		$orderby    = 'name';
		$order      = 'asc';
		$hide_empty = false;
		$cat_args   = array(
			'taxonomy'   => 'product_cat',
			'orderby'    => $orderby,
			'order'      => $order,
			'hide_empty' => $hide_empty,
			'name__like' => $pro,
		);

		$product_categories = get_terms( $cat_args );

		if ( ! empty( $product_categories ) ) {
			foreach ( $product_categories as $proo ) {
				$pro_front_post = ( mb_strlen( $proo->name ) > 50 ) ? mb_substr( $proo->name, 0, 49 ) . '...' : $proo->name;
				$data_array[]   = array( $proo->term_id, $pro_front_post ); // array( Post ID, Post Title ).

				$rule_id = $selected_product['rule_id'];

				$main_product_qty = $item->get_quantity();

				unset( $selected_product['rule_id'] );

			}
		}
		echo wp_json_encode( $data_array );
		die();
	}



	public function cpfw_adding_settings() {

		include CTCP_PLUGIN_DIR . 'includes/admin/setting/general-setting.php';
	}
	public function cpfw_line_item( $item_id, $item, $product ) {
		if ( ! is_admin() ) {
			return;
		}

		$selected_product = $item->get_meta( 'ctcp_chain_product_bundle', true );

		if ( $selected_product && isset( $selected_product['rule_id'] ) ) {

			$rule_id = $selected_product['rule_id'];

			$main_product_qty = $item->get_quantity();

			unset( $selected_product['rule_id'] );

			?>
			<table class="ct-cpfw-selected-bundle-table" style="display:none">

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

					if ( 'linked' === $qty_type ) {

						$selected_qty *= $main_product_qty;

					}

					$price = $current_products_obj->get_price();

					$discount_type = isset( $product_detail_array['discount_type'] ) ? $product_detail_array['discount_type'] : 'same_price';

					$discount_amount = isset( $product_detail_array['discount_amount'] ) ? (float) $product_detail_array['discount_amount'] : 0;

					if ( 'free' === $discount_type ) {

						$price = 0;

					} elseif ( 'fixed_discount' === $discount_type ) {

						$price -= $discount_amount;

					} elseif ( 'percentage_discount' === $discount_type ) {

						$price -= ( $discount_amount / 100 ) * $price;
					}

					?>


					<tr class="item " data-order_item_id="310">
						<td class="thumb">
							<div class="wc-order-item-thumbnail">
								<img width="150" height="150" src="<?php echo esc_attr( get_the_post_thumbnail_url( $current_products_id ) ); ?>" class="attachment-thumbnail size-thumbnail" alt="" loading="lazy" title="">
							</div>	
						</td>
						<td class="name" data-sort-value="ACME">
							<a href="<?php echo esc_url( get_edit_post_link( $current_products_id ) ); ?>">
								<?php echo esc_attr( $current_products_obj->get_name() ); ?>
							</a>
						</td>


						<td class="item_cost" width="1%" data-sort-value="215.20">
							<?php echo wp_kses_post( wc_price( $price, get_woocommerce_currency_symbol() ) ); ?>
						</td>
						<td class="quantity" width="1%">
							<div class="view">
								<input type="number" step="1" min="0" autocomplete="off" name="order_item_qty[310]" placeholder="0" value="<?php echo esc_attr( $selected_qty ); ?>" readonly data-qty="3" size="4" class="quantity">
							</td>
							<td class="line_cost" width="1%" data-sort-value="645.6">
								<?php echo wp_kses_post( wc_price( $price * $selected_qty, get_woocommerce_currency_symbol() ) ); ?>
							</td>

							<td class="wc-order-edit-line-item" width="1%">

							</td>
						</tr>

					<?php } ?>

				</table>
				<?php

		}
	}

	public function ctcpfw__out_of_stock_product() {

		?>
			<select name="ctcpfw__out_of_stock_product">
				<option value="remove_current_product" <?php echo esc_attr( 'remove_current_product' === get_option( 'ctcpfw__out_of_stock_product' ) ? 'selected' : '' ); ?>>
				<?php echo esc_html__( 'Remove Current Product', 'chain-product-for-woocommerce' ); ?>
				</option>
				<option value="remove_all_product" <?php echo esc_attr( 'remove_all_product' === get_option( 'ctcpfw__out_of_stock_product' ) ? 'selected' : '' ); ?>>
				<?php echo esc_html__( 'Remove All Product', 'chain-product-for-woocommerce' ); ?>
				</option>
			</select>
			<?php
	}


	public function ctcpfw__bundle_product_template() {

		?>
			<select name="ctcpfw__bundle_product_template">
				<option value="table_template" <?php echo esc_attr( 'table_template' === get_option( 'ctcpfw__bundle_product_template' ) ? 'selected' : '' ); ?>>
				<?php echo esc_html__( 'Template First', 'chain-product-for-woocommerce' ); ?>
				</option>
				<option value="ul_li_template" <?php echo esc_attr( 'ul_li_template' === get_option( 'ctcpfw__bundle_product_template' ) ? 'selected' : '' ); ?>>
				<?php echo esc_html__( 'Template Second', 'chain-product-for-woocommerce' ); ?>
				</option>
			</select>
			<?php
	}

	public function ctcpfw__show_product_price() {
		?>
			<input type="checkbox" name="ctcpfw__show_product_price" class="ctcpfw__show_product_price" value="yes" <?php echo esc_attr( get_option( 'ctcpfw__show_product_price' ) ? 'checked' : '' ); ?>>
			<?php
	}

	public function ctcpfw__show_product_des() {
		?>
			<input type="checkbox" name="ctcpfw__show_product_des" class="ctcpfw__show_product_des" value="yes" <?php echo esc_attr( get_option( 'ctcpfw__show_product_des' ) ? 'checked' : '' ); ?>>
			<?php
	}

	public function ctcpfw__bundle_postions() {

		?>
			<select name="ctcpfw__bundle_postions">
				<option value="before_add_to_cart" <?php echo esc_attr( 'before_add_to_cart' === get_option( 'ctcpfw__bundle_postions' ) ? 'selected' : '' ); ?>>
				<?php echo esc_html__( 'Before Add to cat button ', 'chain-product-for-woocommerce' ); ?>
				</option>
				<option value="after_add_to_cart" <?php echo esc_attr( 'after_add_to_cart' === get_option( 'ctcpfw__bundle_postions' ) ? 'selected' : '' ); ?>>
				<?php echo esc_html__( 'After Add to cat button', 'chain-product-for-woocommerce' ); ?>
				</option>
			</select>
			<?php
	}

	public function ctcpfw__show_product_des_max_charater() {
		if ( ! get_option( 'ctcpfw__show_product_des_max_charater' ) ) {

			update_option( 'ctcpfw__show_product_des_max_charater', 50 );

		}
		?>
			<input type="number" name="ctcpfw__show_product_des_max_charater" value="<?php echo esc_attr( get_option( 'ctcpfw__show_product_des_max_charater' ) ); ?>">
			<?php
	}

	public function ctcpfw__over_purchasing_error_meassage() {
		?>
			<textarea name="ctcpfw__over_purchasing_error_meassage" cols="60" rows="3" ><?php echo esc_attr( get_option( 'ctcpfw__over_purchasing_error_meassage' ) ); ?></textarea>
			<br>
			<i>
			<?php echo esc_html__( 'Set error message which will show try to over Purchase product . Use  variable {max_prodcut} to let the customer about max product you can buy in bundle.', 'chain-product-for-woocommerce' ); ?>
			</i>
			<?php
	}

	public function ctcpfw__low_purchasing_error_meassage() {
		?>
			<textarea name="ctcpfw__low_purchasing_error_meassage" cols="60" rows="3" ><?php echo esc_attr( get_option( 'ctcpfw__low_purchasing_error_meassage' ) ); ?></textarea>
			<br>
			<i>
			<?php echo esc_html__( 'Set error message which will show try to minimum Purchase product . Use variable {min_prodcut} to let the customer that must purchase a specific product.', 'chain-product-for-woocommerce' ); ?>
			</i>
			<?php
	}

	public function ctcpfw__must_select_required_product_error_meassage() {
		?>
			<textarea name="ctcpfw__must_select_required_product_error_meassage" cols="60" rows="3" ><?php echo esc_attr( get_option( 'ctcpfw__must_select_required_product_error_meassage' ) ); ?></textarea>
			<br>
			<i>
			<?php echo esc_html__( 'Set error message for required product .Use variable {product_name} to show product name in message.', 'chain-product-for-woocommerce' ); ?>
			</i>
			<?php
	}

	public function ctcpfw__do_not_cross_max_quantity() {
		?>
			<textarea name="ctcpfw__do_not_cross_max_quantity" cols="60" rows="3" ><?php echo esc_attr( get_option( 'ctcpfw__do_not_cross_max_quantity' ) ); ?></textarea>
			<br>
			<i>
			<?php echo esc_html__( 'Set error message to tell maximum quantity. Use variable {max_qty} to show allowed  maximum quantity.Use variable {product_name} to show product name in message.', 'chain-product-for-woocommerce' ); ?>
			</i>
			<?php
	}
	public function ctcpfw__do_not_cross_min_quantity() {
		?>
			<textarea name="ctcpfw__do_not_cross_min_quantity" cols="60" rows="3" ><?php echo esc_attr( get_option( 'ctcpfw__do_not_cross_min_quantity' ) ); ?></textarea>
			<br>
			<i>
			<?php echo esc_html__( 'Set error message to tell minimum quantity. use variable {min_qty} to show allowed  minimum quantity.Use variable {product_name} to show product name in message.', 'chain-product-for-woocommerce' ); ?>
			</i>
			<?php
	}
}

	new Cpfw_Admin();
