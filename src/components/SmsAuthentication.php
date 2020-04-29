<?php
/**
 * SMS Authentication component class file.
 * 
 * @package AWS SNS Authentication
 */

namespace SeattleWebCo\AWSSNSAuthentication\Components;

class SmsAuthentication {

    /**
     * Insert required authentication row 
     *
     * @param integer $user_id
     * @param string $code
     * @return boolean
     */
    public function add_sms_authentication( $user_id, $code ) {
        global $wpdb;

        $phone = get_user_meta( $user_id, 'phone_e164', true );

        return (bool) $wpdb->insert( $wpdb->prefix . 'user_authentication', [
            'user_id'               => $user_id,
            'phone_e164'            => $phone,
            'authentication_code'   => $code,
            'type'                  => 'sms'
        ] );
    }

    public function is_user_authenticated( $user_id, $type ) {
        global $wpdb;

        return (bool) $wpdb->get_var( $wpdb->prepare( "
            SELECT      verified
            FROM        {$wpdb->prefix}user_authentication 
            WHERE       user_id = %s 
            AND         type = %s
        ", $user_id, $type ) );
    }

}