<?php
/**
 * Plugin Name:     AWS SNS Verification
 * Description:     Send messages via SNS to verify users during login or registration
 * Version:         1.0.0
 * Author:          Seattle Web Co.
 * Author URI:      https://seattlewebco.com
 * Text Domain:     aws-sns-verification
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
function aws_sns_verification_version() {
    if ( function_exists( 'get_plugin_data' ) ) {
        return get_plugin_data( __FILE__ )['Version'];
    }

    return null;
}

// Constants
define( 'AWS_SNS_VERIFICATION_VER', aws_sns_verification_version() ?? '1.0.0' );
define( 'AWS_SNS_VERIFICATION_DIR', plugin_dir_path( __FILE__ ) );
define( 'AWS_SNS_VERIFICATION_URL', plugin_dir_url( __FILE__ ) );

// Include Composer packages
require_once AWS_SNS_VERIFICATION_DIR . 'vendor/autoload.php';

new SeattleWebCo\AWSSNSVerification\AWS_SNS_Verification;

register_activation_hook( __FILE__, function() {
    $upgrade = new \SeattleWebCo\AWSSNSVerification\Upgrade;
    $upgrade->install_tables();

    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
