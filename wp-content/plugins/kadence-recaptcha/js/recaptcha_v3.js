if ( typeof wc_checkout_params !== 'undefined' ) {
	jQuery( document.body ).on( 'checkout_error', function(){
		setTimeout(function(){
			jQuery('form').find( '.kt-g-recaptcha' ).each( function() {
				var item = jQuery( this );
				grecaptcha.execute( ktrecap.recaptcha_skey, { action: 'checkout' } ).then(function (token) { item.val( token ); });
			});
		}, 100);
	});
	jQuery( document.body ).on( 'updated_checkout', function(){
		setTimeout(function(){
			jQuery('form').find( '.kt-g-recaptcha' ).each( function() {
				var item = jQuery( this );
				grecaptcha.execute( ktrecap.recaptcha_skey, { action: 'checkout' } ).then(function (token) { item.val( token ); });
			});
		}, 100);
	});
	jQuery( document.body ).on( 'cfw_login_modal_open', function(){
		setTimeout(function(){
			jQuery('#cfw_login_modal_form').find( '.kt-g-recaptcha' ).each(function(){
				var item = jQuery( this );
				grecaptcha.execute( ktrecap.recaptcha_skey, { action: 'checkout' } ).then(function (token) { item.val( token ); });
			});
		}, 100);
	} );	
}

( function ( ) {
	'use strict';
	const wc_recaptcha = {};
	const CHECKOUT_STORE_KEY = 'wc/store/checkout';
	/**
	 * Get the order attribution data.
	 *
	 * Returns object full of `null`s if tracking is disabled or if sourcebuster.js is blocked.
	 *
	 * @returns {Object} Schema compatible object.
	 */
	wc_recaptcha.getTokenData = function() {
		const item = document.querySelector(".kt-g-recaptcha");
		if ( item?.value ) {
			wc_recaptcha.updateCheckoutBlockData( item.value );
		} else {
			grecaptcha.execute( ktrecap.recaptcha_skey, { action: 'checkout' } ).then(function (token) { 
				item.value = token;
				wc_recaptcha.updateCheckoutBlockData( token );
			});
		}
	}
	/**
	 * Update Checkout extension data.
	 *
	 * @param {Object} values Object containing field values.
	 */
	wc_recaptcha.updateCheckoutBlockData = function( values ) {
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
				wc_recaptcha.getTokenData();
			}, CHECKOUT_STORE_KEY );
			setTimeout( function() {
				const blockCheckout = document.querySelector("form.wc-block-components-form .kt-g-recaptcha");
				if ( blockCheckout && blockCheckout?.value ) {
					wc_recaptcha.updateCheckoutBlockData( blockCheckout.value );
				}
			}, 1000 );
		}
	};
	// Wait for DOMContentLoaded to make sure wp.data is in place, if applicable for the page.
	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", eventuallyInitializeForCheckoutBlock);
	} else {
		eventuallyInitializeForCheckoutBlock();
	}
}() );