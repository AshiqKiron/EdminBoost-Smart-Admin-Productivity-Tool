/**
 * Command Center live admin bar — AJAX slide-out drawer.
 */
( function () {
	'use strict';

	if ( ! window.edminboostCcBar ) {
		return;
	}

	var config        = window.edminboostCcBar;
	var animationMs   = parseInt( config.animationMs, 10 ) || 300;
	var activeItem    = null;
	var closeTimer    = null;
	var frameCache    = {};

	function getDrawer() {
		return document.getElementById( 'edminboost-cc-drawer' );
	}

	function getDrawerBody() {
		var drawer = getDrawer();

		return drawer ? drawer.querySelector( '.edminboost-cc-drawer__body' ) : null;
	}

	function normalizeUrl( url ) {
		if ( ! url || url === 'about:blank' ) {
			return '';
		}

		try {
			return new URL( url, window.location.href ).href;
		} catch ( error ) {
			return url;
		}
	}

	function resolveDrawerNodeId( nodeId ) {
		if ( ! nodeId ) {
			return '';
		}

		if ( config.drawerItems[ nodeId ] ) {
			return nodeId;
		}

		if ( 0 === nodeId.indexOf( 'wp-admin-bar-' ) ) {
			var stripped = nodeId.substring( 13 );

			if ( config.drawerItems[ stripped ] ) {
				return stripped;
			}
		}

		return nodeId;
	}

	function markLoadedIfComplete( entry ) {
		if ( ! normalizeUrl( entry.iframe.src ) ) {
			return false;
		}

		try {
			var doc = entry.iframe.contentDocument;

			if ( doc && doc.readyState === 'complete' ) {
				entry.loaded = true;
				entry.loading = false;
				return true;
			}
		} catch ( error ) {
			// Ignore cross-origin access errors.
		}

		return false;
	}

	function bindFrameLoadHandler( entry, frameUrl, loadingEl ) {
		if ( entry.loadBound ) {
			return;
		}

		entry.loadBound = true;

		entry.iframe.addEventListener(
			'load',
			function () {
				entry.loaded = true;
				entry.loading = false;

				if (
					activeItem
					&& normalizeUrl( activeItem.frameUrl ) === normalizeUrl( frameUrl )
				) {
					setLoadingVisible( loadingEl, false );
				}
			},
			{ once: true }
		);
	}

	function findExistingFrame( frameUrl ) {
		var body = getDrawerBody();
		var key  = normalizeUrl( frameUrl );

		if ( ! body || ! key ) {
			return null;
		}

		var frames = body.querySelectorAll( '.edminboost-cc-drawer__iframe[data-edminboost-frame-url]' );

		for ( var i = 0; i < frames.length; i++ ) {
			if ( normalizeUrl( frames[ i ].getAttribute( 'data-edminboost-frame-url' ) ) === key ) {
				return frames[ i ];
			}
		}

		return null;
	}

	function getFrameEntry( frameUrl ) {
		var key = normalizeUrl( frameUrl );

		if ( ! key ) {
			return null;
		}

		if ( frameCache[ key ] ) {
			return frameCache[ key ];
		}

		var iframe = findExistingFrame( frameUrl );
		var body   = getDrawerBody();

		if ( ! iframe && body ) {
			iframe = document.createElement( 'iframe' );
			iframe.className = 'edminboost-cc-drawer__iframe';
			iframe.hidden = true;
			iframe.title = config.iframeTitle || 'Admin page preview';
			iframe.setAttribute( 'data-edminboost-frame-url', frameUrl );
			iframe.src = 'about:blank';
			body.appendChild( iframe );
		}

		if ( ! iframe ) {
			return null;
		}

		var entry = {
			iframe: iframe,
			loaded: false,
			loading: false
		};

		if ( markLoadedIfComplete( entry ) ) {
			frameCache[ key ] = entry;
			return entry;
		}

		if ( iframe.src && iframe.src !== 'about:blank' ) {
			entry.loading = true;
			bindFrameLoadHandler( entry, frameUrl, null );
		}

		frameCache[ key ] = entry;

		return entry;
	}

	function bootstrapExistingFrames() {
		var body = getDrawerBody();

		if ( ! body ) {
			return;
		}

		var frames = body.querySelectorAll( '.edminboost-cc-drawer__iframe[data-edminboost-frame-url]' );

		for ( var i = 0; i < frames.length; i++ ) {
			var frameUrl = frames[ i ].getAttribute( 'data-edminboost-frame-url' );

			if ( frameUrl ) {
				getFrameEntry( frameUrl );
			}
		}
	}

	function hideAllFrames() {
		Object.keys( frameCache ).forEach( function ( key ) {
			frameCache[ key ].iframe.hidden = true;
			frameCache[ key ].iframe.removeAttribute( 'id' );
		} );
	}

	function setLoadingVisible( loadingEl, visible ) {
		if ( loadingEl ) {
			loadingEl.hidden = ! visible;
		}
	}

	function loadFrameEntry( entry, frameUrl, loadingEl ) {
		if ( entry.loaded || markLoadedIfComplete( entry ) ) {
			setLoadingVisible( loadingEl, false );
			return;
		}

		if ( entry.loading ) {
			setLoadingVisible( loadingEl, true );
			bindFrameLoadHandler( entry, frameUrl, loadingEl );
			return;
		}

		entry.loading = true;
		setLoadingVisible( loadingEl, true );
		bindFrameLoadHandler( entry, frameUrl, loadingEl );

		if ( normalizeUrl( entry.iframe.src ) !== normalizeUrl( frameUrl ) ) {
			entry.iframe.src = frameUrl;
		}
	}

	function showFrameForItem( item, loadingEl ) {
		var entry = getFrameEntry( item.frameUrl );

		if ( ! entry ) {
			return;
		}

		hideAllFrames();
		entry.iframe.hidden = false;
		entry.iframe.id = 'edminboost-cc-drawer-iframe';

		if ( entry.loaded || markLoadedIfComplete( entry ) ) {
			setLoadingVisible( loadingEl, false );
			return;
		}

		loadFrameEntry( entry, item.frameUrl, loadingEl );
	}

	function openDrawer( item ) {
		var drawer = getDrawer();

		if ( ! drawer || ! item || ! item.frameUrl ) {
			return;
		}

		var openFull  = drawer.querySelector( '.edminboost-cc-drawer__open-full' );
		var titleEl   = drawer.querySelector( '.edminboost-cc-drawer__title' );
		var loadingEl = drawer.querySelector( '.edminboost-cc-drawer__loading' );

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

		showFrameForItem( item, loadingEl );
	}

	function closeDrawer() {
		var drawer = getDrawer();

		if ( ! drawer ) {
			return;
		}

		var loadingEl = drawer.querySelector( '.edminboost-cc-drawer__loading' );

		drawer.classList.remove( 'is-open' );
		document.body.classList.remove( 'edminboost-cc-drawer-active' );

		closeTimer = window.setTimeout( function () {
			drawer.hidden = true;
			hideAllFrames();
			setLoadingVisible( loadingEl, false );
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
		var nodeId = resolveDrawerNodeId( node ? node.id : '' );

		if ( ! nodeId || ! config.drawerItems || ! config.drawerItems[ nodeId ] ) {
			return;
		}

		openDrawer( config.drawerItems[ nodeId ] );
	} );

	document.addEventListener( 'click', function ( event ) {
		var drawer = getDrawer();

		if ( ! drawer ) {
			return;
		}

		if ( event.target.closest( '.edminboost-cc-drawer__backdrop' ) ) {
			closeDrawer();
			return;
		}

		if ( event.target.closest( '.edminboost-cc-drawer__close' ) ) {
			closeDrawer();
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		var drawer = getDrawer();

		if ( event.key === 'Escape' && drawer && drawer.classList.contains( 'is-open' ) ) {
			closeDrawer();
		}
	} );

	bootstrapExistingFrames();

	window.edminboostCcOpenDrawer = openDrawer;
} )();
