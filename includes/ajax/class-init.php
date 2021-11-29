<?php namespace cp\ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'cp\ajax\Init' ) ) {


	/**
	 * Class Init
	 *
	 * @package cp\ajax
	 */
	class Init {


		/**
		 * Init constructor.
		 */
		public function __construct() {
			add_action( 'admin_init', array( CP(), 'init_current_locale' ), 0 );
		}


		/**
		 * All AJAX includes
		 *
		 * @since 1.0
		 */
		public function includes() {

		}


		/**
		 * Check nonce
		 *
		 * @param bool|string $action
		 *
		 * @since 1.0
		 */
		public function check_nonce( $action = false ) {
			$nonce  = isset( $_REQUEST['nonce'] ) ? sanitize_key( $_REQUEST['nonce'] ) : '';
			$action = empty( $action ) ? 'cp-common-nonce' : $action;

			if ( ! wp_verify_nonce( $nonce, $action ) ) {
				wp_send_json_error( __( 'Wrong AJAX Nonce', 'cportal' ) );
			}
		}
	}
}
