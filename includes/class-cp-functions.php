<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'CP_Functions' ) ) {


	/**
	 * Class CP_Functions
	 */
	class CP_Functions {


		/**
		 * @var bool CPU Links Structure
		 *
		 * @since 1.0
		 */
		public $is_permalinks;


		/**
		 * @var string Standard or Minified versions
		 *
		 * @since 1.0
		 */
		public $scrips_prefix = '';


		/**
		 * What type of request is this?
		 *
		 * @param string $type String containing name of request type (AJAX, frontend, WP Cron or wp-admin)
		 *
		 * @return bool
		 *
		 * @since 1.0
		 */
		public function is_request( $type ) {
			switch ( $type ) {
				case 'admin':
					return is_admin();
				case 'ajax':
					return defined( 'DOING_AJAX' );
				case 'cron':
					return defined( 'DOING_CRON' );
				case 'frontend':
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}

			return false;
		}


		/**
		 * Define constant if not already set.
		 *
		 * @since 1.1.1
		 * @access protected
		 *
		 * @param string      $name  Constant name.
		 * @param string|bool $value Constant value.
		 */
		protected function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}


		/**
		 * Easy merge arrays based on parent array key. Insert after selected key
		 *
		 * @since 1.1.1
		 *
		 * @param array $array
		 * @param string $key
		 * @param array $insert_array
		 *
		 * @return array
		 */
		public function array_insert_after( $array, $key, $insert_array ) {
			$index = array_search( $key, array_keys( $array ), true );
			if ( false === $index ) {
				return $array;
			}

			$array = array_slice( $array, 0, $index + 1, true ) + $insert_array + array_slice( $array, $index + 1, count( $array ) - 1, true );

			return $array;
		}


		/**
		 * Init current locale if exists
		 */
		public function init_current_locale() {
			// phpcs:disable WordPress.Security.NonceVerification -- don't need verifying there just the information about locale from JS to AJAX handlers
			if ( ! empty( $_REQUEST['cp_current_locale'] ) ) {
				$locale = sanitize_key( $_REQUEST['cp_current_locale'] );

				/**
				 * Fires after render all admin notices
				 *
				 *
				 * @since 1.0
				 * @hook cp_admin_init_locale
				 *
				 * @param {string} $locale Current locale from $_REQUEST.
				 */
				do_action( 'cp_admin_init_locale', $locale );
			}
			// phpcs:enable WordPress.Security.NonceVerification
		}
	}
}
