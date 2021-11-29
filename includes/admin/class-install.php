<?php namespace cp\admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'cp\admin\Install' ) ) {


	/**
	 * Class Install
	 * @package cp\admin
	 */
	class Install {


		/**
		 * @var bool
		 */
		public $install_process = false;


		/**
		 * Install constructor.
		 */
		public function __construct() {
		}


		/**
		 * Plugin Activation
		 *
		 * @since 1.0
		 */
		public function activation() {
			$this->install_process = true;

			$this->single_site_activation();
			if ( is_multisite() ) {
				update_network_option( get_current_network_id(), 'cp_maybe_network_wide_activation', 1 );
			}

			$this->install_process = false;
		}


		/**
		 * Check if plugin is network activated make the first installation on all blogs
		 *
		 * @since 1.0
		 */
		public function maybe_network_activation() {
			$maybe_activation = get_network_option( get_current_network_id(), 'cp_maybe_network_wide_activation' );

			if ( $maybe_activation ) {

				delete_network_option( get_current_network_id(), 'cp_maybe_network_wide_activation' );

				if ( is_plugin_active_for_network( CP_PLUGIN ) ) {
					// get all blogs
					$blogs = get_sites();
					if ( ! empty( $blogs ) ) {
						foreach ( $blogs as $blog ) {
							switch_to_blog( $blog->blog_id );
							//make activation script for each sites blog
							$this->single_site_activation();
							restore_current_blog();
						}
					}
				}
			}
		}


		/**
		 * Single site plugin activation handler
		 *
		 * @since 1.0
		 */
		public function single_site_activation() {
			$version = CP()->options()->get( 'version' );
			if ( ! $version ) {
				//set first install date and set current version as last upgrade version
				CP()->options()->update( 'last_version_upgrade', CP_VERSION );
				CP()->options()->add( 'first_activation_date', time() );
			}

			if ( CP_VERSION !== $version ) {
				// update current version on first install or activation another version
				CP()->options()->update( 'version', CP_VERSION );
			}

			//set default settings
			$this->set_defaults( CP()->config()->get( 'defaults' ) );
			//create custom roles + upgrade capabilities
			$this->create_roles();

			if ( ! $version ) {
				// if no version in options then it's a first install
				$this->first_install();
			}

			CP()->common()->rewrite()->reset_rules();
		}


		/**
		 * Actions on the first install
		 *
		 * @since 1.0
		 */
		public function first_install() {

		}


		/**
		 * Set default CP settings
		 *
		 * @param array $defaults
		 *
		 * @since 1.0
		 */
		public function set_defaults( $defaults ) {
			if ( ! empty( $defaults ) ) {
				foreach ( $defaults as $key => $value ) {
					add_option( CP()->options()->get_key( $key ), $value );
				}
			}
		}


		/**
		 * Parse user capabilities and set the proper capabilities for roles
		 *
		 * @since 1.0
		 */
		public function create_roles() {
			global $wp_roles;

			if ( ! class_exists( '\WP_Roles' ) ) {
				return;
			}

			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new \WP_Roles(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- only if ! isset
			}

			$all_caps         = CP()->config()->get( 'all_caps' );
			$custom_roles     = CP()->config()->get( 'custom_roles' );
			$capabilities_map = CP()->config()->get( 'capabilities_map' );

			foreach ( $custom_roles as $role_id => $role_title ) {
				$wp_roles->remove_role( $role_id );

				if ( empty( $capabilities_map[ $role_id ] ) ) {
					$capabilities_map[ $role_id ] = array();
				}

				add_role( $role_id, $role_title, $capabilities_map[ $role_id ] );
			}

			foreach ( $capabilities_map as $role_id => $caps ) {
				foreach ( array_diff( $caps, $all_caps ) as $cap ) {
					$wp_roles->remove_cap( $role_id, $cap );
				}

				foreach ( $caps as $cap ) {
					$wp_roles->add_cap( $role_id, $cap );
				}
			}
		}


		/**
		 * Install all predefined pages
		 *
		 * @since 1.0
		 */
		public function predefined_pages() {
			foreach ( CP()->config()->get( 'predefined_pages' ) as $slug => $array ) {
				$this->predefined_page( $slug );
			}
		}


		/**
		 * Install predefined page via the page slug
		 *
		 * @param $slug
		 */
		public function predefined_page( $slug ) {
			$predefined_pages = CP()->config()->get( 'predefined_pages' );
			if ( empty( $predefined_pages ) || ! CP()->common()->permalinks()->predefined_page_slug_exists( $slug ) ) {
				return;
			}

			$option_key = CP()->options()->get_predefined_page_option_key( $slug );

			$page_id = CP()->options()->get( $option_key );
			if ( ! empty( $page_id ) ) {
				$page = get_post( $page_id );

				if ( isset( $page->ID ) ) {
					return;
				}
			}

			$data = $predefined_pages[ $slug ];

			if ( empty( $data['title'] ) ) {
				return;
			}

			$user_page = array(
				'post_title'     => $data['title'],
				'post_content'   => ! empty( $data['content'] ) ? $data['content'] : '',
				'post_name'      => $slug,
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'post_author'    => get_current_user_id(),
				'comment_status' => 'closed',
			);

			$post_id = wp_insert_post( $user_page );
			if ( empty( $post_id ) || is_wp_error( $post_id ) ) {
				return;
			}

			CP()->options()->update( $option_key, $post_id );
		}

	}
}
