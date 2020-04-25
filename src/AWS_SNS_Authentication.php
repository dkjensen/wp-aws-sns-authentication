<?php
/**
 * Main class file
 * 
 * @package AWS SNS Authentication
 */

namespace SeattleWebCo\AWSSNSAuthentication;

use SeattleWebCo\AWSSNSAuthentication\Actions\Register;

class AWS_SNS_Authentication {

    /**
     * Contains instances of classes extending Action
     *
     * @var array
     */
    public $actions = [];

    /**
     * Plugin init
     */
    public function __construct() {
        $this->actions = apply_filters( 'aws_sns_authentication_actions', [
            new Register
        ] );
    }

}
