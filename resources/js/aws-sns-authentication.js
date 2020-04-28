import intlTelInput from 'intl-tel-input';
import '../../node_modules/intl-tel-input/build/js/utils.js';

( function( intlTelInput ) {

    let fields = document.querySelectorAll( '.input-intl-phone' );

    if ( fields.length ) {
        fields.forEach( field => {
            intlTelInput( field, {
                hiddenInput: 'user_phone_e164',
                utilsScript: '../../node_modules/intl-tel-input/build/js/utils.js'
            } );
        } );
    }

    

} )( intlTelInput );

