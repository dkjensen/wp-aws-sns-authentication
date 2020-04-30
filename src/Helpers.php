<?php
/**
 * Helper methods file
 * 
 * @package AWS SNS Verification
 */

namespace SeattleWebCo\AWSSNSVerification;

class Helpers {

    /**
     * Return the users phone number
     *
     * @param integer $user_id
     * @return string
     */
    public static function get_user_phone( $user_id ) {
        $phone = get_user_meta( $user_id, 'phone', true );

        return apply_filters( 'aws_sns_verification_get_user_phone', $phone, $user_id );
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

    /**
     * How long should the verification code length be?
     *
     * @return integer
     */
    public static function get_verification_code_length() {
        return absint( apply_filters( 'aws_sns_verification_code_length', 6 ) );
    }

    /**
     * Generate a random code from a predefined length
     *
     * @param integer $length
     * @return string
     */
    public static function generate_random_verification_code( int $length = 0 ) {
        if ( ! $length ) {
            $length = self::get_verification_code_length();
        }

        $code = '';
        
        for ( $i = 0; $i < $length; $i++ ) {
            $code .= mt_rand( 0, 9 );
        }

        return $code;
    }

    /**
     * Get the verification code SMS message
     *
     * @param integer $user_id
     * @param string $code
     * @return string
     */
    public static function get_verification_sms_message( $user_id, $code ) {
        $message = sprintf( __( 'Your account verification code is: %s', 'aws-sns-verification' ), $code );

        return apply_filters( 'aws_sns_verification_sms_message', $message, $user_id, $code );
    }

}
