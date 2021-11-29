<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// remove plugin system options
delete_option( 'cp_last_version_upgrade' );
delete_option( 'cp_first_activation_date' );
delete_option( 'cp_version' );
delete_option( 'cp_flush_rewrite_rules' );
delete_option( 'cp_hidden_admin_notices' );
