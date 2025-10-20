/*global kadence_adv_pages_params */
( function ( $, window ) {
	$( '.kadence-status-toggle' ).on( 'click', function ( event ) {
		event.preventDefault();
		var $button = $( this );
		$button.find( '.spinner' ).addClass( 'is-active' );
		$.ajax( {
			type: 'POST',
			url: kadence_adv_pages_params.ajax_url,
			data: {
				action: 'kadence_adv_pages_change_status',
				post_id: $button.data( 'post-id' ),
				post_status: $button.data( 'post-status' ),
				security: kadence_adv_pages_params.ajax_nonce,
			},
			dataType: 'json',
			success: function ( response ) {
				$button.find( '.spinner' ).removeClass( 'is-active' );
				if ( response && response.success ) {
					if ( 'publish' === $button.data( 'post-status' ) ) {
						$button.removeClass( 'kadence-status-publish' );
						$button.addClass( 'kadence-status-draft' );
						$button.data( 'post-status', 'draft' );
						$button.find( '.kadence-status-label' ).html( kadence_adv_pages_params.draft );
					} else {
						$button.removeClass( 'kadence-status-draft' );
						$button.addClass( 'kadence-status-publish' );
						$button.data( 'post-status', 'publish' );
						$button.find( '.kadence-status-label' ).html( kadence_adv_pages_params.publish );
					}
					$button.closest( 'tr.type-kadence_adv_page' ).find( '.column-title .post-state' ).hide();
				} else {
					alert( 'Failed to change post status, please reload and try again' );
					window.console.log( response );
				}
			},
		} ).fail( function ( response ) {
			$button.find( '.spinner' ).removeClass( 'is-active' );
			alert( 'Failed to change post status, please reload and try again' );
			window.console.log( response );
		} );
	} );
} )( jQuery, window );
