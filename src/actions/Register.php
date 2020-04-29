<?php
/**
 * Register action class file
 * 
 * @package AWS SNS Authentication
 */

namespace SeattleWebCo\AWSSNSAuthentication\Actions;

use SeattleWebCo\AWSSNSAuthentication\Helpers;
use SeattleWebCo\AWSSNSAuthentication\Actions\Action;

use Aws\Sns\SnsClient; 
use Aws\Exception\AwsException;

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
     * @param array $meta
     * @param \WP_User $user 
     * @param boolean $update
     * @return void
     */
    public function insert_user_meta_filter( $meta, $user, $update ) {
        if ( ! $update ) {
            $meta['phone'] = $_REQUEST['user_phone'] ?? '';
            $meta['phone_e164'] = $_REQUEST['user_phone_e164'] ?? '';
        }

        return $meta;
    }

    /**
     * Send a account authentication SMS to the user
     *
     * @param integer $user_id
     * @return void
     */
    public function user_register_action( $user_id ) {
        $args = [ 
            'version'   => constant( 'AWS_SNS_AUTH_VER' ) ?? 'latest',
            'region'    => constant( 'AWS_SNS_AUTH_REGION' ) ?? null
        ];

        if ( defined( 'AWS_SNS_AUTH_KEY' ) && defined( 'AWS_SNS_AUTH_SECRET' ) ) {
            $args['credentials'] = [
                'key'       =>  constant( 'AWS_SNS_AUTH_KEY' ),
                'secret'    => constant( 'AWS_SNS_AUTH_SECRET' )
            ];
        }

        $SnSclient = new SnsClient( $args );
        
        $code    = Helpers::generate_random_authentication_code();
        $message = Helpers::get_authentication_sms_message( $user_id, $code );
        $phone   = get_user_meta( $user_id, 'phone_e164', true );
        
        try {
            $result = $SnSclient->publish( [
                'Message'       => $message,
                'PhoneNumber'   => $phone,
            ] );
        } catch (AwsException $e) {
            error_log( $e->getMessage() );
        } 
    }

    public function registration_redirect_filter( $redirect ) {
        $redirect = add_query_arg( [ 'action' => 'checksms' ], remove_query_arg( array_keys( $_GET ?? [] ), $redirect ) );
        
        return $redirect;
    }

    public function login_form_checksms_filter() {}

    public function login_form_checksms_action() {
        ob_start();
    }

    public function login_footer_action() {
        if ( isset ( $_GET['action'] ) && $_GET['action'] === 'checksms' ) {
            $content = ob_get_clean();

            $domDocument = new \DOMDocument();		
            $domContent = $domDocument->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES' ) );		
            
            $loginform = $domDocument->getElementById( 'loginform' );

            $form = $domDocument->createElement( 'form' );
            $form->setAttribute( 'action', esc_url( site_url( 'wp-login.php', 'login_post' ) ) );
            $form->setAttribute( 'method', 'post' );
            $form->setAttribute( 'id', 'loginform' );

            $label = $domDocument->createElement( 'label', __( 'Verification code', 'aws-sns-authentication' ) );
            $label->setAttribute( 'for', 'authentication_code' );

            $p = $domDocument->createElement( 'p' );
            $p->setAttribute( 'class', 'aws-sns-auth-field-container' );
            $p->appendChild( $label );

            $length = Helpers::get_authentication_code_length();

            $field = $domDocument->createElement( 'input' );
            $field->setAttribute( 'name', 'authentication_code' );
            $field->setAttribute( 'class', 'input aws-sns-auth-field' );
            $field->setAttribute( 'type', 'text' );
            $field->setAttribute( 'autofocus', 'autofocus' );
            $field->setAttribute( 'maxlength', $length );
            $field->setAttribute( 'inputmode', 'numeric' );
            $field->setAttribute( 'pattern', '[0-9]*' );
            $field->setAttribute( 'autocomplete', 'one-time-code' );

            $p->appendChild( $field );

            $form->appendChild( $p );

            $nonce = $domDocument->createElement( 'input' );
            $nonce->setAttribute( 'type', 'hidden' );
            $nonce->setAttribute( 'name', '_wpnonce' );
            $nonce->setAttribute( 'value', wp_create_nonce( 'checksms' ) );

            $form->appendChild( $nonce );

            $submit = $domDocument->createElement( 'input' );
            $submit->setAttribute( 'type', 'submit' );
            $submit->setAttribute( 'class', 'button button-primary button-large' );

            $p = $domDocument->createElement( 'p' );
            $p->setAttribute( 'class', 'submit' );
            $p->appendChild( $submit );

            $form->appendChild( $p );

            $loginform->parentNode->replaceChild( $form, $loginform );
            echo $domDocument->saveHTML();

            if ( false === $domContent ) {			
                return $content;		
            }

            return '';
        }
        
        echo ob_get_clean();
    }

}
