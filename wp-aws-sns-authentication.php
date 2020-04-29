<?php
/**
 * Plugin Name:     AWS SNS Authentication
 * Description:     Send messages via SNS to authenticate users during login or registration
 * Version:         1.0.0
 * Author:          Seattle Web Co.
 * Author URI:      https://seattlewebco.com
 * Text Domain:     aws-sns-authentication
 * Domain Path:     /languages/
 * Contributors:    seattlewebco, dkjensen
 * Requires PHP:    7.0.0
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Return version of this plugin
 *
 * @return void
 */
function aws_sns_authentication_version() {
    if ( function_exists( 'get_plugin_data' ) ) {
        return get_plugin_data( __FILE__ )['Version'];
    }

    return null;
}

// Constants
define( 'AWS_SNS_AUTHENTICATION_VER', aws_sns_authentication_version() ?? '1.0.0' );
define( 'AWS_SNS_AUTHENTICATION_DIR', plugin_dir_path( __FILE__ ) );
define( 'AWS_SNS_AUTHENTICATION_URL', plugin_dir_url( __FILE__ ) );

// Include Composer packages
require_once AWS_SNS_AUTHENTICATION_DIR . 'vendor/autoload.php';

new SeattleWebCo\AWSSNSAuthentication\AWS_SNS_Authentication;

register_activation_hook( __FILE__, function() {
    $install = new \SeattleWebCo\AWSSNSAuthentication\Install;
    $install->install_tables();

    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
