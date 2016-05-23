<?php
	/**
	 * Plugin Name: LB Discount
	 * Plugin URI:
	 * Description:
	 * Author: leobaiano
	 * Author URI: http://leobaiano.com.br
	 * Version: 1.0.0
	 * License: GPLv2 or later
	 * Text Domain: lb-discount
 	 * Domain Path: /languages/
	 */

	if ( ! defined( 'ABSPATH' ) )
		exit; // Exit if accessed directly.

	/**
	 * LB Discount
	 *
	 * @author   Leo Baiano <leobaiano@leobaiano.com.br>
	 */
	class LB_Discount {
		/**
		 * Instance of this class.
		 *
		 * @var object $instance
		 */
		protected static $instance = null;

		/**
		 * Discount percentage
		 *
		 * @var string $percentage
		 */
		protected $percentage;

		/**
		 * Slug.
		 *
		 * @var string $text_domain
		 */
		protected static $text_domain = 'lb-discount';

		/**
		 * Initialize the plugin
		 */
		private function __construct() {
			// Include class
			add_action( 'init', 'include_functions' );

			// Get options
			add_action( 'init', array( $this, 'get_options' ) );

			// Load plugin text domain
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

			// Client discount
			add_action( 'woocommerce_cart_calculate_fees', array( $this, 'apply_discount' ) );
		}

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @return void
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( self::$text_domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Set options
		 *
		 * @return void
		 */
		public function get_options() {
			$lb_discount_options = get_option( 'lb_discount_setings_main' );
			$this->percentage = $lb_discount_options[ 'lb_discount_percentage' ];
		}

		/**
		 * Apply discount for client
		 */
		public function apply_discount() {
			global $woocommerce;
			if ( is_admin() && ! defined( 'DOING_AJAX' ) )
				return;

			if ( !is_user_logged_in() )
				return;

			$user_ID = get_current_user_id();

			$surcharge = ( $woocommerce->cart->cart_contents_total * $this->percentage ) / 100;

			$discount = -$surcharge;

			$woocommerce->cart->add_fee( 'Discount first purchase ( ' . $this->percentage . '% )', $discount, true, '' );
		}

	} // end LB Discount
	add_action( 'plugins_loaded', array( 'LB_Discount', 'get_instance' ), 0 );

	/**
	 * Include functions
	 */
	function include_functions() {
		require 'class/admin_options.php';
	}
