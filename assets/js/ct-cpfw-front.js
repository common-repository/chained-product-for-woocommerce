/**
 * Front Js is used for jqueyr functions.
 *
 * @package : chain-product-for-woocommerce
 */

jQuery( document ).ready(
	function ($) {

		'Use restricted';

		append_new_tr();

		$( document.body ).on(
			'wc_fragments_refreshed',
			function () {

				append_new_tr();

			}
		);
		$( document ).ajaxComplete(
			function () {

				append_new_tr();

			}
		);

		var button_text = jQuery( '.single_add_to_cart_button' ).text();

		jQuery( '.single_add_to_cart_button' ).html( button_text );

		jQuery( '.ct-cpfw-add-to-cart-custom-button' ).each(
			function () {

				var rule_id = $( this ).data( 'rule_id' );

				$( this ).closest( 'a.add_to_cart_button' ).addClass( 'ct-cpfw-add-to-cart-custom-button-' + rule_id );
				$( this ).closest( 'a.add_to_cart_button' ).css( 'display','inline-flex' );

				$( this ).css( 'display','inline-flex' );
				$( this ).closest( '.single_add_to_cart_button' ).addClass( 'ct-cpfw-add-to-cart-custom-button-' + rule_id );

				var text = $( this ).closest( 'a.add_to_cart_button' ).text();

				$( this ).closest( 'a.add_to_cart_button' ).html( text );

			}
		);

		jQuery( document ).on(
			'change',
			'.variation_id',
			function () {

				jQuery( '.ct-cpfw-final-product-bundle-main-div' ).html( null );

				if ( ! $( this ).val() ) {
					return;
				}

				jQuery.ajax(
					{
						url: php_var.admin_url,
						type: 'POST',
						data: {
							action 			: 'ct_get_chain_product_bundles_on_variation',
							nonce 			: php_var.nonce,
							variation_id 	: $( this ).val(),
							product_id 		: jQuery( 'input[name=product_id]' ).val(),
						},
						success: function (response) {

							if ( response["new_html"] ) {

								jQuery( '.ct-cpfw-final-product-bundle-main-div' ).append( response["new_html"] );

							}

						}

					}
				);

			}
		);

		total_price();

		jQuery( document ).on(
			'click',
			'.ctcpfw__selected_product',
			function () {

				total_price();

			}
		);

		jQuery( document ).on(
			'keypress',
			'.ctcpfw__selected_qty',
			function () {

				total_price();

			}
		);

		jQuery( document ).on(
			'change',
			'.ctcpfw__selected_qty',
			function () {

				total_price();

			}
		);

		jQuery( document ).on(
			'keyup',
			'.qty',
			function () {

				total_price();

			}
		);

		jQuery( document ).on(
			'change',
			'.qty',
			function () {

				total_price();

			}
		);

	}
);

function total_price() {

	var symbol = jQuery( '.ct-cpfw-selected-options_total' ).find( '.woocommerce-Price-currencySymbol' ).text();

	var total = parseFloat( 0.00 );

	var total_seected_prd = 0;

	jQuery( '.ctcpfw__selected_qty' ).each(
		function () {

			if ( jQuery( this ).val() ) {

					var price = jQuery( this ).data( 'ctcpfw__product_price' ) ? parseFloat( jQuery( this ).data( 'ctcpfw__product_price' ) ) : 0;

					var qty = jQuery( this ).val() ? parseFloat( jQuery( this ).val() ) : 0;

					var new_total = parseFloat( ( price * qty ) ).toFixed( 2 );

					total = parseFloat( total + parseFloat( new_total ) );
					total_seected_prd++;

			}

		}
	);

	jQuery( '.ctcpfw__selected_qty' ).each(
		function () {

			if ( jQuery( this ).val() ) {

					var price = jQuery( this ).closest( 'li' ).find( '.ctcpfw__selected_qty' ).data( 'ctcpfw__product_price' ) ? parseFloat( jQuery( this ).closest( 'li' ).find( '.ctcpfw__selected_qty' ).data( 'ctcpfw__product_price' ) ) : 0;

					var qty = jQuery( this ).closest( 'li' ).find( '.ctcpfw__selected_qty' ).val() ? parseFloat( jQuery( this ).closest( 'li' ).find( '.ctcpfw__selected_qty' ).val() ) : 0;

					var new_total = parseFloat( ( price * qty ) ).toFixed( 2 );

					total = parseFloat( total + parseFloat( new_total ) );

			}

		}
	);

	var prd_qty_ = jQuery( '.ctcpfw__main_prd_price' ).closest( 'form.cart' ).find( 'input.qty' ).val() ? parseFloat( jQuery( '.ctcpfw__main_prd_price' ).closest( 'form.cart' ).find( 'input.qty' ).val() ) : parseFloat( 0 );

	var prd_total = jQuery( '.ctcpfw__main_prd_price' ).val() ? parseFloat( jQuery( '.ctcpfw__main_prd_price' ).val() ) : parseFloat( 0.00 );

	prd_total = parseFloat( prd_total * prd_qty_ );

	prd_total = parseFloat( prd_total + total ).toFixed( 2 );

	jQuery( '.ct-cpfw-selected-options_total' ).html( '<span class="woocommerce-Price-currencySymbol">' + symbol + '</span>' + total );

	jQuery( '.ct-cpfw-selected-total' ).html( '<span class="woocommerce-Price-currencySymbol">' + symbol + '</span>' + prd_total );

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