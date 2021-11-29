<?php namespace cp\admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'cp\admin\Init' ) ) {


	/**
	 * Class Init
	 *
	 * @package cp\admin
	 */
	class Init {


		/**
		 * @var string
		 *
		 * @since 1.0
		 */
		public $templates_path = '';


		/**
		 * Init constructor.
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init_variables' ), 10 );
			add_action( 'admin_init', array( CP(), 'init_current_locale' ), 0 );
		}


		/**
		 * Init admin variables
		 *
		 * @since 1.0
		 */
		public function init_variables() {
			$this->templates_path = CP_PATH . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;
		}


		/**
		 * All admin includes in one function
		 *
		 * @since 1.0
		 */
		public function includes() {

		}
	}
}
