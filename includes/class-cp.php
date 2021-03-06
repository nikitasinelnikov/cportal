<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'CP' ) ) {


	/**
	 * Main CP Class
	 *
	 * @class CP
	 * @version 1.0
	 */
	final class CP extends CP_Functions {


		/**
		 * @var CP the single instance of the class
		 */
		private static $instance = null;


		/**
		 * @var array all plugin's classes
		 */
		public $classes = array();


		/**
		 * Main CP Instance
		 *
		 * Ensures only one instance of CP is loaded or can be loaded.
		 *
		 * @since 1.0
		 * @static
		 * @see CP()
		 * @return CP - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
				self::$instance->cp_construct();
			}

			return self::$instance;
		}


		/**
		 * Cloning is forbidden.
		 *
		 * @since 1.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden.', 'cportal' ), '1.0' );
		}


		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is forbidden.', 'cportal' ), '1.0' );
		}


		/**
		 * CP pseudo-constructor.
		 *
		 * @since 1.0
		 */
		public function cp_construct() {
			$this->define_constants();

			//register autoloader for include CP classes
			spl_autoload_register( array( $this, 'cp__autoloader' ) );

			if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
				// run activation
				register_activation_hook( CP_PLUGIN, array( $this->install(), 'activation' ) );
				if ( is_multisite() && ! defined( 'DOING_AJAX' ) ) {
					add_action( 'wp_loaded', array( $this->install(), 'maybe_network_activation' ) );
				}

				// textdomain loading
				$this->localize();

				// include CP classes
				$this->includes();
			}
		}


		/**
		 * Define CPortal Constants.
		 *
		 * @since 3.0
		 */
		private function define_constants() {
			$this->define( 'CP_TEMPLATE_CONFLICT_TEST', false );
		}


		/**
		 * Autoload CP classes handler
		 *
		 * @since 1.0
		 *
		 * @param $class
		 */
		public function cp__autoloader( $class ) {
			if ( strpos( $class, 'cp' ) === 0 ) {
				$array                        = explode( '\\', strtolower( $class ) );
				$array[ count( $array ) - 1 ] = 'class-' . end( $array );

				if ( strpos( $class, 'cp\\' ) === 0 ) {
					$class     = implode( '\\', $array );
					$path      = str_replace( array( 'cp\\', '_', '\\' ), array( DIRECTORY_SEPARATOR, '-', DIRECTORY_SEPARATOR ), $class );
					$full_path = CP_PATH . 'includes' . $path . '.php';
				}

				if ( isset( $full_path ) && file_exists( $full_path ) ) {
					/** @noinspection PhpIncludeInspection */
					include_once $full_path;
				}
			}
		}


		/**
		 * Loading CP textdomain
		 *
		 * 'cportal' by default
		 *
		 * @since 1.0
		 */
		public function localize() {
			$language_locale = ( '' !== get_locale() ) ? get_locale() : 'en_US';
			/**
			 * Filters the language locale before loading textdomain
			 *
			 * @since 1.0
			 * @hook cp_language_locale
			 *
			 * @param {string} $language_locale Current language locale.
			 *
			 * @return {string} Maybe changed language locale.
			 */
			$language_locale = apply_filters( 'cp_language_locale', $language_locale );
			/**
			 * Filters the plugin's textdomain
			 *
			 * @since 1.0
			 * @hook cp_language_textdomain
			 *
			 * @param {string} $textdomain Plugin's textdomain.
			 *
			 * @return {string} Maybe changed plugin's textdomain.
			 */
			$language_domain = apply_filters( 'cp_language_textdomain', 'cportal' );

			$language_file = WP_LANG_DIR . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $language_domain . '-' . $language_locale . '.mo';
			/**
			 * Filters the path to the language file (*.mo)
			 *
			 * @since 1.0
			 * @hook cp_language_file
			 *
			 * @param {string} $language_file Default path to the language file.
			 *
			 * @return {string} Language file path.
			 */
			$language_file = apply_filters( 'cp_language_file', $language_file );

			load_textdomain( $language_domain, $language_file );
		}


		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		public function includes() {
			$this->common()->includes();
			if ( $this->is_request( 'ajax' ) ) {
				$this->ajax()->includes();
			} elseif ( $this->is_request( 'admin' ) ) {
				$this->admin()->includes();
			} elseif ( $this->is_request( 'frontend' ) ) {
				$this->frontend()->includes();
			}
		}


		/**
		 * Getting the Config class instance
		 *
		 * @since 1.0
		 *
		 * @return cp\Config
		 */
		public function config() {
			return $this->call_class( 'cp\Config' );
		}


		/**
		 * Getting the Install class instance
		 *
		 * @since 1.0
		 *
		 * @return cp\admin\Install()
		 */
		public function install() {
			return $this->call_class( 'cp\admin\Install' );
		}


		/**
		 * Getting the Options class instance
		 *
		 * @since 1.0
		 *
		 * @return cp\common\Options()
		 */
		public function options() {
			return $this->call_class( 'cp\common\Options' );
		}


		/**
		 * Getting the Common class instance
		 *
		 * @since 1.0
		 *
		 * @return cp\common\Init()
		 */
		public function common() {
			return $this->call_class( 'cp\common\Init' );
		}


		/**
		 * Getting the Admin class instance
		 *
		 * @since 1.0
		 *
		 * @return cp\admin\Init()
		 */
		public function admin() {
			return $this->call_class( 'cp\admin\Init' );
		}


		/**
		 * Getting the Frontend class instance
		 *
		 * @since 1.0
		 *
		 * @return cp\frontend\Init()
		 */
		public function frontend() {
			return $this->call_class( 'cp\frontend\Init' );
		}


		/**
		 * Getting the AJAX class instance
		 *
		 * @since 1.0
		 *
		 * @return cp\ajax\Init()
		 */
		public function ajax() {
			return $this->call_class( 'cp\ajax\Init' );
		}


		/**
		 * @param string $class
		 *
		 * @return mixed
		 *
		 * @since 1.0
		 */
		private function call_class( $class ) {
			$key = strtolower( $class );

			if ( empty( $this->classes[ $key ] ) ) {
				$this->classes[ $key ] = new $class();
			}

			return $this->classes[ $key ];
		}

	}
}


/**
 * Function for calling CP methods and variables
 *
 * @since 1.0
 *
 * @return CP
 */
function CP() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return CP::instance();
}
