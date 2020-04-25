<?php
/**
 * Helper methods file
 * 
 * @package AWS SNS Authentication
 */

namespace SeattleWebCo\AWSSNSAuthentication;

class Helpers {

    /**
     * Return the users phone number
     *
     * @param integer $user_id
     * @return string
     */
    public static function get_user_phone( $user_id ) {
        $phone = get_user_meta( $user_id, 'phone', true );

        return apply_filters( 'aws_sns_authentication_get_user_phone', $phone, $user_id );
    }

    /**
     * Sanitize a given phone number
     *
     * @param string $phone
     * @return string
     */
    public static function sanitize_phone_number( $phone ) {
        return preg_replace( '/[^\+\d]/', '', $phone );
    }

}
