<?php
/**
 * Hook listener abstract class file
 * 
 * @package AWS SNS Verification
 */

namespace SeattleWebCo\AWSSNSVerification\Abstracts;

use ReflectionMethod;

abstract class HookListener {
    
    /**
     * Automatically register methods in the WordPress hook system from
     * method naming conventions
     */
    final public function __construct() {
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

}
