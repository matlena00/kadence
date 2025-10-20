/* global kadenceProInfiniteConfig */
/**
 * File post-infinite-scroll.js.
 * Gets single post infinite scroll working.
 */

(function() {
	'use strict';
	window.kadenceProInfiniteEvents = {
		// Initiate scroll when the DOM loads.
		init: function() {
			var infScrollContainer = document.querySelector( '.content-wrap[data-infinite-scroll]' );
			// No point if no container.
			if ( ! infScrollContainer.dataset || ! infScrollContainer.dataset.infiniteScroll ) {
				return;
			}
			let parsedData = JSON.parse( infScrollContainer.dataset.infiniteScroll );
			var infScroll = new InfiniteScroll( '.content-wrap[data-infinite-scroll]', {
				path: parsedData.path,
				append: parsedData.append,
				hideNav:parsedData.hideNav,
				status: parsedData.status,
			} );
			infScroll.on( 'append', function( body, path, items, response ) {
				const event = new Event('kadenceJSInitReload');
				document.dispatchEvent( event );
			});
		}
	}
	if ( 'loading' === document.readyState ) {
		// The DOM has not yet been loaded.
		document.addEventListener( 'DOMContentLoaded', window.kadenceProInfiniteEvents.init );
	} else {
		// The DOM has already been loaded.
		window.kadenceProInfiniteEvents.init();
	}
})();