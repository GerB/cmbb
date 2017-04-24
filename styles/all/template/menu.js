 // MOBILE MENU
jQuery( document ).ready( function() {
	var hWindow = jQuery( window ).height();
	jQuery( "#menu-trigger" ).click( function() {
		jQuery( "html" ).toggleClass( "menu-active" );
		jQuery( "#main-navigation" ).css( "height", hWindow );
	} );
	jQuery( "#content-wrapper" ).click( function() {
		jQuery( "html" ).removeClass( "menu-active" );
	} );
	jQuery( window ).resize( function() {
        var wWindow = jQuery( window ).width();
        if (wWindow > 900) {
            var hSet = 'auto';            
        }
        else {
            hSet = jQuery( window ).height();
        }
        jQuery( "#main-navigation" ).css( "height", hSet );
	} );

} );
jQuery( window ).bind( "orientationchange", function( event ) {
	jQuery( "#menu-trigger" ).click( function() {
		jQuery( "#main-navigation" ).css( "height", screen.height );
	} );
} );

