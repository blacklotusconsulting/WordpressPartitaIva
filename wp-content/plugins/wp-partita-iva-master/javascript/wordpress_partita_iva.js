/**
 * Wrapper function to safely use $
 */
function wp_partita_ivaWrapper( $ ) {
	var wp_partita_iva = {

		/**
		 * Main entry point
		 */
		init: function () {
			wp_partita_iva.prefix      = 'wp_partita_iva_';
			wp_partita_iva.templateURL = $( '#template-url' ).val();
			wp_partita_iva.ajaxPostURL = $( '#ajax-post-url' ).val();

			wp_partita_iva.registerEventHandlers();
		},

		/**
		 * Registers event handlers
		 */
		registerEventHandlers: function () {
			$( '#-container' ).children( 'a' ).click( wp_partita_iva.Handler );
		},

		/**
		 *  event handler
		 *
		 * @param object event
		 */
		Handler: function ( event ) {
			alert( $( this ).attr( 'href' ) );

			event.preventDefault();
		}
	}; // end wp_partita_iva

	$( document ).ready( wp_partita_iva.init );

} // end wp_partita_ivaWrapper()

wp_partita_ivaWrapper( jQuery );
