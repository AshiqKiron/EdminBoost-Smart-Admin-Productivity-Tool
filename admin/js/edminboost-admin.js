( function () {
	'use strict';

	if ( typeof window.edminboostData === 'undefined' ) {
		return;
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		var root = document.querySelector( '.edminboost-wrap' );

		if ( ! root ) {
			return;
		}

		root.setAttribute( 'data-edminboost-ready', 'true' );
	} );
} )();
