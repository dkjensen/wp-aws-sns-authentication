<?php
/**
 * Upgrade class file
 * 
 * @package AWS SNS Verification
 */

namespace SeattleWebCo\AWSSNSVerification;

use SeattleWebCo\AWSSNSVerification\Abstracts\HookListener;

class Upgrade extends HookListener {

    /**
     * Current version of database tables
     *
     * @var string
     */
    public $db_version = '1.0.5';

    /**
     * Check if the database needs to be updated
     *
     * @return void
     */
    public function needs_update() {
        $current_ver = get_option( 'aws_sns_verification_db_ver' );

        if ( version_compare( $current_ver, $this->db_version, '<' ) ) {
            return true;
        }

        return false;
    }

    /**
     * Install tables and update database version option
     *
     * @return void
     */
    public function install_tables() {
        global $wpdb;
        
        if ( ! $this->needs_update() ) {
            return;
        }

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "
            CREATE TABLE {$wpdb->prefix}user_verification (
                id                  BIGINT(11) UNSIGNED NOT NULL auto_increment, 
                user_id             BIGINT(20) UNSIGNED NOT NULL, 
                phone_e164          VARCHAR(50) DEFAULT NULL, 
                verification_code INT(11) DEFAULT NULL, 
                created_date        DATETIME DEFAULT CURRENT_TIMESTAMP,
                verified_date       DATETIME DEFAULT NULL,
                verified            TINYINT(1) NOT NULL DEFAULT 0, 
                type                VARCHAR(50) NOT NULL
                PRIMARY KEY (id) 
                UNIQUE KEY user_verification (user_id,type)
            )
            $charset_collate
        ";

        require_once constant( 'ABSPATH' ) . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        update_option( 'aws_sns_verification_db_ver', $this->db_version );
    }

    /**
     * Display admin notice if database tables need updating
     *
     * @return void
     */
    public function admin_notices_action() {
        if ( $this->needs_update() ) {
            ?>

            <div class="notice notice-warning">
                <p>
                    <?php _e( 'The AWS SNS Verification database needs to be updated.', 'aws-sns-verification' ); ?> 
                    <a href="<?php echo wp_nonce_url( add_query_arg( [ 'aws-sns-auth-install' => 1 ] ), 'aws-sns-auth-install' ); ?>"><?php _e( 'Update Now', 'aws-sns-verification' ); ?></a>
                </p>
            </div>

            <?php
        }
    }

    /**
     * Update handler from admin notice update link
     *
     * @return void
     */
    public function admin_init_action() {
        if ( empty ( $_GET['aws-sns-auth-install'] ) ) {
            return;
        }

        if ( wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'aws-sns-auth-install' ) && current_user_can( 'update_plugins' ) ) {
            $this->install_tables();

            wp_redirect( remove_query_arg( [ 'aws-sns-auth-install', '_wpnonce' ] ) );
            exit;
        }
    }
}
