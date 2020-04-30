<?php
/**
 * Main class file
 * 
 * @package AWS SNS Verification
 */

namespace SeattleWebCo\AWSSNSVerification;

use SeattleWebCo\AWSSNSVerification\Upgrade;
use SeattleWebCo\AWSSNSVerification\Actions\Register;

class AWS_SNS_Verification {

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
        $this->actions = apply_filters( 'aws_sns_verification_actions', [
            new Register
        ] );

        new Upgrade;
    }

}
