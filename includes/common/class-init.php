<?php namespace cp\common;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'cp\common\Init' ) ) {


	/**
	 * Class Init
	 *
	 * @package cp\common
	 */
	class Init {


		/**
		 * Init constructor.
		 */
		public function __construct() {
		}


		/**
		 * Common plugin includes
		 *
		 * @since 1.0
		 */
		public function includes() {

		}


		/**
		 * @since 1.0
		 *
		 * @return Permalinks
		 */
		public function permalinks() {
			if ( empty( CP()->classes['cp\common\permalinks'] ) ) {
				CP()->classes['cp\common\permalinks'] = new Permalinks();
			}

			return CP()->classes['cp\common\permalinks'];
		}


		/**
		 * @since 1.0
		 *
		 * @return Rewrite
		 */
		public function rewrite() {
			if ( empty( CP()->classes['cp\common\rewrite'] ) ) {
				CP()->classes['cp\common\rewrite'] = new Rewrite();
			}

			return CP()->classes['cp\common\rewrite'];
		}
	}
}
