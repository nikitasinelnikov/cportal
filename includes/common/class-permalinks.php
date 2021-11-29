<?php namespace cp\common;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'cp\common\Permalinks' ) ) {


	/**
	 * Class Permalinks
	 *
	 * @package cp\common
	 */
	class Permalinks {


		/**
		 * Permalinks constructor.
		 */
		public function __construct() {
		}


		/**
		 * Get page slug
		 *
		 * @param string $key Pre-set page key
		 *
		 * @return string
		 *
		 * @since 1.0
		 */
		public function get_slug( $key ) {
			$preset_page_id = $this->get_predefined_page_id( $key );

			$slug = '';
			if ( $preset_page_id ) {
				$preset_page = get_post( $preset_page_id );
				if ( ! empty( $preset_page ) && ! is_wp_error( $preset_page ) ) {
					$slug = $preset_page->post_name;
				}
			}

			return $slug;
		}


		/**
		 * @param string $slug
		 *
		 * @return bool
		 */
		public function predefined_page_slug_exists( $slug ) {
			$predefined_pages = CP()->config()->get( 'predefined_pages' );
			return array_key_exists( $slug, $predefined_pages );
		}


		/**
		 * Get predefined page ID
		 *
		 * @param string $slug
		 *
		 * @return false|int
		 *
		 * @since 1.1.1
		 */
		public function get_predefined_page_id( $slug ) {
			if ( ! $this->predefined_page_slug_exists( $slug ) ) {
				return false;
			}

			$option_key = CP()->options()->get_predefined_page_option_key( $slug );

			/**
			 * Filters the predefined page ID.
			 *
			 * Note: See all predefined pages slugs in CP()->config()->get( 'predefined_pages' );
			 *
			 * Note: CPortal internally uses this hook for getting integrated with multilingual plugins that make duplicates of the pages for translations.
			 *
			 * @since 1.0
			 * @hook cp_get_predefined_page_id
			 *
			 * @param {string} $page_id The predefined page ID. The value obtained from options.
			 * @param {string} $slug    The predefined page slug. E.g. 'my-portal'.
			 *
			 * @return {string} The predefined page ID.
			 */
			$page_id = apply_filters( 'cp_get_predefined_page_id', CP()->options()->get( $option_key ), $slug );

			$page_exists = get_post( $page_id );
			if ( ! $page_exists ) {
				return false;
			}

			return (int) $page_id;
		}


		/**
		 *
		 * @param string $slug
		 * @param null|int|\WP_Post $post
		 *
		 * @return bool
		 */
		public function is_predefined_page( $slug, $post = null ) {
			// handle $post inside, just we need make $post as \WP_Post. Otherwise something is wrong and return false
			if ( ! $post ) {
				global $post;

				if ( empty( $post ) ) {
					return false;
				}
			} else {
				if ( is_numeric( $post ) ) {
					$post = get_post( $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- internal variable

					if ( empty( $post ) ) {
						return false;
					}
				}
			}

			if ( empty( $post->ID ) ) {
				return false;
			}

			$predefined_page_id = $this->get_predefined_page_id( $slug );
			$condition          = $post->ID === $this->get_predefined_page_id( $slug );

			/**
			 * Filters the condition check if the post is predefined page.
			 *
			 * Note: See all predefined pages slugs in CP()->config()->get( 'predefined_pages' );
			 *
			 * Note: CPortal internally uses this hook for getting integrated with multilingual plugins that make duplicates of the pages for translations.
			 *
			 * @since 1.0
			 * @hook cp_is_predefined_page
			 *
			 * @param {bool}    $is_predefined      Condition if the $post is the predefined page.
			 * @param {WP_Post} $post               The Post object current or passed to function.
			 * @param {int}     $predefined_page_id The predefined page ID. The value obtained from options via the page slug.
			 * @param {string}  $slug               The predefined page slug. E.g. 'my-portal'.
			 *
			 * @return {string} The predefined page ID.
			 */
			return apply_filters( 'cp_is_predefined_page', $condition, $post, $predefined_page_id, $slug );
		}


		/**
		 * Get predefined page URL
		 *
		 * @param string $slug
		 *
		 * @return false|string
		 *
		 * @since 1.1.1
		 */
		public function get_predefined_page_link( $slug ) {
			$url     = false;
			$page_id = $this->get_predefined_page_id( $slug );

			if ( ! empty( $page_id ) ) {
				$url = get_permalink( $page_id );
			}

			return $url;
		}


		/**
		 * Are CP pages installed
		 *
		 * @return bool
		 *
		 * @since 1.0
		 */
		public function are_pages_installed() {
			$installed = true;

			$pages            = array();
			$predefined_pages = array_keys( CP()->config()->get( 'predefined_pages' ) );
			if ( ! empty( $predefined_pages ) ) {
				foreach ( $predefined_pages as $slug ) {
					$option = CP()->options()->get( CP()->options()->get_predefined_page_option_key( $slug ) );
					if ( ! empty( $option ) ) {
						$pages[ $slug ] = $option;
					}
				}
			}

			if ( empty( $pages ) || count( $pages ) !== count( $predefined_pages ) ) {
				$installed = false;
			} else {
				foreach ( $pages as $page_id ) {
					$page = get_post( $page_id );

					if ( ! isset( $page->ID ) ) {
						$installed = false;
						break;
					}
				}
			}

			return $installed;
		}
	}
}
