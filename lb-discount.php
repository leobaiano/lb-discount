<?php

/**
 * LB Discount plugin
 *
 * @link              https://github.com/leobaiano/lb-discount
 * @since             1.0.0
 * @package           LB Discount
 *
 * @wordpress-plugin
 * Plugin Name:       LB Discount
 * Plugin URI:        https://github.com/leobaiano/lb-discount
 * Description:       Apply a percentage discount on the first purchase
 * Version:           1.0.0
 * Author:            Leo Baiano
 * Author URI:        http://leobaiano.com.br
 * Text Domain:       lb-discount
 * Domain Path:       /languages
 *
 * Copyright: © 2016
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LB_Discount' ) ) :

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
         * A dummy constructor to prevent LB_Discount from being loaded more than once.
         * 
         * @see LB_Discount::get_instance()
         */
        private function __construct() { /* Do nothing here */ }

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
         */
        private function includes() {
        	require( $this->class . 'admin_options.php' );
        }
		
		private function setup_hooks() {
			// Checks if WooCommerce is installed.
            if ( ! class_exists( 'WooCommerce' ) ) {
                add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
                return;
            }

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

			if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
				return;
            }

			if ( !is_user_logged_in() ) {
				return;
            }

			$user_ID = get_current_user_id();

			$surcharge = ( $woocommerce->cart->cart_contents_total * $this->percentage ) / 100;

			$discount = -$surcharge;

            $message = __( 'Discount first purchase', 'lb-discount' );

			$woocommerce->cart->add_fee(  $message . ' ( ' . $this->percentage . '% )', $discount, true, '' );
		}

	} // end LB Discount

endif;

/**
 * The main function responsible for returning the one true LB_Discount Instance.
 *
 * @since 1.0.0
 *
 * @return LB_Discount The one true LB_Discount Instance.
 */
function LB_Discount() {
    return LB_Discount::get_instance();
}
add_action( 'plugins_loaded', 'LB_Discount');

// That's it! =)
