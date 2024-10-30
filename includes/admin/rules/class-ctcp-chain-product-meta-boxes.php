<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Ctcp_Chain_Product_Bundle_Rules {

	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'create_metabox' ) );
	}

	public function create_metabox() {

		add_meta_box(
			'ctcpfw__restrictions',
			esc_html__( 'Restrictions', 'chain-product-for-woocommerce' ),
			array( $this, 'restriction' ),
			'ct_chain_product'
		);

		add_meta_box(
			'ctcpfw__add_to_cart_styling',
			esc_html__( 'Add To Cart Styling', 'chain-product-for-woocommerce' ),
			array( $this, 'add_to_cart_styling' ),
			'ct_chain_product'
		);

		if ( 'product' === (string) get_post_type( get_the_ID() ) && 'simple' === (string) wc_get_product( get_the_ID() )->get_type() ) {

			add_meta_box(
				'ctcpfw__add_to_cart_styling',
				esc_html__( 'Add To Cart Styling', 'chain-product-for-woocommerce' ),
				array( $this, 'add_to_cart_styling' ),
				'product'
			);
		}

		add_meta_box(
			'ctcpfw__general_options',
			esc_html__( 'General Options', 'chain-product-for-woocommerce' ),
			array( $this, 'general_options' ),
			'ct_chain_product'
		);
	}

	public function restriction() {
		?>
		<div id="message" class="error">
			<p>
				<strong> 
					<?php esc_html_e( 'Rule Functionality is only available in premium version. Explore product level settings for more free options.', 'chain-product-for-woocommerce' ); ?>
				</strong>
			</p>
		</div>
		<?php

		global $wp_roles;
		wp_nonce_field( 'ctcpfw__nonce', 'ctcpfw__nonce' );

		$switch_from_roles          = $wp_roles->get_names();
		$switch_from_roles['guest'] = 'Guest';

		$countries_obj              = new WC_Countries();
		$countries                  = $countries_obj->__get( 'countries' );
		$af_a_and_va_s_product_tags = get_terms( array( 'taxonomy' => 'product_tag' ) );

		$kselect_user_roles  = (array) get_post_meta( get_the_ID(), 'ctcpfw__select_user_from_switch', true );
		$selected_countries  = (array) get_post_meta( get_the_ID(), 'ctcpfw__selected_countries', true );
		$included_product    = (array) get_post_meta( get_the_ID(), 'ctcpfw__product_included_list', true );
		$excluded_products   = (array) get_post_meta( get_the_ID(), 'ctcpfw__product_exclusion_list', true );
		$selected_categories = (array) get_post_meta( get_the_ID(), 'ctcpfw__included_category', true );
		$ct_selected_tags    = (array) get_post_meta( get_the_ID(), 'ctcpfw__product_tags', true );

		?>
		<table>
			<tbody>
				
				<tr>
					<th class="ctcpfw__table_heading">
						<?php echo esc_html__( 'Select Users Roles', 'chain-product-for-woocommerce' ); ?>
					</th>

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
						<p>
							<i>
								<?php echo esc_html__( 'Select user roles to apply rule setting. Leave empty to enable for all users.', 'chain-product-for-woocommerce' ); ?>
							</i>
						</p>
					</td>
				</tr>
				
				<tr>
					<th class="ctcpfw__table_heading">
						<?php echo esc_html__( 'Included Products', 'chain-product-for-woocommerce' ); ?>
					</th>

					<td class="ctcpfw__table_content">
						<select  class="ctcpfw__product_live_search" name="ctcpfw__product_included_list[]" multiple style="width: 50%;">
							<?php
							foreach ( $included_product as $product_id ) {

								if ( ! empty( $product_id ) ) {

									$product = wc_get_product( $product_id );

									?>
									<option value="<?php echo esc_attr( $product_id ); ?>" selected>
										<?php echo esc_attr( $product->get_name() ); ?>
									</option>
									<?php

								}
							}
							?>

						</select>
					</td>
				</tr>

				<tr>
					<th class="ctcpfw__table_heading">
						<?php echo esc_html__( 'Excluded Products', 'chain-product-for-woocommerce' ); ?>
					</th>

					<td class="ctcpfw__table_content">
						<select  class="ctcpfw__product_live_search" name="ctcpfw__product_exclusion_list[]" multiple style="width: 50%;">
							<?php
							foreach ( $excluded_products as $product_id ) {

								if ( ! empty( $product_id ) ) {
									$product = wc_get_product( $product_id );
									?>
									<option value="<?php echo esc_attr( $product_id ); ?>" selected>
										<?php echo esc_attr( $product->get_name() ); ?>
									</option>
									<?php
								}
							}
							?>

						</select>
					</td>
				</tr>

				<tr>
					<th class="ctcpfw__table_heading">
						<?php echo esc_html__( 'Select Categories', 'chain-product-for-woocommerce' ); ?>
					</th>

					<td class="ctcpfw__table_content">
						<select class="ctcpfw__category_live_search" name="ctcpfw__included_category[]" multiple style="width: 50%;">

							<?php
							foreach ( $selected_categories as $cat_id ) {
								if ( ! empty( $cat_id ) ) {
									$category = get_term( $cat_id, 'product_cat' );
									?>
									<option value="<?php echo esc_attr( $cat_id ); ?>" selected>
										<?php echo esc_attr( $category->name ); ?>
									</option>
									<?php
								}
							}
							?>
						</select>
					</td>
				</tr>
				<tr>

					<th class="ctcpfw__table_heading">
						<?php echo esc_html__( 'Product Tag', 'chain-product-for-woocommerce' ); ?>
					</th>

					<td class="ctcpfw__table_content">
						<select width="100% !important" name="ctcpfw__product_tags[]" id="af_a_and_va_s_product_tags" data-placeholder="Choose Tags..." class="ct_live_Search" multiple="multiple" tabindex="-1" >;
							<?php foreach ( $af_a_and_va_s_product_tags as $product_tag ) { ?>

								<option value="<?php echo esc_html( $product_tag->term_id ); ?>"
									<?php
									if ( in_array( (string) $product_tag->term_id, (array) $ct_selected_tags, true ) ) {
										echo 'selected'; }
									?>
										><?php echo esc_html( $product_tag->name ); ?>

									</option>
									<?php
							}
							?>
							</select>

							<p><?php echo esc_html__( 'Select Tags on which you want to show notification', 'chain-product-for-woocommerce' ); ?></p>
							<p><?php echo esc_html__( 'Leave empty to apply rule on all products.', 'chain-product-for-woocommerce' ); ?></p>
						</td>
					</tr>
					<tr>

						<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Can user get it multiple times', 'chain-product-for-woocommerce' ); ?></th>

						<td class="ctcpfw__table_content">

							<input type="checkbox" name="ctcpfw__can_user_get_this_bundle_again" value="yes" <?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__can_user_get_this_bundle_again', true ) ? 'checked' : '' ); ?>>
							<p>
								<i>
									<?php echo esc_html__( 'Enable checkbox if you want to give this bundle to your user multiple.', 'chain-product-for-woocommerce' ); ?>
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
									<?php echo esc_html__( 'Enable checkbox to show bundle on cart page.', 'chain-product-for-woocommerce' ); ?>
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
									<?php echo esc_html__( 'Enable checkbox to show bundle on checkout page.', 'chain-product-for-woocommerce' ); ?>
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
									<?php echo esc_html__( 'Enable checkbox to show bundle on thank-you page.', 'chain-product-for-woocommerce' ); ?>
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
									<?php echo esc_html__( 'Enable checkbox to show bundle on my-account page.', 'chain-product-for-woocommerce' ); ?>
								</i>
							</p>

						</td>
					</tr>
				</tbody>
			</table>
			<?php
	}

	public function add_to_cart_styling() {
		wp_nonce_field( 'ctcpfw__nonce', 'ctcpfw__nonce' );

		$add_cart_styling_setting = (array) get_post_meta( get_the_ID(), 'ctcpfw__add_to_cart_styling', true );

		?>
			<table>
				<tr>
					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Add To Cart Button Text', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">
						<input type="text" name="ctcpfw__add_to_cart_button_text" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__add_to_cart_button_text', true ) ); ?>" min="1">

						<p>
							<i>
							<?php echo esc_html__( 'Set add to cart button text. Leave empty to show default text.', 'chain-product-for-woocommerce' ); ?>
							</i>
						</p>
					</td>
				</tr>
				<tr>
					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Icon', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">
						
					<?php
					$icon_classes = array( 'fa fa-cart-arrow-down', 'fa fa-cart-plus', 'fa fa-shopping-cart', 'fa fa-shopping-basket', 'custom_icon_class', 'upload_custom_icon' );

					foreach ( $icon_classes as $value ) {
						?>

							<input type="radio" name="ctcpfw__add_to_cart_button_icon" value="<?php echo esc_attr( $value ); ?>" <?php if ( get_post_meta( get_the_ID(), 'ctcpfw__add_to_cart_button_icon', true ) === $value ) : ?>
							checked
							<?php endif ?>>
							
							<?php if ( 'custom_icon_class' != $value && 'upload_custom_icon' != $value ) { ?>

								<i class="<?php echo esc_attr( $value ); ?>" style="font-size: 24px;" ></i>
								<?php

							} else {
								echo esc_attr( ucfirst( str_replace( '_', ' ', $value ) ) );
							}
							?>
							<br>
							<?php
					}
					?>

					</td>
				</tr>
				<tr style="display:none;">
					<th class="ctcpfw__table_heading"><?php echo esc_html__( ' Set Icon Class', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">

						<input type="text"  name="ctcpfw__add_to_crt_btn_icon_class" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__add_to_crt_btn_icon_class', true ) ); ?>">

						<p>
							<i>
							<?php echo esc_html__( 'Set Class like fa fa fa-cart-plus you can find icon from font awesome by', 'chain-product-for-woocommerce' ); ?>
								<a href="https://fontawesome.com/" target="_blank" ><?php echo esc_html__( 'Click Here', 'chain-product-for-woocommerce' ); ?></a>
							</i>
						</p>
					</td>
				</tr>
				<tr style="display:none;">
					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Upload Custom Icon', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">

						<img src="" style="width:100px;height: 100px;"><br>

						<input type="text"  name="ctcpfw__add_to_crt_btn_uploaded_icon" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__add_to_crt_btn_uploaded_icon', true ) ); ?>">

						<i class="fa fa-upload ct-cpfw-upload-icon"></i> <i class="ct-cpfw-remove-icon fa fa-trash"></i>
					</td>
				</tr>
				<tr>
					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Enable Add To Cart Button Styling', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">
						<input type="checkbox" name="ctcpfw__enable_add_to_cart_button_styling" value="yes" <?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__enable_add_to_cart_button_styling', true ) ? 'checked' : '' ); ?>>

						<p>
							<i>
							<?php echo esc_html__( 'Enable checkbox to Set Add To Cart button Styling.', 'chain-product-for-woocommerce' ); ?>
							</i>
						</p>
					</td>
				</tr>
				<tr>
					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Button Text Color', 'chain-product-for-woocommerce' ); ?></th>
					<td class="ctcpfw__table_content">
						<input type="color" class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_text_color]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_text_color'] ) ? $add_cart_styling_setting['button_text_color'] : '' ); ?>" min="1">

						<p>
							<i>
							<?php echo esc_html__( 'Set button text color.', 'chain-product-for-woocommerce' ); ?>
							</i>
						</p>

					</td>
				</tr>

				<tr>

					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Button Background Color', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">

						<input type="color" class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_text_bgcolor]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_text_bgcolor'] ) ? $add_cart_styling_setting['button_text_bgcolor'] : '' ); ?>" min="1">

						<p>
							<i><?php echo esc_html__( 'Set button background color.', 'chain-product-for-woocommerce' ); ?></i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Button Hover Color', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">

						<input type="color" class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_text_hover_color]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_text_hover_color'] ) ? $add_cart_styling_setting['button_text_hover_color'] : '' ); ?>" min="1">

						<p>
							<i>
							<?php echo esc_html__( 'Set hover color.', 'chain-product-for-woocommerce' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Button Hover Background-Color', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">

						<input type="color" class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_text_hover_bgcolor]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_text_hover_bgcolor'] ) ? $add_cart_styling_setting['button_text_hover_bgcolor'] : '' ); ?>" min="1">

						<p>
							<i>
							<?php echo esc_html__( 'Set hover color.', 'chain-product-for-woocommerce' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Button Border Color', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">

						<input type="color" class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_text_border_color]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_text_border_color'] ) ? $add_cart_styling_setting['button_text_border_color'] : '' ); ?>" min="1">

						<p>
							<i>
							<?php echo esc_html__( 'Set button border color.', 'chain-product-for-woocommerce' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Font Size', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">

						<input type="number" class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_font_size]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_font_size'] ) ? $add_cart_styling_setting['button_font_size'] : '14' ); ?>">

						<?php echo esc_html__( 'px', 'chain-product-for-woocommerce' ); ?>

						<p>
							<i>
							<?php echo esc_html__( 'Set font size.', 'chain-product-for-woocommerce' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Font Weight', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">
						<?php

						$weight = array( '100', '200', '300', '400', '500', '600', '700', '800', '900', 'bold', 'bolder', 'lighter', 'normal', 'initial', 'inherit', 'unset', 'revert' );

						$button_font_weight = isset( $add_cart_styling_setting['button_font_weight'] ) ? $add_cart_styling_setting['button_font_weight'] : '100';
						?>
						<select class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_font_weight]">

						<?php foreach ( $weight as $value ) : ?>

								<option value="<?php echo esc_attr( $value ); ?>" <?php if ( $value === $button_font_weight ) { ?>
									selected
									<?php } ?> >
									<?php echo esc_attr( $value ); ?>
								</option>

							<?php endforeach ?>

						</select>

						<p>
							<i>
							<?php echo esc_html__( 'Set font weight.', 'chain-product-for-woocommerce' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Border', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">

						<input type="number" class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_border]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border'] ) ? $add_cart_styling_setting['button_border'] : '' ); ?>" min="0">

						<?php echo esc_html__( 'px.', 'chain-product-for-woocommerce' ); ?>

						<p>
							<i>
							<?php echo esc_html__( 'Set border radius.', 'chain-product-for-woocommerce' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Border Radius', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">

						<input type="number" class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_border_radius]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_radius'] ) ? $add_cart_styling_setting['button_border_radius'] : '' ); ?>" min="0">

						<p>
							<i>
							<?php echo esc_html__( 'Set border radius.', 'chain-product-for-woocommerce' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Padding', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">

						<input type="text" class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_border_padding_left]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_padding_left'] ) ? $add_cart_styling_setting['button_border_padding_left'] : '' ); ?>">

						<input type="text" class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_border_padding_top]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_padding_top'] ) ? $add_cart_styling_setting['button_border_padding_top'] : '' ); ?>">

						<input type="text" class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_border_padding_bottom]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_padding_bottom'] ) ? $add_cart_styling_setting['button_border_padding_bottom'] : '' ); ?>">

						<input type="text" class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_border_padding_right]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_padding_right'] ) ? $add_cart_styling_setting['button_border_padding_right'] : '' ); ?>">

						

						<p>
							<i>
							<?php echo esc_html__( 'Set Padding.', 'chain-product-for-woocommerce' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ctcpfw__table_heading"><?php echo esc_html__( 'Margin', 'chain-product-for-woocommerce' ); ?></th>

					<td class="ctcpfw__table_content">

						<input type="text" class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_border_margin_left]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_margin_left'] ) ? $add_cart_styling_setting['button_border_margin_left'] : '' ); ?>">

						<input type="text" class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_border_margin_top]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_margin_top'] ) ? $add_cart_styling_setting['button_border_margin_top'] : '' ); ?>">

						<input type="text" class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_border_margin_bottom]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_margin_bottom'] ) ? $add_cart_styling_setting['button_border_margin_bottom'] : '' ); ?>">

						<input type="text" class="ctcpfw__add_to_cart_style" name="ctcpfw__add_to_cart_styling[button_border_margin_right]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_margin_right'] ) ? $add_cart_styling_setting['button_border_margin_right'] : '' ); ?>">

						

						<p>
							<i>
							<?php echo esc_html__( 'Set margin.', 'chain-product-for-woocommerce' ); ?>
							</i>
						</p>

					</td>
				</tr>
			</table>
			<?php
	}

	public function general_options() {

		wp_nonce_field( 'ctcpfw__nonce', 'ctcpfw__nonce' );

		global $post;

		?>
			<table>

				<tr>

					<th class="ctcpfw__table_heading">
					<?php echo esc_html__( 'Select Bundle Title', 'chain-product-for-woocommerce' ); ?>
					</th>

					<td class="ctcpfw__table_content">

						<input type="text" name="ctcpfw__bundle_title" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__bundle_title', true ) ); ?>" min="1">

						<p>
							<i>
							<?php echo esc_html__( 'Select Bundle Title.', 'chain-product-for-woocommerce' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>
					<th class="ctcpfw__table_heading">
					<?php echo esc_html__( 'Custom Price Name', 'chain-product-for-woocommerce' ); ?>
					</th>

					<td class="ctcpfw__table_content">
						<input type="text" name="ctcpfw__custom_price_name" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__custom_price_name', true ) ); ?>" min="1">

						<p>
							<i>
							<?php echo esc_html__( 'Set custom price name. Which will replace the original price text.', 'chain-product-for-woocommerce' ); ?>
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
					<th class="ctcpfw__table_heading">
					<?php echo esc_html__( 'To', 'chain-product-for-woocommerce' ); ?>

					</th>

					<td class="ctcpfw__table_content">
						<input type="date" name="ctcpfw__discount_end_date" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'ctcpfw__discount_end_date', true ) ); ?>">
					</td>

				</tr>




				<tr>
					<th class="ctcpfw__table_heading">
					<?php echo esc_html__( 'Add Chain Products', 'chain-product-for-woocommerce' ); ?>
					</th>

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
							<th><?php echo esc_html__( 'Discount Amount', 'chain-product-for-woocommerce' ); ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php echo wp_kses_post( chain_product_bundle_table( $post->ID ) ); ?>
					</tbody>
				</table>
			</div>
			<?php
	}
}
	new Ctcp_Chain_Product_Bundle_Rules();
