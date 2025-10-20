( function ( $, obj ) {
	obj.init = function () {
		$( '.stellarwp-uplink-license-key-field' ).each( function () {
			var $el = $( this );
			var $field = $el.find( '.stellarwp-uplink__settings-field' );
			if ( '' === $field.val().trim() ) {
				$el.find( '.license-test-results' ).hide();
			}

			obj.validateKey( $el );
		} );

		$( document ).on( 'change', '.stellarwp-uplink-license-key-field', function () {
			const $el = $( this );
			obj.validateKey( $el );
		} );
		$( document ).on( 'click', '.stellarwp-uplink-license-key-field-clear', function ( e ) {
			var confirmed = confirm( 'Are you sure you want remove your license key?' );
			if ( ! confirmed ) {
				e.preventDefault();
			} else {
				const $el = $( this ).closest( '.stellarwp-uplink-license-key-field' );
				const $field = $el.find( '.stellarwp-uplink__settings-field' );
				$field.val( '' );
				$field.attr( 'type', 'text' );
				$field.removeAttr( 'disabled' );
				const $other_field = $el.find( '.stellarwp-uplink__settings-field-obfuscated' );
				$other_field.val( '' );
				$other_field.hide();
				$( this ).hide();
				const $submitButton = $( '<input>', {
					type: 'submit',
					class: 'button button-primary',
					text: 'Save Changes', // Text to display on the button
				} );
				const $submitWrap = $( '<p>', {
					class: 'submit',
				} );
				$submitWrap.append( $submitButton );
				$el.append( $submitWrap );
				$el.find( '.license-test-results' ).hide();

				e.preventDefault();
			}
		} );
	};

	obj.validateKey = function ( $el ) {
		const field = $el.find( '.stellarwp-uplink__settings-field' );
		const action = $el.data( 'action' );
		const slug = $el.data( 'plugin-slug' );
		const $validityMessage = $el.find( '.key-validity' );

		if ( '' === field.val().trim() ) {
			return;
		}

		$( $el ).find( '.license-test-results' ).show();
		$( $el ).find( '.tooltip' ).hide();
		$( $el ).find( '.ajax-loading-license' ).show();

		$validityMessage.hide();

		// Strip whitespace from key
		const licenseKey = field.val().trim();
		field.val( licenseKey );

		const data = {
			action: window[ `stellarwp_config_${ action }` ].action,
			slug,
			key: licenseKey,
			_wpnonce: $( $el ).find( '.wp-nonce' ).val(),
		};

		$.post( ajaxurl, data, function ( response ) {
			$validityMessage.show();
			$validityMessage.html( response.message );

			switch ( response.status ) {
				case 1:
					$validityMessage.addClass( 'valid-key' ).removeClass( 'invalid-key' );
					break;
				case 2:
					$validityMessage.addClass( 'valid-key service-msg' );
					break;
				default:
					$validityMessage.addClass( 'invalid-key' ).removeClass( 'valid-key' );
					break;
			}
		} )
			.fail( function ( error ) {
				$validityMessage.show();
				$validityMessage.html( error.message );
			} )
			.always( function () {
				$( $el ).find( '.ajax-loading-license' ).hide();
			} );
	};

	$( function () {
		obj.init();
	} );
} )( jQuery, {} );
