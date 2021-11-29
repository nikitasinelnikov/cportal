<?php
/*
Plugin Name: CPortal
Plugin URI: https://cportal.com/
Description: Add a modern job board to your website. Display job listings and allow employers to submit and manage jobs all from the front-end
Version: 0.1
Author: CPortal
Text Domain: cportal
Domain Path: /languages
*/

defined( 'ABSPATH' ) || exit;

/** @noinspection PhpIncludeInspection */
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plugin_data = get_plugin_data( __FILE__ );

define( 'CP_URL', plugin_dir_url( __FILE__ ) );
define( 'CP_PATH', plugin_dir_path( __FILE__ ) );
define( 'CP_PLUGIN', plugin_basename( __FILE__ ) );
define( 'CP_VERSION', $plugin_data['Version'] );
define( 'CP_PLUGIN_NAME', $plugin_data['Name'] );

require_once 'includes/class-cp-functions.php';
require_once 'includes/class-cp.php';

//run
CP();
