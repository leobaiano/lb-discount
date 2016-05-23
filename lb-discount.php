<?php
	/**
	 * Plugin Name: LB Discount
	 * Plugin URI:
	 * Description: LB Discount
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
	 * LB Discount Class
	 */
	class LB_Discount {
		/**
		 * Discount percentage
		 *
		 * @var string $percentage
		 */
		protected $percentage;

		/**
         * Main instance
         * 
         * @return instance
         */
        public static function get_instance() {

            // Store the instance locally to avoid private static replication
            static $instance = null;

            // Only run these methods if they haven't been run previously
            if ( null === $instance ) {
                $instance = new LB_Discount;
                $instance->setup_globals();
                $instance->includes();
                $instance->setup_hooks();
            }

            // Always return the instance
            return $instance;
        }

		/**
		 * Initialize the plugin
		 */
		private function __construct() {}

		/**
         * Sets some globals for the plugin
         */
        private function setup_globals() {
        	$this->domain        = 'lb-discount';
            $this->name          = 'LB Discount';
            $this->file          = __FILE__;
            $this->basename      = plugin_basename( $this->file                     );
            $this->plugin_dir    = plugin_dir_path( $this->file                     );
            $this->plugin_url    = plugin_dir_url( $this->file                      );
            $this->class  	     = trailingslashit( $this->plugin_dir . 'class'  	);
            $this->lang_dir      = trailingslashit( $this->plugin_dir . 'languages' );
        }

		/**
         * Include needed files.
         *
         * @since 1.0.0
         */
        private function includes() {
        	require( $this->class . 'admin_options.php'  );
        }
		
		private function setup_hooks() {
			// Checks if WooCommerce is installed.
            if ( ! class_exists( 'WooCommerce' ) ) {
                add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
                return;
            }

			// Include class
			add_action( 'init', 		array( $this, 'include_functions' ) );

			// Get options
			add_action( 'init', 		array( $this, 'get_options' ) );

			// Load plugin text domain
			add_action( 'init',         array( $this, 'load_plugin_textdomain' ) );

			// Client discount
			add_action( 'woocommerce_cart_calculate_fees', array( $this, 'apply_discount' ) );
		}

		/**
         * WooCommerce missing notice.
         *
         * @return string
         */
        public function woocommerce_missing_notice() {
            include $this->class . 'views/html-notice-missing-woocommerce.php';
        }

		/**
         * Load the plugin text domain for translation.
         *
         * @since 1.0.0
         */
        public function load_plugin_textdomain() {
            // Traditional WordPress plugin locale filter
            $locale        = apply_filters( 'plugin_locale', get_locale(), $this->domain );
            $mofile        = sprintf( '%1$s-%2$s.mo', $this->domain, $locale );

            // Setup paths to current locale file
            $mofile_local  = $this->lang_dir . $mofile;
            $mofile_global = WP_LANG_DIR . '/lb-disount/' . $mofile;

            // Look in global /wp-content/languages/lb-disount folder
            load_textdomain( $this->domain, $mofile_global );

            // Look in local /wp-content/plugins/lb-disount/languages/ folder
            load_textdomain( $this->domain, $mofile_local );
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
