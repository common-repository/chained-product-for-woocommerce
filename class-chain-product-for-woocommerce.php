<?php
/**
 * Plugin Name:  Chain Product For Woocommerce
 * Plugin URI:   https://cloudtechnologies.store/product/chained-product-for-woocommerce/
 * Description:  Create captivating product bundles with customizable options, discounts, and promotions to boost sales and engage customers.
 * Version:      1.0.0
 * Author:       Cloud Technologies
 * Author URI:   https://cloudtechnologies.store/product/
 * Developed By: Cloud Technologies
 * Support:      https://cloudtechnologies.store/product/
 * License:      GPL-2.0+
 * License URI:
 * Text Domain : chain-product-for-woocommerce
 *
 * @package :    Chain Product For Woocommerce
/**
description of license
 */

/**
 * Multi site check
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'Chain_Product_For_Woocommerce' ) ) {


	/**
	 * Main Class
	 */
	class Chain_Product_For_Woocommerce {

		/**
		 * Class constructor starts
		 */
		public function __construct() {
			// Define Global Constants.
			$this->cart_based_discount_global_constents_vars();
			// load Text Domain.

			add_action( 'plugins_loaded', array( $this, 'af_gf_init' ) );
			add_action( 'init', array( $this, 'af_gf_admin_init' ) );
		}
		public function af_gf_init() {

			// Check the installation of WooCommerce module if it is not a multi site.
			if ( ! is_multisite() && ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

				add_action( 'admin_notices', array( $this, 'af_ev_check_wocommerce' ) );
			}
		}
		public function af_ev_check_wocommerce() {
			// Deactivate the plugin.
			deactivate_plugins( __FILE__ );
			?>
		<div id="message" class="error">
			<p>
				<strong>
					<?php esc_html_e( 'Chain Product For Woocommerce plugin is inactive. WooCommerce plugin must be active in order to activate it.', 'chain-product-for-woocommerce' ); ?>
				</strong>
			</p>
		</div>
			<?php
		}
		public function af_gf_admin_init() {

			if ( defined( 'WC_PLUGIN_FILE' ) ) {

				add_action( 'wp_loaded', array( $this, 'php_var__init' ) );

				// register post.
				$this->ctcp_register_post_callback();
				add_action( 'woocommerce_order_item_meta_start', array( $this, 'ctcp_woocommerce_order_item_meta_start' ), 10, 3 );

				include CTCP_PLUGIN_DIR . 'includes/ajax-controller/class-cpfw-ajax-callback.php';
				include CTCP_PLUGIN_DIR . 'includes/cpfw-general-functions.php';

				if ( is_admin() ) {

					// include Admin Class.

					include CTCP_PLUGIN_DIR . 'includes/admin/class-cpfw-admin.php';
					include CTCP_PLUGIN_DIR . 'includes/admin/rules/class-ctcp-chain-product-meta-boxes.php';
					include CTCP_PLUGIN_DIR . 'includes/admin/rules/class-ctcp-product-level-setting.php';

				} else {

					include CTCP_PLUGIN_DIR . 'includes/front/class-cpfw-front.php';

				}
			}
		}

		public function ctcp_woocommerce_order_item_meta_start( $item_id, $item, $order ) {

			$selected_product = $item->get_meta( 'ctcp_chain_product_bundle', true );

			if ( $selected_product && isset( $selected_product['rule_id'] ) ) {

				$rule_id = $selected_product['rule_id'];

				$main_product_qty = $item->get_quantity();

				unset( $selected_product['rule_id'] );

				if ( is_wc_endpoint_url( 'order-received' ) && ! empty( get_post_meta( $rule_id, 'ctcpfw__show_bundle_on_thankyou_pg', true ) ) ) {
					include CTCP_PLUGIN_DIR . 'templates/cp-on-thankyou-and-my-account-page.php';
				}

				if ( is_account_page() && ! empty( get_post_meta( $rule_id, 'ctcpfw__show_bundle_on_my_account_pg', true ) ) ) {
					include CTCP_PLUGIN_DIR . 'templates/cp-on-thankyou-and-my-account-page.php';
				}
			}
		}



		public function cart_based_discount_global_constents_vars() {
			if ( ! defined( 'CTCP_PLUGIN_URL' ) ) {
				define( 'CTCP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}
			if ( ! defined( 'CTCP_PLUGIN_BASENAME' ) ) {
				define( 'CTCP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			}
			if ( ! defined( 'CTCP_PLUGIN_DIR' ) ) {
				define( 'CTCP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}
		}

		public function php_var__init() {

			if ( function_exists( 'load_plugin_textdomain' ) ) {
				load_plugin_textdomain( 'chain-product-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			}
		}

		public function ctcp_register_post_callback() {

			$supports = array( 'title', 'page-attributes' );

			$labels = array(
				'name'           => __( 'Chain Product Rule', 'chain-product-for-woocommerce' ),
				'singular_name'  => __( 'Chain Product Rule', 'chain-product-for-woocommerce' ),
				'menu_name'      => __( 'Chain Product Rule', 'chain-product-for-woocommerce' ),
				'name_admin_bar' => __( 'Chain Product', 'admin bar' ),
				'edit_item'      => __( 'Edit Rule', 'chain-product-for-woocommerce' ),
				'view_item'      => __( 'View Rule', 'chain-product-for-woocommerce' ),
				'all_items'      => __( 'Chain Product', 'chain-product-for-woocommerce' ),
				'search_items'   => __( 'Search Chain Product', 'chain-product-for-woocommerce' ),
				'not_found'      => __( 'No Chain Product found', 'chain-product-for-woocommerce' ),
				'attributes'     => __( 'Priority', 'chain-product-for-woocommerce' ),
			);
			$args   = array(
				'supports'          => $supports,
				'labels'            => $labels,
				'description'       => 'Chain Product',
				'public'            => true,
				'show_in_menu'      => 'woocommerce',
				'show_in_nav_menus' => false,
				'show_in_admin_bar' => false,

				'can_export'        => true,
				'capability_type'   => 'post',
				'show_in_rest'      => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'ct_chain_product' ),
				'has_archive'       => true,
				'hierarchical'      => false,
			);
			register_post_type( 'ct_chain_product', $args ); // Register Post type.

			$supports = array( 'title', 'page-attributes' );

			$labels = array(
				'name'           => __( 'Who Purchase Chain Product Rule', 'chain-product-for-woocommerce' ),
				'singular_name'  => __( 'Who Purchase Chain Product Rule', 'chain-product-for-woocommerce' ),
				'menu_name'      => __( 'Who Purchase Chain Product Rule', 'chain-product-for-woocommerce' ),
				'name_admin_bar' => __( 'Who Purchase Chain Product', 'admin bar' ),
				'edit_item'      => __( 'Edit Rule', 'chain-product-for-woocommerce' ),
				'view_item'      => __( 'View Rule', 'chain-product-for-woocommerce' ),
				'all_items'      => __( 'Who Purchase Chain Product', 'chain-product-for-woocommerce' ),
				'search_items'   => __( 'Search Who Purchase Chain Product', 'chain-product-for-woocommerce' ),
				'not_found'      => __( 'No Who Purchase Chain Product found', 'chain-product-for-woocommerce' ),
				'attributes'     => __( 'Priority', 'chain-product-for-woocommerce' ),
			);
			$args   = array(
				'supports'          => $supports,
				'labels'            => $labels,
				'description'       => 'Who Purchase Chain Product',
				'public'            => true,
				'show_in_menu'      => false,
				'show_in_nav_menus' => false,
				'show_in_admin_bar' => false,

				'can_export'        => true,
				'capability_type'   => 'post',
				'show_in_rest'      => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'ct_who_buy_chain_prd' ),
				'has_archive'       => true,
				'hierarchical'      => false,
			);
			register_post_type( 'ct_who_buy_chain_prd', $args ); // Register Post type.
		}
	}

	if ( class_exists( 'Chain_Product_For_Woocommerce' ) ) {
		new Chain_Product_For_Woocommerce();
	}
}
