<?php
namespace Ac_Reco_Plugin;

/**
 * Class Plugin
 *
 * @package Ac_Reco_Plugin
 */
final class Admin_General_Options {

	/**
	 * Plugin instance.
	 *
	 * @var null|self
	 */
	protected static $instance = null;

	/**
	 * Plugin constructor.
	 */
	private function __construct() {

		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 60 );
		add_action( 'woocommerce_settings_tabs_settings_tab_ac_reco', __CLASS__ . '::settings_tab' );
		add_action( 'woocommerce_update_options_settings_tab_ac_reco', __CLASS__ . '::update_settings' );
		add_action( 'template_redirect', __CLASS__ . '::ac_reco_thankyou_page' );
		add_action(
			'widgets_init',
			function() {
				register_widget( 'Ac_Reco_Widget' );
			}
		);
		add_action( 'init', [ $this, 'init' ] );

	}

	/**
	 * Get class instance
	 *
	 * @return Admin_General_Options
	 */
	public static function get_instance() : Admin_General_Options {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add translations. You can call other hooks here.
	 */
	public function init() {

	}

		/**
		 * Sends to Reco when the order is complete.
		 */
	public static function ac_reco_thankyou_page( $order_id ) {
		global $wp;
		if ( is_wc_endpoint_url( 'order-received' ) && ! empty( $wp->query_vars['order-received'] ) ) {
			$order     = wc_get_order( $wp->query_vars['order-received'] );
			$address   = $order->get_address( 'billing' );
			$reco_data = [
				'ac_reco_plugin_is_active'     => get_option( 'woocommerce_reco_plugin_is_active' ),
				'ac_reco_plugin_reco_id'       => get_option( 'woocommerce_reco_plugin_reco_id' ),
				'ac_reco_plugin_reco_key'      => get_option( 'woocommerce_reco_plugin_api_key' ),
				'ac_reco_plugin_send_order'    => get_option( 'woocommerce_reco_plugin_send_order' ),
				'ac_reco_plugin_send_interval' => empty( get_option( 'woocommerce_reco_plugin_send_interval' ) ) ? 1 : (int) get_option( 'woocommerce_reco_plugin_send_interval' ),
			];
			if ( empty( $reco_data['ac_reco_plugin_reco_id'] ) || empty( $reco_data['ac_reco_plugin_reco_key'] ) ) {
				return;
			}
			/*
			here I am going to send data to Reco's API
			*/

			$curl                                       = curl_init( 'https://api.reco.se/invite/mail/venue/' . $reco_data['ac_reco_plugin_reco_id'] );
			$options                                    = [
				'X-Reco-ApiKey: ' . $reco_data['ac_reco_plugin_reco_key'],
				'Content-Type: application/json',
			];
			$reco_api_data['invites'][]                 = (object) [
				'email'     => $address['email'],
				'firstName' => $address['first_name'],
				'lastName'  => $address['last_name'],
			];
			$reco_api_data['scheduled']['sendDateFrom'] = date( 'Y-m-d', strtotime( $order->get_date_completed() . ' + ' . $reco_data['ac_reco_plugin_send_interval'] . ' days ' ) );
			$reco_json_data                             = json_encode( $reco_api_data );
			curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'POST' );
			curl_setopt( $curl, CURLOPT_HTTPHEADER, $options );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $reco_json_data );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			$response = curl_exec( $curl );
			curl_close( $curl );
		}
	}


		/**
		 * Add a new settings tab to the WooCommerce settings tabs array.
		 *
		 * @param  array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
		 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
		 */
	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs['settings_tab_ac_reco'] = __( 'Recotrust', 'woocommerce-reco-plugin' );
		return $settings_tabs;
	}

		/**
		 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
		 *
		 * @uses woocommerce_admin_fields()
		 * @uses self::get_settings()
		 */
	public static function settings_tab() {
		echo "<div style='padding:20px 0;'>";
		echo "<img src='" . dirname( untrailingslashit( plugins_url( '/', __FILE__ ) ) ) . "/assets/images/reco-logo.svg'>";
		echo '<h1>Recotrust plugin Settings</h1>';
		printf( __( '<h4>You can add the Recotrust widget in your theme <a href="%1$s">widgets panel</a>.<h4>', 'woocommerce-reco-plugin' ), admin_url( 'widgets.php' ) );
		echo '';
		echo '<hr>';
		woocommerce_admin_fields( self::add_reco_settings() );
		echo '<hr>';
		echo '</div>';
	}
		/**
		 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
		 *
		 * @uses woocommerce_update_options()
		 * @uses self::get_settings()
		 */
	public static function update_settings() {
		woocommerce_update_options( self::add_reco_settings() );
	}
		/**
		 * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
		 *
		 * @return array Array of settings for @see woocommerce_admin_fields() function.
		 */
	public static function add_reco_settings() {
		$badges = [];
		for ( $i = date( 'Y' );$i >= ( date( 'Y' ) - 5 );$i-- ) {
			$badges[ $i ] = $i;
		}
		$settings = [
			'section_title'                => [
				'name' => __( ' ', 'woocommerce-reco-plugin' ),
				'type' => 'title',
			],
			'ac_reco_plugin_is_active'     => [
				'name'     => __( ' ' ),
				'desc_tip' => __( 'Check this box to activate the plugin.', 'woocommerce-reco-plugin' ),
				'id'       => 'woocommerce_reco_plugin_is_active',
				'type'     => 'checkbox',
				'css'      => '',
				'desc'     => __( 'Activate Recotrust Plugin', 'woocommerce-reco-plugin' ),
			],
			'ac_reco_plugin_reco_id'       => [
				'name'     => __( 'Recotrust ID', 'woocommerce-reco-plugin' ),
				'type'     => 'text',
				'desc_tip' => __( 'Your Recotrust ID can be found by logging in at Recotrust.com', 'woocommerce-reco-plugin' ),
				'id'       => 'woocommerce_reco_plugin_reco_id',
			],
			'ac_reco_plugin_api_key'       => [
				'name'     => __( 'Recotrust API Key', 'woocommerce-reco-plugin' ),
				'type'     => 'text',
				'desc_tip' => __( 'Email kundtjanst@recotrust.com to get your API key.', 'woocommerce-reco-plugin' ),
				'id'       => 'woocommerce_reco_plugin_api_key',
			],
			'ac_reco_plugin_send_interval' => [
				'name'     => __( 'Send review request (delay)', 'woocommerce-reco-plugin' ),
				'type'     => 'text',
				'desc_tip' => __( 'Specify how many days after the order is placed we should send a review request. Example: If you shipping time is 7 days set 14 so the customer has a chance to experience your product.', 'woocommerce-reco-plugin' ),
				'id'       => 'woocommerce_reco_plugin_send_interval',
			],
			'section_end'                  => [
				'type' => 'sectionend',
				'desc' => __( ' ', 'woocommerce-reco-plugin' ),
				'id'   => 'woocommerce_reco_plugin_section_end',
			],
		];
		return apply_filters( 'wc_settings_tab_ac_reco_settings', $settings );
	}
}
