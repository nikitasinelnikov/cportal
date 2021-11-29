<?php namespace cp\admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( '\WP_List_Table' ) ) {
	/** @noinspection PhpIncludeInspection */
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}


if ( ! class_exists( 'cp\admin\Emails_List_Table' ) ) {


	/**
	 * Class Emails_List_Table
	 */
	class Emails_List_Table extends \WP_List_Table {


		/**
		 * @var string
		 */
		private $no_items_message = '';


		/**
		 * @var array
		 */
		private $columns = array();


		/**
		 * CP_Emails_List_Table constructor.
		 *
		 * @param array $args
		 */
		public function __construct( $args = array() ) {
			$args = wp_parse_args(
				$args,
				array(
					'singular' => __( 'item', 'cportal' ),
					'plural'   => __( 'items', 'cportal' ),
					'ajax'     => false,
				)
			);

			$this->no_items_message = $args['plural'] . ' ' . __( 'not found.', 'cportal' );

			parent::__construct( $args );
		}


		/**
		 * @param callable $name
		 * @param array $arguments
		 *
		 * @return mixed
		 */
		public function __call( $name, $arguments ) {
			return call_user_func_array( array( $this, $name ), $arguments );
		}


		/**
		 *
		 */
		public function prepare_items() {
			$screen = $this->screen;

			$columns = $this->get_columns();

			$this->_column_headers = array( $columns, array(), array() );

			$emails = CP()->config()->get( 'email_notifications' );

			uasort(
				$emails,
				function ( $a, $b ) {
					if ( strtolower( $a['title'] ) === strtolower( $b['title'] ) ) {
						return 0;
					}
					return ( strtolower( $a['title'] ) < strtolower( $b['title'] ) ) ? -1 : 1;
				}
			);

			$per_page = $this->get_items_per_page( str_replace( '-', '_', $screen->id . '_per_page' ), 999 );
			$paged    = $this->get_pagenum();

			$this->items = array_slice( $emails, ( $paged - 1 ) * $per_page, $per_page );

			$this->set_pagination_args(
				array(
					'total_items' => count( $emails ),
					'per_page'    => $per_page,
				)
			);
		}


		/**
		 * @param object $item
		 * @param string $column_name
		 *
		 * @return string
		 */
		protected function column_default( $item, $column_name ) {
			if ( isset( $item[ $column_name ] ) ) {
				return $item[ $column_name ];
			} else {
				/**
				 * Filters the custom column content in the Email Notifications ListTable
				 *
				 * @since 1.0
				 * @hook cp_emails_list_table_custom_column_content
				 *
				 * @param {string} $content     Custom column content. It's '' by default.
				 * @param {array}  $item        Row item with columns data.
				 * @param {string} $column_name Column name (key).
				 *
				 * @return {string} Custom column content.
				 */
				return apply_filters( 'cp_emails_list_table_custom_column_content', '', $item, $column_name );
			}
		}


		/**
		 *
		 */
		public function no_items() {
			echo esc_html( $this->no_items_message );
		}


		/**
		 * @param array $args
		 *
		 * @return $this
		 */
		public function set_columns( $args = array() ) {
			$this->columns = $args;

			return $this;
		}


		/**
		 * @return array
		 */
		public function get_columns() {
			return $this->columns;
		}


		/**
		 * @param $item
		 *
		 * @return string
		 */
		protected function column_email( $item ) {
			$active = CP()->options()->get( $item['key'] . '_on' );

			return '<span class="dashicons cp-notification-status ' . ( ! empty( $active ) ? 'cp-notification-is-active dashicons-yes' : 'dashicons-no-alt' ) . '"></span><a href="' . add_query_arg( array( 'email' => $item['key'] ) ) . '"><strong>' . esc_html( $item['title'] ) . '</strong></a>';
		}


		/**
		 * @param $item
		 *
		 * @return string
		 */
		protected function column_recipients( $item ) {
			if ( 'admin' === $item['recipient'] ) {
				return CP()->options()->get( 'admin_email' );
			} else {
				return __( 'Member', 'cportal' );
			}
		}


		/**
		 * @param $item
		 *
		 * @return string
		 */
		protected function column_configure( $item ) {
			return '<a class="button cp-email-configure" href="' . add_query_arg( array( 'email' => $item['key'] ) ) . '"><span class="dashicons dashicons-admin-generic"></span></a>';
		}
	}
}
