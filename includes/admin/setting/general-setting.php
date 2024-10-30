<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_settings_section(
	'ctcpfw__general_settings_fields',
	'', // Title to be displayed on the administration page.
	'', // Callback used to render the description of the section.
	'ctcpfw__general_settings_sections'
);
add_settings_field(
	'ctcpfw__out_of_stock_product',
	esc_html__( 'Out of Stock', ' chain-product-for-woocommerce' ), // The label
	array( $this, 'ctcpfw__out_of_stock_product' ),
	'ctcpfw__general_settings_sections', // The page on which this option will be displayed.
	'ctcpfw__general_settings_fields'
);
register_setting(
	'ctcpfw__general_settings_fields',
	'ctcpfw__out_of_stock_product'
);


add_settings_field(
	'ctcpfw__bundle_postions',
	esc_html__( 'Bundle Positions', ' chain-product-for-woocommerce' ), // The label
	array( $this, 'ctcpfw__bundle_postions' ),
	'ctcpfw__general_settings_sections', // The page on which this option will be displayed.
	'ctcpfw__general_settings_fields'
);
register_setting(
	'ctcpfw__general_settings_fields',
	'ctcpfw__bundle_postions'
);


add_settings_field(
	'ctcpfw__bundle_product_template',
	esc_html__( 'Chain Product template', ' chain-product-for-woocommerce' ), // The label
	array( $this, 'ctcpfw__bundle_product_template' ),
	'ctcpfw__general_settings_sections', // The page on which this option will be displayed.
	'ctcpfw__general_settings_fields'
);
register_setting(
	'ctcpfw__general_settings_fields',
	'ctcpfw__bundle_product_template'
);

add_settings_field(
	'ctcpfw__show_product_price',
	esc_html__( 'Show Product Price ', ' chain-product-for-woocommerce' ), // The label
	array( $this, 'ctcpfw__show_product_price' ),
	'ctcpfw__general_settings_sections', // The page on which this option will be displayed.
	'ctcpfw__general_settings_fields'
);
register_setting(
	'ctcpfw__general_settings_fields',
	'ctcpfw__show_product_price'
);

add_settings_field(
	'ctcpfw__show_product_des',
	esc_html__( 'Show Product Description ', ' chain-product-for-woocommerce' ), // The label
	array( $this, 'ctcpfw__show_product_des' ),
	'ctcpfw__general_settings_sections', // The page on which this option will be displayed.
	'ctcpfw__general_settings_fields'
);
register_setting(
	'ctcpfw__general_settings_fields',
	'ctcpfw__show_product_des'
);

add_settings_field(
	'ctcpfw__show_product_des_max_charater',
	esc_html__( 'Product Description Character Limit', ' chain-product-for-woocommerce' ), // The label
	array( $this, 'ctcpfw__show_product_des_max_charater' ),
	'ctcpfw__general_settings_sections', // The page on which this option will be displayed.
	'ctcpfw__general_settings_fields'
);
register_setting(
	'ctcpfw__general_settings_fields',
	'ctcpfw__show_product_des_max_charater'
);



// Error Messages

add_settings_section(
	'ctcpfw__general_settings_error_message',
	'Error Messages', // Title to be displayed on the administration page.
	'', // Callback used to render the description of the section.
	'ctcpfw__general_settings_sections'
);

add_settings_field(
	'ctcpfw__do_not_cross_max_quantity',
	esc_html__( 'Maximum Quantity ', ' chain-product-for-woocommerce' ), // The label
	array( $this, 'ctcpfw__do_not_cross_max_quantity' ),
	'ctcpfw__general_settings_sections', // The page on which this option will be displayed.
	'ctcpfw__general_settings_error_message'
);
register_setting(
	'ctcpfw__general_settings_fields',
	'ctcpfw__do_not_cross_max_quantity'
);

add_settings_field(
	'ctcpfw__do_not_cross_min_quantity',
	esc_html__( 'Minimum Quantity ', ' chain-product-for-woocommerce' ), // The label
	array( $this, 'ctcpfw__do_not_cross_min_quantity' ),
	'ctcpfw__general_settings_sections', // The page on which this option will be displayed.
	'ctcpfw__general_settings_error_message'
);
register_setting(
	'ctcpfw__general_settings_fields',
	'ctcpfw__do_not_cross_min_quantity'
);
