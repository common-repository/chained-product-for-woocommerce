<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
class Ctcp_Product_Level_Setting {

	public function __construct() {

		add_filter( 'woocommerce_product_data_tabs', array( $this, 'make_a_tab_on_prouct_data_tab' ), 98 );

		add_filter( 'woocommerce_product_data_panels', array( $this, 'orer_renew_prod_tab_data' ), 98 );

		add_action( 'woocommerce_process_product_meta_simple', array( $this, 'order_variable_prod_value_save' ) );
	}

	public function make_a_tab_on_prouct_data_tab( $tabs ) {

		if ( 'simple' == wc_get_product( get_the_ID() )->get_type() ) {

			$tabs['ctcp_chain_product_bundle'] = array(
				'label'    => __( 'Chain Product', 'chain-product-for-woocommerce' ), // Navigation Label Name
				'target'   => 'ctcp_chain_product_bundle', // The HTML ID of the tab content wrapper
				'class'    => array(),
				'priority' => 1,
			);

		}

		return $tabs;
	}

	public function orer_renew_prod_tab_data() {

		global $wp_roles;

		wp_nonce_field( 'ctcpfw__nonce', 'ctcpfw__nonce' );
		?>
		<div id="ctcp_chain_product_bundle" class='panel woocommerce_options_panel'>
			<?php

			$switch_from_roles          = $wp_roles->get_names();
			$switch_from_roles['guest'] = 'Guest';

			$countries_obj = new WC_Countries();
			$countries     = $countries_obj->__get( 'countries' );

			$kselect_user_roles  = (array) get_post_meta( get_the_ID(), 'ctcpfw__select_user_from_switch', true );
			$selected_countries  = (array) get_post_meta( get_the_ID(), 'ctcpfw__selected_countries', true );
			$included_product    = (array) get_post_meta( get_the_ID(), 'ctcpfw__product_included_list', true );
			$excluded_products   = (array) get_post_meta( get_the_ID(), 'ctcpfw__product_exclusion_list', true );
			$selected_categories = (array) get_post_meta( get_the_ID(), 'ctcpfw__included_category', true );

			?>
			<table>
				<tr>
					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Select Bundle Title', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">
						<input type="text" name="ctcpfw__bundle_title" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__bundle_title', true ) ); ?>">

						<p>
							<i>
								<?php echo wp_kses_post( wc_help_tip( 'Select Bundle Title.', 'chain-product-for-woocommerce' ) ); ?>
							</i>
						</p>
					</td>
				</tr>
				<tr>
					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Custom Price Name', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">
						<input type="text" name="ctcpfw__custom_price_name" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__custom_price_name', true ) ); ?>">

						<p>
							<i>
								<?php echo wp_kses_post( wc_help_tip( 'Set custom price name. Which will replace the original price text.', 'chain-product-for-woocommerce' ) ); ?>
							</i>
						</p>
					</td>
				</tr>
				
				<tr>
					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Select Users Roles', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">
						<select id="select_user_from_switch" style="width: 350px;" name="ctcpfw__select_user_from_switch[]" class="select_user_from_switch"  multiple>
							<?php
							foreach ( $switch_from_roles as $key => $from_switch_role ) {
								?>
								<option value="<?php echo esc_attr( $key ); ?>"
									<?php echo in_array( $key, $kselect_user_roles ) ? esc_attr( 'selected' ) : ''; ?> />
									<?php echo esc_attr( $from_switch_role ); ?>
								</option>
							<?php } ?>
						</select>

						<?php echo wp_kses_post( wc_help_tip( 'Select user roles to apply rule setting. Leave empty to enable for all users.', 'chain-product-for-woocommerce' ) ); ?>

					</td>
				</tr>
				<tr>
					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Can user get it multiple times', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">

						<input type="checkbox" name="ctcpfw__can_user_get_this_bundle_again" value="yes" <?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__can_user_get_this_bundle_again', true ) ? 'checked' : '' ); ?>>
						<p>
							<i>
								<?php echo wp_kses_post( wc_help_tip( 'Enable checkbox if you want to give this bundle to your user multiple time.', 'chain-product-for-woocommerce' ) ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>
					<th class="ctcpfw__table_heading">
						<?php echo esc_html__( 'Show Bundle On Cart Page', 'chain-product-for-woocommerce' ); ?>
					</th>

					<td class="ctcpfw__table_content">

						<input type="checkbox" name="ctcpfw__show_bundle_on_cart_pg" value="yes" <?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__show_bundle_on_cart_pg', true ) ? 'checked' : '' ); ?>>
						<p>
							<i>
								<?php echo wp_kses_post( wc_help_tip( 'Enable checkbox to show bundle on cart page.', 'chain-product-for-woocommerce' ) ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>
					<th class="ctcpfw__table_heading">
						<?php echo esc_html__( 'Show Bundle On Checkout Page', 'chain-product-for-woocommerce' ); ?>
					</th>

					<td class="ctcpfw__table_content">

						<input type="checkbox" name="ctcpfw__show_bundle_on_checkout_pg" value="yes" <?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__show_bundle_on_checkout_pg', true ) ? 'checked' : '' ); ?>>
						<p>
							<i>
								<?php echo wp_kses_post( wc_help_tip( 'Enable checkbox to show bundle on checkout page.', 'chain-product-for-woocommerce' ) ); ?>
							</i>
						</p>

					</td>
				</tr>

				<tr>
					<th class="ctcpfw__table_heading">
						<?php echo esc_html__( 'Show Bundle On ThankYou Page', 'chain-product-for-woocommerce' ); ?>
					</th>

					<td class="ctcpfw__table_content">

						<input type="checkbox" name="ctcpfw__show_bundle_on_thankyou_pg" value="yes" <?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__show_bundle_on_thankyou_pg', true ) ? 'checked' : '' ); ?>>
						<p>
							<i>
								<?php echo wp_kses_post( wc_help_tip( 'Enable checkbox to show bundle on thank-you page.', 'chain-product-for-woocommerce' ) ); ?>
							</i>
						</p>

					</td>
				</tr>

				<tr>
					<th class="ctcpfw__table_heading">
						<?php echo esc_html__( 'Show Bundle On My-Account Page', 'chain-product-for-woocommerce' ); ?>
					</th>

					<td class="ctcpfw__table_content">

						<input type="checkbox" name="ctcpfw__show_bundle_on_my_account_pg" value="yes" <?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__show_bundle_on_my_account_pg', true ) ? 'checked' : '' ); ?>>
						<p>
							<i>
								<?php echo wp_kses_post( wc_help_tip( 'Enable checkbox to show bundle on my-account page.', 'chain-product-for-woocommerce' ) ); ?>
							</i>
						</p>

					</td>
				</tr>

				<tr>
					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'From', 'chain-product-for-woocommerce' ); ?></th>
					<td class="ctcpfw__table_content">
						<input type="date" name="ctcpfw__discount_start_date" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__discount_start_date', true ) ); ?>">
					</td>

				</tr>


				<tr>
					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'To', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">
						<input type="date" name="ctcpfw__discount_end_date" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__discount_end_date', true ) ); ?>">
					</td>

				</tr>

				<tr>

					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Add Chain Products', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">

						<select  class="ctcpfw__product_live_search ctcpfw__selected_chain_prd" name="ctcpfw__selected_chain_prd[]" multiple style="width: 50%;">
						</select>

						<i class="fa fa-plus ct_add_chain_prd button button-primary button-large" data-rule_id="<?php echo esc_attr( get_the_ID() ); ?>"><?php echo esc_html__( 'Add', 'chain-product-for-woocommerce' ); ?></i>

					</td>
				</tr>

			</table>

			<div class="ct-cp-woo-product-bundle-table-showing-data ct-cp-woo-product-bundle-table">
				<table>
					<thead>
						<tr class="ct-cp-table-row-heading">	
							<th class="ct-cp-product-name"><?php echo esc_html__( 'Product', 'chain-product-for-woocommerce' ); ?></th>
							<th class="ct-cp-product-name"><?php echo esc_html__( 'Qty Type', 'chain-product-for-woocommerce' ); ?></th>
							<th class="ct-cp-product-qty-type"><?php echo esc_html__( 'Min Qty', 'chain-product-for-woocommerce' ); ?></th>
							<th class="ct-cp-product-qty"><?php echo esc_html__( 'MAX Qty', 'chain-product-for-woocommerce' ); ?></th>
							<th class="ct-cp-product-price-type"><?php echo esc_html__( 'Price type', 'chain-product-for-woocommerce' ); ?></th>
							<th><?php echo esc_html__( 'Discount', 'chain-product-for-woocommerce' ); ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
						echo wp_kses_post( chain_product_bundle_table( get_the_ID() ) );

						?>

					</tbody>
				</table>
			</div>

		</div>
		<?php
	}

	public function order_variable_prod_value_save( $post_id ) {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {

			return;
		}

		if ( 'simple' != wc_get_product( $post_id )->get_type() ) {

			return;
		}

		// For custom post type.

		$exclude_statuses = array( 'auto-draft', 'trash' );

		$ka_notification_plugin_action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

		if ( ! in_array( get_post_status( $post_id ), $exclude_statuses ) && ! is_ajax() && 'untrash' != $ka_notification_plugin_action ) {

			$nonce = isset( $_POST['ctcpfw__nonce'] ) ? sanitize_text_field( $_POST['ctcpfw__nonce'] ) : '';

			if ( ! wp_verify_nonce( $nonce, 'ctcpfw__nonce' ) ) {
				wp_die( esc_html__( 'Security Violate!', 'chain-product-for-woocommerce' ) );
			}

			$bundle_title = isset( $_POST['ctcpfw__bundle_title'] ) ? sanitize_text_field( $_POST['ctcpfw__bundle_title'] ) : 'Special Bundle';

			update_post_meta( $post_id, 'ctcpfw__bundle_title', $bundle_title );

			$custom_price_name = isset( $_POST['ctcpfw__custom_price_name'] ) ? sanitize_text_field( $_POST['ctcpfw__custom_price_name'] ) : '';

			update_post_meta( $post_id, 'ctcpfw__custom_price_name', $custom_price_name );

			$add_to_cart_button_text = isset( $_POST['ctcpfw__add_to_cart_button_text'] ) ? sanitize_text_field( $_POST['ctcpfw__add_to_cart_button_text'] ) : '';

			update_post_meta( $post_id, 'ctcpfw__add_to_cart_button_text', $add_to_cart_button_text );

			$ctcpfw__add_to_cart_button_icon = isset( $_POST['ctcpfw__add_to_cart_button_icon'] ) ? sanitize_text_field( $_POST['ctcpfw__add_to_cart_button_icon'] ) : '';

			update_post_meta( $post_id, 'ctcpfw__add_to_cart_button_icon', $ctcpfw__add_to_cart_button_icon );

			$ctcpfw__can_user_get_this_bundle_again = isset( $_POST['ctcpfw__can_user_get_this_bundle_again'] ) ? sanitize_text_field( $_POST['ctcpfw__can_user_get_this_bundle_again'] ) : '';

			update_post_meta( $post_id, 'ctcpfw__can_user_get_this_bundle_again', $ctcpfw__can_user_get_this_bundle_again );

			$ctcpfw__add_to_crt_btn_icon_class = isset( $_POST['ctcpfw__add_to_crt_btn_icon_class'] ) ? sanitize_text_field( $_POST['ctcpfw__add_to_crt_btn_icon_class'] ) : '';

			update_post_meta( $post_id, 'ctcpfw__add_to_crt_btn_icon_class', $ctcpfw__add_to_crt_btn_icon_class );

			$ctcpfw__add_to_crt_btn_uploaded_icon = isset( $_POST['ctcpfw__add_to_crt_btn_uploaded_icon'] ) ? sanitize_text_field( $_POST['ctcpfw__add_to_crt_btn_uploaded_icon'] ) : '';

			update_post_meta( $post_id, 'ctcpfw__add_to_crt_btn_uploaded_icon', $ctcpfw__add_to_crt_btn_uploaded_icon );

			$enable_add_to_cart_button_styling = isset( $_POST['ctcpfw__enable_add_to_cart_button_styling'] ) ? sanitize_text_field( $_POST['ctcpfw__enable_add_to_cart_button_styling'] ) : '';

			update_post_meta( $post_id, 'ctcpfw__enable_add_to_cart_button_styling', $enable_add_to_cart_button_styling );

			$ctcpfw__add_to_cart_styling = isset( $_POST['ctcpfw__add_to_cart_styling'] ) ? sanitize_meta( '', $_POST['ctcpfw__add_to_cart_styling'], '' ) : array();

			update_post_meta( $post_id, 'ctcpfw__add_to_cart_styling', $ctcpfw__add_to_cart_styling );

			$show_on_cart_pg = isset( $_POST['ctcpfw__show_bundle_on_cart_pg'] ) ? sanitize_text_field( $_POST['ctcpfw__show_bundle_on_cart_pg'] ) : '';

			update_post_meta( $post_id, 'ctcpfw__show_bundle_on_cart_pg', $show_on_cart_pg );

			$show_on_checkout = isset( $_POST['ctcpfw__show_bundle_on_checkout_pg'] ) ? sanitize_text_field( $_POST['ctcpfw__show_bundle_on_checkout_pg'] ) : '';

			update_post_meta( $post_id, 'ctcpfw__show_bundle_on_checkout_pg', $show_on_checkout );

			$show_on_thbakyou = isset( $_POST['ctcpfw__show_bundle_on_thankyou_pg'] ) ? sanitize_text_field( $_POST['ctcpfw__show_bundle_on_thankyou_pg'] ) : '';

			update_post_meta( $post_id, 'ctcpfw__show_bundle_on_thankyou_pg', $show_on_thbakyou );

			$show_on_myaccount = isset( $_POST['ctcpfw__show_bundle_on_my_account_pg'] ) ? sanitize_text_field( $_POST['ctcpfw__show_bundle_on_my_account_pg'] ) : '';

			update_post_meta( $post_id, 'ctcpfw__show_bundle_on_my_account_pg', $show_on_myaccount );

			$max = isset( $_POST['ctcpfw__show_bundle_on_cart_page'] ) ? sanitize_text_field( $_POST['ctcpfw__show_bundle_on_cart_page'] ) : 1;

			update_post_meta( $post_id, 'ctcpfw__show_bundle_on_cart_page', $max );
			$max = isset( $_POST['ctcpfw__max_purchase'] ) ? sanitize_text_field( $_POST['ctcpfw__max_purchase'] ) : 1;

			update_post_meta( $post_id, 'ctcpfw__max_purchase', $max );

			$min = isset( $_POST['ctcpfw__min_purchase'] ) ? sanitize_text_field( $_POST['ctcpfw__min_purchase'] ) : 1;

			update_post_meta( $post_id, 'ctcpfw__min_purchase', $min );

			$start_date = isset( $_POST['ctcpfw__discount_start_date'] ) ? sanitize_text_field( $_POST['ctcpfw__discount_start_date'] ) : '';
			update_post_meta( $post_id, 'ctcpfw__discount_start_date', $start_date );

			$end_date = isset( $_POST['ctcpfw__discount_end_date'] ) ? sanitize_text_field( $_POST['ctcpfw__discount_end_date'] ) : '';
			update_post_meta( $post_id, 'ctcpfw__discount_end_date', $end_date );

			$user_role = isset( $_POST['ctcpfw__select_user_from_switch'] ) ? sanitize_meta( '', $_POST['ctcpfw__select_user_from_switch'], '' ) : array();
			update_post_meta( $post_id, 'ctcpfw__select_user_from_switch', $user_role );

			$selected_countries = isset( $_POST['ctcpfw__selected_countries'] ) ? sanitize_meta( '', $_POST['ctcpfw__selected_countries'], '' ) : array();
			update_post_meta( $post_id, 'ctcpfw__selected_countries', $selected_countries );

			$all_product = isset( $_POST['ctcpfw__for_all_product'] ) ? sanitize_meta( '', $_POST['ctcpfw__for_all_product'], '' ) : '';
			update_post_meta( $post_id, 'ctcpfw__for_all_product', $all_product );

			$excluded_product = isset( $_POST['ctcpfw__product_exclusion_list'] ) ? sanitize_meta( '', $_POST['ctcpfw__product_exclusion_list'], '' ) : array();
			update_post_meta( $post_id, 'ctcpfw__product_exclusion_list', $excluded_product );

			$included_product = isset( $_POST['ctcpfw__product_included_list'] ) ? sanitize_meta( '', $_POST['ctcpfw__product_included_list'], '' ) : array();
			update_post_meta( $post_id, 'ctcpfw__product_included_list', $included_product );

			$selected_categories = isset( $_POST['ctcpfw__included_category'] ) ? sanitize_meta( '', $_POST['ctcpfw__included_category'], '' ) : array();
			update_post_meta( $post_id, 'ctcpfw__included_category', $selected_categories );

			$old_selected_prd = (array) get_post_meta( $post_id, 'ctcpfw__selected_chain_prd', true );

			foreach ( $old_selected_prd as $product_id => $product_detail ) {

				if ( isset( $_POST['selected_chain_prd_detail'][ $product_id ] ) ) {
					$selected_prd_dtail = sanitize_meta( '', $_POST['selected_chain_prd_detail'][ $product_id ], '' );

					$qty_type = isset( $selected_prd_dtail['qty_type'] ) ? $selected_prd_dtail['qty_type'] : 'linked';

					$min_qty = isset( $selected_prd_dtail['min_qty'] ) ? $selected_prd_dtail['min_qty'] : 1;

					$max_qty = isset( $selected_prd_dtail['max_qty'] ) ? $selected_prd_dtail['max_qty'] : '';

					$selected_discount_type = isset( $selected_prd_dtail['discount_type'] ) ? $selected_prd_dtail['discount_type'] : 'same_price';

					$discount_amount = isset( $selected_prd_dtail['discount_amount'] ) ? $selected_prd_dtail['discount_amount'] : 0;

					$required = isset( $selected_prd_dtail['required'] ) ? $selected_prd_dtail['required'] : '';

					$old_selected_prd[ $product_id ] = array(
						'qty_type'        => $qty_type,
						'min_qty'         => $min_qty,
						'max_qty'         => $max_qty,
						'discount_type'   => $selected_discount_type,
						'discount_amount' => $discount_amount,
					);

				}
			}

			update_post_meta( $post_id, 'ctcpfw__selected_chain_prd', $old_selected_prd );
		}
	}
}
new Ctcp_Product_Level_Setting();
