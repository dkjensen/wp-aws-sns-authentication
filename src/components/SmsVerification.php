<?php
/**
 * SMS Verification component class file.
 * 
 * @package AWS SNS Verification
 */

namespace SeattleWebCo\AWSSNSVerification\Components;

class SmsVerification {

    /**
     * Insert required verification row 
     *
     * @param integer $user_id
     * @param string $code
     * @return boolean
     */
    public function add_sms_verification( $user_id, $code ) {
        global $wpdb;

        $phone = get_user_meta( $user_id, 'phone_e164', true );

        return (bool) $wpdb->insert( $wpdb->prefix . 'user_verification', [
            'user_id'               => $user_id,
            'phone_e164'            => $phone,
            'verification_code'   => $code,
            'type'                  => 'sms'
        ] );
    }

    /**
     * Check if the user is verified with a given verification type
     *
     * @param integer $user_id
     * @param string $type
     * @return boolean
     */
    public function is_user_verified( $user_id, $type ) {
        global $wpdb;

        return (bool) $wpdb->get_var( $wpdb->prepare( "
            SELECT      verified
            FROM        {$wpdb->prefix}user_verification 
            WHERE       user_id = %s 
            AND         type = %s
        ", $user_id, $type ) );
    }

}
