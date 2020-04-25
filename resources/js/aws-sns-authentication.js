import intlTelInput from 'intl-tel-input';

( function( intlTelInput ) {

    let fields = document.querySelectorAll( '.input-intl-phone' );

    console.log( fields.length );

    if ( fields.length ) {
        fields.forEach( field => {
            intlTelInput( field, {

            } );
        } );
    }

    

} )( intlTelInput );

