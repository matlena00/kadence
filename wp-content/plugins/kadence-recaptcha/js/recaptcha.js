// Write/rewrite form HTML structure and block/unblock send button.
function change_button ( value, address  ) {
	var a, ele;
	if ( value === null ) {
		jQuery('form').find( '.kt-g-recaptcha' ).each(function(){
			ele = jQuery( this ).closest('form').find( '[type=submit]' );
			if ( ele.length > 0 ) {
				ele.attr( 'disabled', '' );
			}
		});
	}
	if ( value === true ) {
		jQuery('form').find( '.kt-g-recaptcha' ).each(function(){
			ele = jQuery( this ).closest('form').find( '[type=submit]' ); 
			if ( ele.length > 0 ) {
				ele.removeAttr ('disabled');
			}
			//jQuery( this ).closest('form').append( '<input type="hidden" name="ktwpiprv" value="' + address + '">' );
		});
	}
}

// Ajax connection for verifying response through the secret key
var ktrespVerifyCallback = function( ktresp ) {
	change_button( true, '' );
	// jQuery.ajax({
	// 	url : ktrecap.ajax_url,
	// 	type : 'POST',
	// 	data : { 
	// 		'action' : 'kadence_verify_recaptcha',
	// 		'resp'	 : ktresp,
	// 	}, 
	// 	dataType : 'json',
	// 	success : function( ktrespr ) {
	// 		if ( ktrespr.data.result === 'OK' ) {
	// 			change_button( true, ktrespr.data.address );
	// 		} else {
	// 			console.log( ktrespr );
	// 		}
	// 	},
	// 	error : function( errorThrown ) {
	// 		console.log( errorThrown );
	// 	}
	// });
};
if ( typeof wc_checkout_params !== 'undefined' ) {
	jQuery( document.body ).on( 'checkout_error', function(){
		setTimeout(function(){ 
			change_button( null, null );
			var widget_id = jQuery( '#kt_g_recaptcha_checkout' ).attr( 'rcid' );
			grecaptcha.reset( widget_id );
		}, 100);
	});
	jQuery( document.body ).on( 'updated_checkout', function(){
		setTimeout(function(){
			change_button( null, null );
			if ( jQuery( '#kt_g_recaptcha_checkout' ).children().length > 0 ) {
				var widget_id = jQuery( '#kt_g_recaptcha_checkout' ).attr( 'rcid' );
				grecaptcha.reset( widget_id );
			} else {
				kt_reload_captcha_checkout();
			}
		}, 100);
	});
	jQuery( document.body ).on( 'cfw_login_modal_open', function(){
		setTimeout(function(){
			change_button( null, null );
			jQuery('#cfw_login_modal_form').find( '.kt-g-recaptcha' ).each(function(){
				var single_ktrecap = grecaptcha.render( jQuery( this ).attr('id'), {
					'sitekey' : ktrecap.recaptcha_skey,
					'theme' : ktrecap.recaptcha_theme,
					'type' : ktrecap.recaptcha_type,
					'size' : ktrecap.recaptcha_size,
					'tabindex' : 0,
					'callback' : ktrespVerifyCallback
				} );
				jQuery( this ).attr( 'rcid', single_ktrecap );
			});
		}, 100);
	} );
}
/**
 * Update Checkout extension data.
 *
 * @param {Object} values Object containing field values.
 */
var ktRecapUpdateCheckoutBlockData = function( values ) {
	// Update Checkout block data if available.
	if ( window.wp && window.wp.data && window.wp.data.dispatch && window.wc && window.wc.wcBlocksData ) {
		window.wp.data.dispatch( "wc/store/checkout" ).__internalSetExtensionData(
			'kadence/recaptcha',
			{ token: values }
		);
	}
}

// Global onload Method
var ktrecaploadCallback = function() {
	jQuery('form').find( '.kt-g-recaptcha' ).each(function(){
		var single_ktrecap = grecaptcha.render( jQuery( this ).attr('id'), {
			'sitekey' : ktrecap.recaptcha_skey,
			'theme' : ktrecap.recaptcha_theme,
			'type' : ktrecap.recaptcha_type,
			'size' : ktrecap.recaptcha_size,
			'tabindex' : 0,
			'callback' : jQuery( this ).attr('id') === 'kt_g_blocks_recaptcha_checkout' ? ktRecapUpdateCheckoutBlockData : ktrespVerifyCallback
		} );
		jQuery( this ).attr( 'rcid', single_ktrecap );
	});
};
// Global onload Method
var kt_reload_captcha_checkout = function() {
	jQuery('form').find( '#kt_g_recaptcha_checkout' ).each(function(){
		var single_ktrecap = grecaptcha.render( 'kt_g_recaptcha_checkout', {
			'sitekey' : ktrecap.recaptcha_skey,
			'theme' : ktrecap.recaptcha_theme,
			'type' : ktrecap.recaptcha_type,
			'size' : ktrecap.recaptcha_size,
			'tabindex' : 0,
			'callback' : ktrespVerifyCallback
		} );
		jQuery( this ).attr( 'rcid', single_ktrecap );
	});
};
(function ($) { change_button ( null, null); })(jQuery);


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
		const blockCheckout = document.querySelector("form.wc-block-components-form #kt_g_blocks_recaptcha_checkout");
		const widget_id = blockCheckout?.getAttribute('rcid');
		if ( widget_id ) {
			grecaptcha.reset( widget_id );
		} else {
			var single_ktrecap = grecaptcha.render( 'kt_g_blocks_recaptcha_checkout', {
				'sitekey' : ktrecap.recaptcha_skey,
				'theme' : ktrecap.recaptcha_theme,
				'type' : ktrecap.recaptcha_type,
				'size' : ktrecap.recaptcha_size,
				'tabindex' : 0,
				'callback' : ktRecapUpdateCheckoutBlockData
			} );
			blockCheckout.setAttribute( 'rcid', single_ktrecap );
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
				const blockCheckout = document.querySelector("form.wc-block-components-form #kt_g_blocks_recaptcha_checkout");
				if ( blockCheckout) {
					wc_recaptcha.getTokenData();
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