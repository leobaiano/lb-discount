<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Options.
 *
 * @author   Leo Baiano <leobaiano@lbideias.com.br>
 */
class LB_Discount_Options {

	/**
	 * Plugin settings.
	 *
	 * @var array
	 */
	public $plugin_settings = array();
	/**
	 * Initialize the plugin
	 */
	public function __construct() {
		// Adds admin menu.
		add_action( 'admin_menu', array( $this, 'settings_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_section_options' ) );
	}

	/**
	 * Settings menu
	 *
	 * @return void
	 */
	public function settings_menu() {
		add_menu_page(
			__( 'LB Discount', 'lb-discount' ),
			__( 'LB Discount', 'lb-discount' ),
			'manage_options',
			'lb_discount_options',
			array( $this, 'settings_page' ),
			'',
			50.1
		);
	}

	/**
	 * Creating the options section and fields of plugin options
	 *
	 */
	public function settings_section_options() {
	 	add_settings_section(
			'lb_discount_settings_section',
			__( 'LB Discount', 'lb-discount' ),
			'__return_false',
			'lb_discount_options'
		);
	 	add_settings_field(
			'lb_discount_percentage',
			__( 'Discount Percentage', 'lb-discount' ),
			array( $this, 'text_element_callback' ),
			'lb_discount_options',
			'lb_discount_settings_section',
			array(
				'name' => 'lb_discount_percentage',
				'id' => 'lb_discount_percentage',
				'class' => 'input_text',
				'settings' => 'lb_discount_setings_main'
			)
		);

	 	register_setting( 'lb_discount_settings', 'lb_discount_setings_main' );
	}
	/**
	 * Callback to create fields of type text
	 *
	 * @param array $args Data required for the creation of the field
	 * @return string HTML field
	 */
	public function text_element_callback( $args ){
		$name = $args['name'];
		$id = $args['id'];
		$class = $args['class'];
		$settings = $args['settings'];
		$valueArr = get_option( $settings, array() );
		if( isset( $valueArr[$name] ) )
			$value = $valueArr[$name];
		else
			$value = '';
		echo sprintf( '<input type="text" id="%2$s" name="%4$s[%1$s]" value="%5$s" class="%3$s" />', $name, $id, $class, $settings, $value ) . ' % ';
	}

	/**
	 * Settings page
	 *
	 * @return string
	 */
	public function settings_page() {
		include 'view_options.php';
	}
}
new LB_Discount_Options;
