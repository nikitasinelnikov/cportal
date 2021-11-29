<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$list_table = new cp\admin\Emails_List_Table(
	array(
		'singular' => __( 'Email Notification', 'cportal' ),
		'plural'   => __( 'Email Notifications', 'cportal' ),
		'ajax'     => false,
	)
);

/**
 * Filters the Email Notifications ListTable columns list
 *
 * @since 1.0
 * @hook cp_email_templates_columns
 *
 * @param {array} $columns Email Notifications ListTable columns.
 *
 * @return {array} Email Notifications ListTable columns "key => title" structure.
 */
$columns = apply_filters(
	'cp_email_templates_columns',
	array(
		'email'      => __( 'Email', 'cportal' ),
		'recipients' => __( 'Recipient(s)', 'cportal' ),
		'configure'  => '',
	)
);

$list_table->set_columns( $columns );
$list_table->prepare_items();
?>

<form action="" method="get" name="cp-settings-emails" id="cp-settings-emails">
	<input type="hidden" name="page" value="cp-settings" />
	<input type="hidden" name="tab" value="email" />

	<?php $list_table->display(); ?>
</form>
