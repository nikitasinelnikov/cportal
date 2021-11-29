<?php namespace cp;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'cp\Config' ) ) {


	/**
	 * Class Config
	 *
	 * @package cp
	 */
	final class Config {


		/**
		 * @var array
		 */
		public $defaults;


		/**
		 * @var
		 */
		public $custom_roles;


		/**
		 * @var
		 */
		public $all_caps;


		/**
		 * @var
		 */
		public $capabilities_map;


		/**
		 * @var
		 */
		public $permalink_options;


		/**
		 * @var
		 */
		public $predefined_pages;


		/**
		 * @var
		 */
		public $email_notifications;


		/**
		 * Config constructor.
		 */
		public function __construct() {
		}


		/**
		 * Get variable from config
		 *
		 * @param string $key
		 *
		 * @return mixed
		 *
		 * @since 1.0
		 */
		public function get( $key ) {
			if ( empty( $this->$key ) ) {
				call_user_func( array( &$this, 'init_' . $key ) );
			}

			/**
			 * Filters the variable before getting it from the config
			 *
			 * @since 1.0
			 * @hook cp_config_get
			 *
			 * @param {mixed}  $data The predefined data in config.
			 * @param {string} $key  The predefined data key. E.g. 'predefined_pages'.
			 *
			 * @return {mixed} The predefined page ID.
			 */
			return apply_filters( 'cp_config_get', $this->$key, $key );
		}


		/**
		 * Init plugin defaults
		 *
		 * @since 1.0
		 */
		public function init_defaults() {
			// Use this structure for defaults array "setting-key => setting-value"
			$this->defaults = array();

			foreach ( $this->get( 'email_notifications' ) as $key => $notification ) {
				$this->defaults[ $key . '_on' ]  = ! empty( $notification['default_active'] );
				$this->defaults[ $key . '_sub' ] = $notification['subject'];
			}

			foreach ( $this->get( 'predefined_pages' ) as $slug => $array ) {
				$this->defaults[ CP()->options()->get_predefined_page_option_key( $slug ) ] = '';
			}
		}


		/**
		 * Initialize CP custom roles list
		 *
		 * @since 1.0
		 */
		public function init_custom_roles() {
			$this->custom_roles = array(
				'cp_role' => __( 'CP Role', 'cportal' ),
			);
		}


		/**
		 * Initialize CP roles capabilities list
		 *
		 * @since 1.0
		 */
		public function init_capabilities_map() {
			$this->capabilities_map = array(
				'administrator' => array(
					'cp_manage_capability',
					'cp_capability',
				),
				'cp_role'       => array(
					'cp_capability',
				),
			);
		}


		/**
		 * Initialize CP custom capabilities
		 *
		 * @since 1.0
		 */
		public function init_all_caps() {
			$this->all_caps = array(
				'cp_manage_capability',
				'cp_capability',
			);
		}


		/**
		 * Initialize CP permalink options
		 *
		 * @since 1.0
		 */
		public function init_permalink_options() {
			$this->permalink_options = array(
				'cp-slug',
			);
		}


		/**
		 * Initialize CP predefined pages
		 *
		 * @since 1.0
		 */
		public function init_predefined_pages() {
			$this->predefined_pages = array(
				'cp_page' => array(
					'title'   => __( 'CP Page', 'cportal' ),
					'content' => '[cp_shortcode /]',
				),
			);
		}


		/**
		 * Initialize CP email notifications
		 *
		 * @since 1.0
		 */
		public function init_email_notifications() {
			$this->email_notifications = array(
				'cp_notification'      => array(
					'key'            => 'cp_notification',
					'title'          => __( 'CP Notification', 'cportal' ),
					'subject'        => __( 'CP Notification - {site_name}', 'cportal' ),
					'description'    => __( 'Whether to send an email to admin when CP action on website.', 'cportal' ),
					'recipient'      => 'admin',
					'default_active' => true,
				),
				'cp_user_notification' => array(
					'key'            => 'cp_user_notification',
					'title'          => __( 'CP User Notification', 'cportal' ),
					'subject'        => __( 'CP User Notification - {site_name}', 'cportal' ),
					'description'    => __( 'Whether to send an email to user when CP action on website.', 'cportal' ),
					'recipient'      => 'user',
					'default_active' => true,
				),
			);
		}
	}
}
