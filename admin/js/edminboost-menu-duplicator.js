/**
 * Append a Duplicate menu button on the nav menus screen.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		if ( typeof edminboostMenuDuplicator === 'undefined' ) {
			return;
		}

		var wrap = document.querySelector( '.nav-tab-wrapper' );
		if ( ! wrap ) {
			return;
		}

		var link = document.createElement( 'a' );
		link.className = 'button';
		link.style.marginLeft = '8px';
		link.href = edminboostMenuDuplicator.url;
		link.textContent = edminboostMenuDuplicator.label;
		wrap.appendChild( link );
	} );
}() );
