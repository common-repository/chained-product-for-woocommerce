/**
 * Admin js is used for admin side jquery functions.
 *
 * @package : chain-product-for-woocommerce
 */

jQuery( 'document' ).ready(
	function ($) {
		'use strict';

		append_new_tr();

		$( document.body ).on(
			'wc_fragments_refreshed',
			function () {

				append_new_tr();

			}
		);
		$( document ).ajaxComplete(
			function (event, xhr, settings) {

				if ( settings.data && settings.data.toLowerCase().includes( 'woocommerce_load_variations' ) ) {

				}

			}
		);

		$( document ).on(
			'click',
			'.ct-cpfw-upload-icon',
			function (e) {
				e.preventDefault();

				var upload_files_fo_card_wrapper = wp.media(
					{
						title: 'Upload Icon',
						multiple: false,
					}
				);

				upload_files_fo_card_wrapper.on(
					'select',
					function () {

						var attachments = upload_files_fo_card_wrapper.state().get( 'selection' ).map(
							function ( attachment ) {

								attachment.toJSON();
								return attachment;

							}
						);

								// loop through the array and do things with each attachment.

						if (attachments[0] && attachments[0].attributes && attachments[0].attributes.url ) {

							var url = attachments[0].attributes.url;

							jQuery( '.ct-cpfw-upload-icon' ).closest( 'tr' ).find( 'input[name=ctcpfw__add_to_crt_btn_uploaded_icon]' ).val( url );
							check_image();

						}

					}
				);

				upload_files_fo_card_wrapper.open();
			}
		);
		check_image();
		jQuery( document ).on(
			'click',
			'.ct-cpfw-remove-icon',
			function () {
				jQuery( '.ct-cpfw-upload-icon' ).closest( 'tr' ).find( 'input[name=ctcpfw__add_to_crt_btn_uploaded_icon]' ).val( null );
				check_image();

			}
		);
		ctcpfw__add_to_cart_button_icon()
		$( document ).on(
			'click',
			'input[name="ctcpfw__add_to_cart_button_icon"]' ,
			function () {
				ctcpfw__add_to_cart_button_icon();
			}
		)

		discount_type();

		jQuery( document ).on(
			'change',
			'.selected_prd_discounted_type',
			function () {
				discount_type();

			}
		);

		ctcpfw__add_to_cart_style();

		jQuery( document ).on(
			'click',
			'input[name=ctcpfw__enable_add_to_cart_button_styling]',
			function () {
				ctcpfw__add_to_cart_style();

			}
		);

		jQuery( document ).on(
			'click',
			'.ct_add_chain_prd',
			function () {
				var selected_options = [];

				$( this ).closest( 'td' ).find( 'select.ctcpfw__selected_chain_prd' ).children( 'option:selected' ).each(
					function () {
						if ( $( this ).val() ) {
							selected_options[ $( this ).val() ] = $( this ).val();
						}
					}
				);

				selected_options = selected_options.filter( Boolean );

				jQuery.ajax(
					{
						url: php_var.admin_url,
						type: 'POST',
						data: {
							action 		: 'ct_add_chain_prd',
							nonce 		: php_var.nonce,
							rule_id 	: $( this ).data( 'rule_id' ),
							product_ids : selected_options,
						},
						success: function (response) {

							if ( response.data && response.data["new_html"] ) {

								jQuery( '.ct-cp-woo-product-bundle-table table tbody' ).append( response.data["new_html"] );

							}
							discount_type();

						}

					}
				);

			}
		);

		jQuery( document ).on(
			'click',
			'.ct-remove-woo-product-bundle',
			function () {

				var current_btn = $( this );

				jQuery.ajax(
					{
						url: php_var.admin_url,
						type: 'POST',
						data: {
							action 		: 'ct_delete_woo_bundle_product',
							nonce 		: php_var.nonce,
							rule_id 	: $( this ).data( 'main_product_id' ),
							product_id : $( this ).data( 'product_id' ),
						},
						success: function (response) {

							if ( response.data && response.data["delete"] ) {

								current_btn.closest( 'tr' ).remove();

							}

						}

					}
				);

			}
		);

		// ------------------------------- Delete Image ---------------------------------------------------.

		$( '#select_user_from_switch' ).select2(
			{
				multiple 	: 	true,
				placeholder : 'Select User Roles',
			}
		);
		$( '.ctcpfw__email_to_order_status' ).select2(
			{
				multiple 	: 	true,
				placeholder : 'Select Order Statuses',
			}
		);
		$( '.ct_live_Search' ).select2(
			{
				multiple 	: 	true,
			}
		);
		$( '.ctcpfw__countries' ).select2(
			{
				multiple 	: 	true,
				placeholder : 'Select Countries',
			}
		);

		var ajaxurl = php_var.admin_url;
		var nonce   = php_var.nonce;

		// product search.
		jQuery( '.ctcpfw__product_live_search' ).select2(
			{
				ajax: {
					url: ajaxurl,
					dataType: 'json',
					type: 'POST',
					delay: 20,
					data: function (params) {
						return {
							q: params.term, // search query.
							action: 'ctcpfw__product_search',
							nonce: nonce,
						};
					},
					processResults: function ( data ) {
						var options = [];
						if (data ) {
							// data is the array of arrays, and each of them contains ID and the Label of the option.
							$.each(
								data,
								function ( index, text ) {
									// do not forget that "index" is just auto incremented value.
									options.push( { id: text[0], text: text[1]  } );
								}
							);
						}
						return {
							results: options
						};
					},
					cache: true
				},
				multiple: true,
				placeholder: 'Choose Products',
						// minimumInputLength: 3 // the minimum of symbols to input before perform a search.
			}
		);
		jQuery( '.ctcpfw__category_live_search' ).select2(
			{
				ajax: {
					url: ajaxurl,
					dataType: 'json',
					type: 'POST',
					delay: 20,
					data: function (params) {
						return {
							q: params.term, // search query.
							action: 'ctcpfw__category_search',
							nonce: nonce,
						};
					},
					processResults: function ( data ) {
						var options = [];
						if (data ) {
							// data is the array of arrays, and each of them contains ID and the Label of the option.
							jQuery.each(
								data,
								function ( index, text ) {
									// do not forget that "index" is just auto incremented value.
									options.push( { id: text[0], text: text[1]  } );
								}
							);
						}
						return {
							results: options
						};
					},
					cache: true
				},
				multiple: true,
				placeholder: 'Choose category',
						// minimumInputLength: 3 // the minimum of symbols to input before perform a search.
			}
		);

	}
);




function discount_type(){

	jQuery( '.selected_prd_discounted_type' ).each(
		function () {

			jQuery( this ).closest( 'tr' ).find( '.selected_prd_detail_discount_amount' ).prop( 'readonly',true );
			jQuery( this ).closest( 'tr' ).find( '.selected_prd_detail_discount_amount' ).prop( 'min' , '0' );

			if ( jQuery( this ).children( 'option:selected' ).val() === 'percentage_discount' || jQuery( this ).children( 'option:selected' ).val() === 'fixed_discount' ) {

				jQuery( this ).closest( 'tr' ).find( '.selected_prd_detail_discount_amount' ).prop( 'readonly' , false );
				jQuery( this ).closest( 'tr' ).find( '.selected_prd_detail_discount_amount' ).prop( 'min' , '1' );

			}

		}
	);

}






function append_new_tr() {

	jQuery( '.ct-cpfw-custom-row-added' ).remove();

	jQuery( '.ct-cpfw-selected-bundle-table' ).each(
		function () {

			jQuery( this ).find( 'tbody tr' ).each(
				function () {
					jQuery( this ).addClass( 'ct-cpfw-custom-row-added' );
				}
			);

			var new_html = jQuery( this ).find( 'tbody' ).html();

			jQuery( this ).closest( 'tr' ).after( new_html );

			jQuery( this ).find( 'tbody tr' ).each(
				function () {
					jQuery( this ).removeClass( 'ct-cpfw-custom-row-added' );
				}
			);

			jQuery( '.ct-cpfw-custom-row-added' ).each(
				function () {

					jQuery( this ).show();

				}
			);

		}
	);

}


function ctcpfw__add_to_cart_style() {

	jQuery( '.ctcpfw__add_to_cart_style' ).each(
		function () {

			jQuery( this ).closest( 'tr' ).hide();

		}
	);

	if ( jQuery( 'input[name=ctcpfw__enable_add_to_cart_button_styling]' ).is( ':checked' ) ) {
		jQuery( '.ctcpfw__add_to_cart_style' ).each(
			function () {

				jQuery( this ).closest( 'tr' ).show();

			}
		);
	}

}
function ctcpfw__add_to_cart_button_icon() {

	jQuery( 'input[name=ctcpfw__add_to_crt_btn_icon_class]' ).closest( 'tr' ).hide();
	jQuery( 'input[name=ctcpfw__add_to_crt_btn_uploaded_icon]' ).closest( 'tr' ).hide();

	if ( 'custom_icon_class' == jQuery( 'input[name="ctcpfw__add_to_cart_button_icon"]:checked' ).val()) {
		jQuery( 'input[name=ctcpfw__add_to_crt_btn_icon_class]' ).closest( 'tr' ).show();

	} else if ( 'upload_custom_icon' == jQuery( 'input[name="ctcpfw__add_to_cart_button_icon"]:checked' ).val()) {
		jQuery( 'input[name=ctcpfw__add_to_crt_btn_uploaded_icon]' ).closest( 'tr' ).show();

	}

}


function check_image() {

	jQuery( '.ct-cpfw-upload-icon' ).closest( 'tr' ).find( 'img' ).prop( 'src','' );
	jQuery( '.ct-cpfw-upload-icon' ).closest( 'tr' ).find( 'img' ).fadeOut( 'slow' );

	if ( jQuery( '.ct-cpfw-upload-icon' ).closest( 'tr' ).find( 'input[name=ctcpfw__add_to_crt_btn_uploaded_icon]' ).val() ) {

		var url = jQuery( '.ct-cpfw-upload-icon' ).closest( 'tr' ).find( 'input[name=ctcpfw__add_to_crt_btn_uploaded_icon]' ).val();

		jQuery( '.ct-cpfw-upload-icon' ).closest( 'tr' ).find( 'img' ).prop( 'src',url );
		jQuery( '.ct-cpfw-upload-icon' ).closest( 'tr' ).find( 'img' ).fadeIn( 'slow' );

	}

}