if ( typeof wc_checkout_params !== 'undefined' ) {
	jQuery( document.body ).on( 'checkout_error', function(){
		setTimeout(function(){
            if(typeof turnstile != "undefined") {
                jQuery('form').find( '.cf-turnstile' ).each( function(e) {
					const id = e.getAttribute('id');

					if( id ) {
						turnstile.render('#' + id)
					} else {
						turnstile.render('.cf-turnstile')
					}
                });
            }
		}, 100);
	});
	jQuery( document.body ).on( 'updated_checkout', function(){
		setTimeout(function(){
            if(typeof turnstile != "undefined") {
                jQuery('form').find( '.cf-turnstile' ).each( function(key, e) {
					const id = e.getAttribute('id');

					if( id ) {
						turnstile.render('#' + id)
					} else {
						turnstile.render('.cf-turnstile')
					}
                });
            }
		}, 100);
	});
	jQuery( document.body ).on( 'cfw_login_modal_open', function(){
		setTimeout(function(){
			if(typeof turnstile != "undefined") {
				jQuery('#cfw_login_modal_form').find( '.cf-turnstile' ).each( function(key, e) {
					const id = e.getAttribute('id');

					if( id ) {
						turnstile.render('#' + id)
					} else {
						turnstile.render('.cf-turnstile')
					}
                });
			}
		}, 100);
	} );
}  
( function ( ) {
	'use strict';
	const wc_turnstile = {};
	const CHECKOUT_STORE_KEY = 'wc/store/checkout';
	/**
	 * Get the order attribution data.
	 *
	 * Returns object full of `null`s if tracking is disabled or if sourcebuster.js is blocked.
	 *
	 * @returns {Object} Schema compatible object.
	 */
	wc_turnstile.getTokenData = function() {
		const turnstileItem = document.querySelector("form.wc-block-components-form .cf-checkout-turnstile");
		if ( turnstile && turnstileItem ) {
			turnstile.render( turnstileItem, {
				sitekey: turnstileItem.dataset.sitekey,
				callback: function( data ) {
					wc_turnstile.updateCheckoutBlockData( data );
				},
			});
		}
	}
	/**
	 * Update Checkout extension data.
	 *
	 * @param {Object} values Object containing field values.
	 */
	wc_turnstile.updateCheckoutBlockData = function( values ) {
		// Update Checkout block data if available.
		if ( window.wp && window.wp.data && window.wp.data.dispatch && window.wc && window.wc.wcBlocksData ) {
			window.wp.data.dispatch( CHECKOUT_STORE_KEY ).__internalSetExtensionData(
				'kadence/recaptcha',
				{ token: values }
			);
		}
	}
	function eventuallyInitializeForCheckoutBlock() {
		if (
			window.wp && window.wp.data && typeof window.wp.data.subscribe === 'function'
		) {
			// Update checkout block data once more if the checkout store was loaded after this script.
			const unsubscribe = window.wp.data.subscribe( function () {
				unsubscribe();
				wc_turnstile.getTokenData();
			}, CHECKOUT_STORE_KEY );
		}
	};
	// Wait for DOMContentLoaded to make sure wp.data is in place, if applicable for the page.
	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", eventuallyInitializeForCheckoutBlock);
	} else {
		eventuallyInitializeForCheckoutBlock();
	}
}() );
