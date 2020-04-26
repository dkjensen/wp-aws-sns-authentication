<?php
/**
 * Abstract action class file
 * 
 * @package AWS SNS Authentication
 */

namespace SeattleWebCo\AWSSNSAuthentication\Actions;

use ReflectionMethod;

abstract class Action {

    /**
     * Add action and filter hooks based on method name
     *
     * @return void
     */
    final public function __construct() {
        add_action( 'login_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

        $methods = get_class_methods( $this );

        foreach ( $methods as $name ) {
            $reflection = new ReflectionMethod( $this, $name );

            if ( substr( $name, -7 ) === '_action' ) {
                add_action( substr( $name, 0, strpos( $name, '_action' ) ), [ $this, $name ], 10, $reflection->getNumberOfRequiredParameters() );
            }

            if ( substr( $name, -7 ) === '_filter' ) {
                add_filter( substr( $name, 0, strpos( $name, '_filter' ) ), [ $this, $name ], 10, $reflection->getNumberOfRequiredParameters() );
            }
        }
    }

    final public function enqueue_scripts() {
        wp_enqueue_style( 'aws-sns-authentication', AWS_SNS_AUTHENTICATION_URL . 'assets/css/aws-sns-authentication.css' );
        wp_enqueue_script( 'aws-sns-authentication', AWS_SNS_AUTHENTICATION_URL . 'assets/js/aws-sns-authentication.js', [], null, true );
    }
}
