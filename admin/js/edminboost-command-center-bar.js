/**
 * Command Center live admin bar — AJAX slide-out drawer.
 */
( function () {
	'use strict';

	if ( ! window.edminboostCcBar || ! window.edminboostCcBar.drawerItems ) {
		return;
	}

	var config = window.edminboostCcBar;
	var drawer = document.getElementById( 'edminboost-cc-drawer' );

	if ( ! drawer ) {
		return;
	}

	var panel      = drawer.querySelector( '.edminboost-cc-drawer__panel' );
	var backdrop   = drawer.querySelector( '.edminboost-cc-drawer__backdrop' );
	var closeBtn   = drawer.querySelector( '.edminboost-cc-drawer__close' );
	var openFull   = drawer.querySelector( '.edminboost-cc-drawer__open-full' );
	var titleEl    = drawer.querySelector( '.edminboost-cc-drawer__title' );
	var loadingEl  = drawer.querySelector( '.edminboost-cc-drawer__loading' );
	var iframe     = document.getElementById( 'edminboost-cc-drawer-iframe' );
	var animationMs = parseInt( config.animationMs, 10 ) || 300;
	var activeItem  = null;
	var closeTimer  = null;

	function openDrawer( item ) {
		if ( ! item || ! item.frameUrl ) {
			return;
		}

		if ( closeTimer ) {
			clearTimeout( closeTimer );
			closeTimer = null;
		}

		activeItem = item;
		drawer.hidden = false;
		drawer.classList.add( 'is-open' );
		document.body.classList.add( 'edminboost-cc-drawer-active' );

		if ( titleEl ) {
			titleEl.textContent = item.label || item.slug || '';
		}

		if ( openFull ) {
			openFull.href = item.openUrl || item.frameUrl;
		}

		if ( loadingEl ) {
			loadingEl.hidden = false;
		}

		if ( iframe ) {
			iframe.onload = function () {
				if ( loadingEl ) {
					loadingEl.hidden = true;
				}
			};
			iframe.src = item.frameUrl;
		}
	}

	function closeDrawer() {
		drawer.classList.remove( 'is-open' );
		document.body.classList.remove( 'edminboost-cc-drawer-active' );

		closeTimer = window.setTimeout( function () {
			drawer.hidden = true;
			if ( iframe ) {
				iframe.onload = null;
				iframe.src = 'about:blank';
			}
			if ( loadingEl ) {
				loadingEl.hidden = true;
			}
			activeItem = null;
			closeTimer = null;
		}, animationMs );
	}

	document.addEventListener( 'click', function ( event ) {
		var trigger = event.target.closest( '#wpadminbar .edminboost-cc-bar-drawer-trigger > .ab-item' );

		if ( ! trigger ) {
			return;
		}

		event.preventDefault();

		var node = trigger.parentElement;
		var nodeId = node ? node.id : '';

		if ( ! nodeId || ! config.drawerItems[ nodeId ] ) {
			return;
		}

		openDrawer( config.drawerItems[ nodeId ] );
	} );

	if ( backdrop ) {
		backdrop.addEventListener( 'click', closeDrawer );
	}

	if ( closeBtn ) {
		closeBtn.addEventListener( 'click', closeDrawer );
	}

	document.addEventListener( 'keydown', function ( event ) {
		if ( event.key === 'Escape' && drawer.classList.contains( 'is-open' ) ) {
			closeDrawer();
		}
	} );
} )();
