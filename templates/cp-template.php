<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filters template's data just for the testing "templates" folder
 *
 * @since 1.0
 * @hook cp_template_data
 *
 * @param {array} $data Data.
 *
 * @return {array} Data.
 */
$data = apply_filters( 'cp_template_data', array() );
?>

<div id="cp-data" class="cp"></div>
