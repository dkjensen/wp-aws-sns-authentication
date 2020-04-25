<?php
/**
 * Register action class file
 * 
 * @package AWS SNS Authentication
 */

namespace SeattleWebCo\AWSSNSAuthentication\Actions;

use SeattleWebCo\AWSSNSAuthentication\Helpers;
use SeattleWebCo\AWSSNSAuthentication\Actions\Action;

class Register extends Action {

    /**
     * Add a phone number field to registration form
     *
     * @return void
     */
    public function register_form_action() {
        ?>

        <p>
            <label for="user_phone"><?php esc_html_e( 'Phone', 'aws-sns-authentication' ); ?></label>
            <input type="tel" name="user_phone" id="user_phone" class="input input-intl-phone" value="" size="25" />
		</p>

        <?php
    }

    /**
     * Error handler for our custom phone number field
     *
     * @param \WP_Error $errors
     * @return \WP_Error
     */
    public function registration_errors_filter( $errors ) {
        if ( empty( $_REQUEST['user_phone'] ) ) {
            $errors->add( 'empty_phone', __( '<strong>ERROR</strong>: A phone number is required for account authentication.', 'aws-sns-authentication' ) );
        }

        if ( strlen( Helpers::sanitize_phone_number( $_REQUEST['user_phone'] ) ) < 8 ) {
            $errors->add( 'invalid_phone', __( '<strong>ERROR</strong>: Please enter a valid phone number.', 'aws-sns-authentication' ) );
        }

        return $errors;
    }

    /**
     * Save the phone field to the users profile
     *
     * @param integer $user_id
     * @return void
     */
    public function register_user_action( $user_id ) {
        add_user_meta( $user_id, 'phone', $_REQUEST['phone'] ?? '' );
    }

}
