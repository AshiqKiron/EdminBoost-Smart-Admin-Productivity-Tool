( function () {
	'use strict';

	if ( typeof window.edminboostData === 'undefined' ) {
		return;
	}

	var loadCommandCenterPageRef = null;
	var syncPresetCatalogFn      = null;

	function resolveThemePreviewMode( mode ) {
		if ( 'auto' !== mode ) {
			return 'dark' === mode ? 'dark' : 'light';
		}

		if ( window.matchMedia && window.matchMedia( '(prefers-color-scheme: dark)' ).matches ) {
			return 'dark';
		}

		return 'light';
	}

	function getThemePreviewColors( themePresets, presetId, mode, customColors ) {
		if ( 'custom' === presetId && customColors ) {
			return customColors;
		}

		var config        = themePresets[ presetId ] || themePresets.default || {};
		var effectiveMode = resolveThemePreviewMode( mode || 'light' );

		if ( config.colorsByMode && config.colorsByMode[ effectiveMode ] ) {
			return config.colorsByMode[ effectiveMode ];
		}

		return config.colors || {};
	}

	function applyThemePreviewColorVars( target, colors ) {
		if ( ! target || ! colors ) {
			return;
		}

		target.style.setProperty( '--eb-op-accent', colors.accent || '#2271b1' );
		target.style.setProperty( '--eb-op-surface', colors.surface || '#ffffff' );
		target.style.setProperty( '--eb-op-text', colors.text || '#1d2327' );
		target.style.setProperty( '--eb-op-top', colors.topbar || '#1d2327' );
		target.style.setProperty( '--eb-op-sidebar', colors.sidebar || '#1d2327' );
		target.style.setProperty( '--eb-op-content', colors.content || '#f0f0f1' );
	}

	function getOverviewPreviewStrings() {
		var strings = edminboostData.strings || {};

		return {
			wordpressLogo: strings.previewWordPressLogo || 'WordPress',
			profile: strings.previewProfile || 'My account',
		};
	}

	function createTopBarPreviewTip( title, iconClass ) {
		var tip = document.createElement( 'span' );
		tip.className = 'edminboost-overview-topbar-preview__tip';

		if ( title ) {
			tip.setAttribute( 'title', title );
		}

		var icon = document.createElement( 'span' );
		icon.className = 'dashicons ' + ( iconClass || 'dashicons-admin-generic' );
		icon.setAttribute( 'aria-hidden', 'true' );
		tip.appendChild( icon );

		return tip;
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		var root = document.querySelector( '.edminboost-wrap' );

		if ( ! root ) {
			return;
		}

		initCommandCenterNav();
		initPickerCloseOnScroll();
		reinitEdminboostPage( root );
		primeCommandCenterHistory();
	} );

	function reinitEdminboostPage( root ) {
		if ( ! root ) {
			return;
		}

		root.setAttribute( 'data-edminboost-ready', 'true' );

		initMapper( root );
		initMenuStudio( root );
		initBehavior( root );
		initTheme( root );
		initLayoutPresetPicker( root );
		initDashboardOverview( root );
		initPresets( root );
		initSetupWizard( root );
		initCommandCenterForms( root );
		initSettingsForm( root );
		initBackupSettings( root );
	}

	function primeCommandCenterHistory() {
		if ( ! window.history || ! window.history.replaceState || ! edminboostData.currentPage ) {
			return;
		}

		if ( window.history.state && window.history.state.edminboostPage ) {
			return;
		}

		window.history.replaceState(
			{
				edminboostPage: edminboostData.currentPage
			},
			'',
			window.location.href
		);
	}

	function initPickerCloseOnScroll() {
		if ( window.edminboostPickerCloseOnScrollInit ) {
			return;
		}

		window.edminboostPickerCloseOnScrollInit = true;

		var pickerListSelector = '.edminboost-layout-preset-picker__list, .edminboost-theme-preset-picker__list, .edminboost-overview-topbar-links-picker__list';

		function isPickerListScrollTarget( target ) {
			if ( ! target || target.nodeType !== 1 ) {
				return false;
			}

			return !! (
				target.matches( pickerListSelector )
				|| target.closest( pickerListSelector )
			);
		}

		function closeOpenPickerLists() {
			document.querySelectorAll( pickerListSelector ).forEach( function ( list ) {
				if ( list.hidden ) {
					return;
				}

				list.hidden = true;

				var listId = list.id;
				if ( ! listId ) {
					return;
				}

				document.querySelectorAll( '[aria-controls="' + listId + '"]' ).forEach( function ( toggle ) {
					toggle.setAttribute( 'aria-expanded', 'false' );
				} );
			} );
		}

		document.addEventListener( 'scroll', function ( event ) {
			if ( isPickerListScrollTarget( event.target ) ) {
				return;
			}

			closeOpenPickerLists();
		}, { passive: true, capture: true } );
	}

	function initCommandCenterNav() {
		var navConfig = edminboostData.ccNav;
		var strings   = edminboostData.strings || {};
		var navRequest = null;

		if ( ! navConfig ) {
			return;
		}

		document.addEventListener( 'click', function ( event ) {
			var link = event.target.closest( '.edminboost-cc-nav__link' );

			if ( ! link ) {
				return;
			}

			var wrap = document.querySelector( '.edminboost-wrap' );

			if ( ! wrap || ! wrap.contains( link ) ) {
				return;
			}

			if ( event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.target === '_blank' ) {
				return;
			}

			if ( link.classList.contains( 'is-active' ) ) {
				event.preventDefault();
				return;
			}

			event.preventDefault();
			loadCommandCenterPage( link.getAttribute( 'data-edminboost-page' ) || '', link.href, { push: true } );
		} );

		window.addEventListener( 'popstate', function ( event ) {
			if ( ! event.state || ! event.state.edminboostPage ) {
				return;
			}

			loadCommandCenterPage( event.state.edminboostPage, window.location.href, { push: false } );
		} );

		function loadCommandCenterPage( page, url, options ) {
			options = options || {};

			if ( ! page ) {
				return;
			}

			var currentWrap = document.querySelector( '.edminboost-wrap' );
			var nav         = currentWrap ? currentWrap.querySelector( '.edminboost-cc-nav' ) : null;

			if ( navRequest ) {
				navRequest.abort();
			}

			if ( nav ) {
				nav.classList.add( 'is-loading' );
				nav.setAttribute( 'aria-busy', 'true' );
			}

			if ( currentWrap ) {
				currentWrap.classList.add( 'is-cc-loading' );
			}

			var controller = new window.AbortController();
			navRequest = controller;

			var formData = new window.FormData();
			formData.append( 'action', navConfig.action );
			formData.append( 'nonce', navConfig.nonce );
			formData.append( 'page', page );

			window.fetch( navConfig.ajaxUrl, {
				method: 'POST',
				body: formData,
				credentials: 'same-origin',
				signal: controller.signal
			} )
				.then( function ( response ) {
					return response.json();
				} )
				.then( function ( payload ) {
					if ( ! payload.success ) {
						var errorMessage = payload.data && payload.data.message
							? payload.data.message
							: strings.pageLoadFailed;

						throw new Error( errorMessage );
					}

					var data = payload.data || {};
					var parser = new window.DOMParser();
					var doc = parser.parseFromString( data.html || '', 'text/html' );
					var newWrap = doc.querySelector( '.edminboost-wrap' );

					if ( ! newWrap || ! currentWrap ) {
						throw new Error( strings.pageLoadFailed );
					}

					currentWrap.replaceWith( newWrap );
					edminboostData.currentPage = data.page || page;

					if ( data.documentTitle ) {
						document.title = data.documentTitle;
					} else if ( data.title ) {
						document.title = data.title;
					}

					if ( options.push && url && window.history && window.history.pushState ) {
						window.history.pushState(
							{
								edminboostPage: data.page || page
							},
							'',
							url
						);
					}

					reinitEdminboostPage( newWrap );

					var heading = newWrap.querySelector( 'h1' );
					if ( heading ) {
						heading.setAttribute( 'tabindex', '-1' );
						heading.focus( { preventScroll: true } );
					}

					newWrap.scrollIntoView( { behavior: 'smooth', block: 'start' } );
				} )
				.catch( function ( error ) {
					if ( error && error.name === 'AbortError' ) {
						return;
					}

					window.alert( error.message || strings.pageLoadFailed );

					if ( url ) {
						window.location.assign( url );
					}
				} )
				.finally( function () {
					if ( navRequest === controller ) {
						navRequest = null;
					}

					var activeWrap = document.querySelector( '.edminboost-wrap' );
					var activeNav  = activeWrap ? activeWrap.querySelector( '.edminboost-cc-nav' ) : null;

					if ( activeNav ) {
						activeNav.classList.remove( 'is-loading' );
						activeNav.removeAttribute( 'aria-busy' );
					}

					if ( activeWrap ) {
						activeWrap.classList.remove( 'is-cc-loading' );
					}
				} );
		}

		loadCommandCenterPageRef = loadCommandCenterPage;
	}

	function initMapper( root ) {
		var form         = document.getElementById( 'edminboost-mapper-form' );
		var canvas       = document.getElementById( 'edminboost-topbar-items' );
		var canvasShell  = document.getElementById( 'edminboost-topbar-canvas' );
		var discovered   = document.getElementById( 'edminboost-discovered-list' );
		var searchInput  = document.getElementById( 'edminboost-plugin-search' );
		var drawer       = document.getElementById( 'edminboost-item-drawer' );
		var emptyHint    = document.getElementById( 'edminboost-canvas-empty' );
		var hiddenInputs = document.getElementById( 'edminboost-topbar-hidden-inputs' );

		if ( ! form || ! canvas || ! discovered ) {
			return;
		}

		var selectedItem = null;
		var dragItem     = null;
		var dragPayload  = null;

		function getItems() {
			return Array.prototype.slice.call( canvas.querySelectorAll( '.edminboost-topbar-item' ) );
		}

		function findCanvasItemBySlug( slug, anchor ) {
			anchor = anchor || '';
			return getItems().find( function ( item ) {
				return item.dataset.slug === slug && ( item.dataset.anchor || '' ) === anchor;
			} ) || null;
		}

		function findDiscoveredRowBySlug( slug ) {
			var rows = discovered.querySelectorAll( '.edminboost-discovered-item' );
			for ( var i = 0; i < rows.length; i++ ) {
				if ( rows[ i ].dataset.slug === slug ) {
					return rows[ i ];
				}
			}
			return null;
		}

		function getRowData( row ) {
			return {
				slug: row.dataset.slug || '',
				label: row.dataset.label || '',
				icon: row.dataset.icon || 'dashicons-admin-generic'
			};
		}

		function setDiscoveredChecked( slug, checked ) {
			var row = findDiscoveredRowBySlug( slug );
			if ( ! row ) {
				return;
			}

			var checkbox = row.querySelector( '.edminboost-discovered-item__checkbox' );
			if ( checkbox ) {
				checkbox.checked = checked;
			}

			row.classList.toggle( 'is-active', checked );
		}

		function updateEmptyState() {
			if ( ! emptyHint ) {
				return;
			}
			emptyHint.hidden = getItems().length > 0;
		}

		function syncHiddenInputs() {
			if ( ! hiddenInputs ) {
				return;
			}

			hiddenInputs.innerHTML = '';
			var optionName = window.edminboostData.optionName || 'edminboost_settings';

			getItems().forEach( function ( item, index ) {
				var fields = {
					slug: item.dataset.slug || '',
					anchor: item.dataset.anchor || '',
					label: item.dataset.label || '',
					icon: item.dataset.icon || 'dashicons-admin-generic',
					interaction: item.dataset.interaction || 'redirect',
					badge_source: item.dataset.badgeSource || ''
				};

				Object.keys( fields ).forEach( function ( key ) {
					var input = document.createElement( 'input' );
					input.type = 'hidden';
					input.name = optionName + '[command_center][top_bar_items][' + index + '][' + key + ']';
					input.value = fields[ key ];
					hiddenInputs.appendChild( input );
				} );
			} );
		}

		function createTopBarItem( data ) {
			var li = document.createElement( 'li' );
			li.className = 'edminboost-topbar-item';
			li.setAttribute( 'role', 'listitem' );
			li.draggable = true;
			li.dataset.slug = data.slug;
			li.dataset.anchor = data.anchor || '';
			li.dataset.label = data.label;
			li.dataset.icon = data.icon;
			li.dataset.interaction = data.interaction || 'redirect';
			li.dataset.badgeSource = data.badgeSource || '';

			var btn = document.createElement( 'button' );
			btn.type = 'button';
			btn.className = 'edminboost-topbar-item__btn';
			btn.setAttribute( 'aria-label', data.label );

			var icon = document.createElement( 'span' );
			icon.className = 'edminboost-topbar-item__icon dashicons ' + data.icon;
			icon.setAttribute( 'aria-hidden', 'true' );

			var text = document.createElement( 'span' );
			text.className = 'edminboost-topbar-item__text';
			text.textContent = data.label;

			btn.appendChild( icon );
			btn.appendChild( text );

			if ( data.badgeSource ) {
				var badge = document.createElement( 'span' );
				badge.className = 'edminboost-topbar-item__badge';
				badge.setAttribute( 'aria-hidden', 'true' );
				badge.textContent = '3';
				btn.appendChild( badge );
			}

			li.appendChild( btn );
			bindTopBarItem( li );
			return li;
		}

		function addToCanvas( data ) {
			var anchor = data.anchor || '';

			if ( ! data.slug || findCanvasItemBySlug( data.slug, anchor ) ) {
				return;
			}

			canvas.appendChild( createTopBarItem( data ) );
			if ( ! anchor ) {
				setDiscoveredChecked( data.slug, true );
			}
			updateEmptyState();
			syncHiddenInputs();
		}

		function removeFromCanvas( slug, anchor ) {
			var item = findCanvasItemBySlug( slug, anchor );
			if ( item ) {
				item.remove();
			}

			if ( '' === ( anchor || '' ) ) {
				setDiscoveredChecked( slug, false );
			}

			if ( selectedItem && selectedItem.dataset.slug === slug && ( selectedItem.dataset.anchor || '' ) === ( anchor || '' ) ) {
				closeDrawer();
			}

			updateEmptyState();
			syncHiddenInputs();
		}

		function openDrawer( item ) {
			if ( ! drawer ) {
				return;
			}

			selectedItem = item;
			getItems().forEach( function ( el ) {
				el.classList.toggle( 'is-selected', el === item );
			} );

			drawer.hidden = false;

			var subtitle = document.getElementById( 'edminboost-drawer-subtitle' );
			if ( subtitle ) {
				var subtitleText = item.dataset.slug || '';
				if ( item.dataset.anchor ) {
					subtitleText += '#' + item.dataset.anchor;
				}
				subtitle.textContent = subtitleText;
			}

			var labelInput = document.getElementById( 'edminboost-item-label' );
			if ( labelInput ) {
				labelInput.value = item.dataset.label || '';
			}

			var anchorInput = document.getElementById( 'edminboost-item-anchor' );
			if ( anchorInput ) {
				anchorInput.value = item.dataset.anchor || '';
			}

			var interaction = item.dataset.interaction || 'redirect';
			var radios = drawer.querySelectorAll( 'input[name="edminboost_item_interaction"]' );
			radios.forEach( function ( radio ) {
				radio.checked = radio.value === interaction;
			} );

			var badgeSelect = document.getElementById( 'edminboost-item-badge' );
			if ( badgeSelect ) {
				badgeSelect.value = item.dataset.badgeSource || '';
			}

			var iconButtons = drawer.querySelectorAll( '.edminboost-icon-picker__btn' );
			iconButtons.forEach( function ( btn ) {
				btn.classList.toggle( 'is-selected', btn.dataset.icon === item.dataset.icon );
			} );

			updateDrawerPreviewVisibility();
		}

		function updateDrawerPreviewVisibility() {
			var previewWrap = document.getElementById( 'edminboost-drawer-preview-wrap' );

			if ( ! previewWrap ) {
				return;
			}

			if ( ! selectedItem ) {
				previewWrap.hidden = true;
				return;
			}

			previewWrap.hidden = ( selectedItem.dataset.interaction || 'redirect' ) !== 'drawer';
		}

		function closeDrawer() {
			if ( drawer ) {
				drawer.hidden = true;
			}

			selectedItem = null;
			getItems().forEach( function ( el ) {
				el.classList.remove( 'is-selected' );
			} );
		}

		function bindTopBarItem( item ) {
			var btn = item.querySelector( '.edminboost-topbar-item__btn' );

			if ( btn ) {
				btn.addEventListener( 'click', function ( event ) {
					event.preventDefault();
					openDrawer( item );
				} );
			}

			item.addEventListener( 'dragstart', function ( event ) {
				dragPayload = null;
				dragItem = item;
				item.classList.add( 'is-dragging' );
				event.dataTransfer.effectAllowed = 'move';
			} );

			item.addEventListener( 'dragend', function () {
				item.classList.remove( 'is-dragging' );
				dragItem = null;
				setCanvasDragOver( false );
				syncHiddenInputs();
			} );

			item.addEventListener( 'dragover', function ( event ) {
				event.preventDefault();
				if ( ! dragItem || dragItem === item ) {
					return;
				}

				var rect = item.getBoundingClientRect();
				var after = event.clientX > rect.left + rect.width / 2;
				canvas.insertBefore( dragItem, after ? item.nextSibling : item );
			} );
		}

		function setCanvasDragOver( active ) {
			if ( canvasShell ) {
				canvasShell.classList.toggle( 'is-drag-over', active );
			}
		}

		function handleDiscoveredDrop() {
			if ( ! dragPayload || ! dragPayload.slug ) {
				return;
			}

			addToCanvas( dragPayload );
			dragPayload = null;
		}

		getItems().forEach( bindTopBarItem );

		discovered.querySelectorAll( '.edminboost-discovered-item' ).forEach( function ( row ) {
			if ( findCanvasItemBySlug( row.dataset.slug ) ) {
				row.classList.add( 'is-active' );
			}

			row.draggable = true;

			row.addEventListener( 'dragstart', function ( event ) {
				if ( event.target.closest( '.edminboost-discovered-item__toggle' ) ) {
					event.preventDefault();
					return;
				}

				dragItem = null;
				dragPayload = getRowData( row );
				row.classList.add( 'is-dragging' );
				event.dataTransfer.effectAllowed = 'copy';
				event.dataTransfer.setData( 'text/plain', dragPayload.slug );
			} );

			row.addEventListener( 'dragend', function () {
				row.classList.remove( 'is-dragging' );
				dragPayload = null;
				setCanvasDragOver( false );
			} );
		} );

		function allowCanvasDrop( event ) {
			if ( ! dragItem && ! dragPayload ) {
				return;
			}

			event.preventDefault();
			setCanvasDragOver( true );
		}

		canvas.addEventListener( 'dragover', allowCanvasDrop );
		canvas.addEventListener( 'drop', function ( event ) {
			event.preventDefault();
			setCanvasDragOver( false );
			handleDiscoveredDrop();
		} );

		if ( canvasShell ) {
			canvasShell.addEventListener( 'dragover', allowCanvasDrop );
			canvasShell.addEventListener( 'dragleave', function ( event ) {
				if ( ! canvasShell.contains( event.relatedTarget ) ) {
					setCanvasDragOver( false );
				}
			} );
			canvasShell.addEventListener( 'drop', function ( event ) {
				if ( event.target.closest( '.edminboost-topbar-item' ) ) {
					return;
				}

				event.preventDefault();
				setCanvasDragOver( false );
				handleDiscoveredDrop();
			} );
		}

		discovered.addEventListener( 'change', function ( event ) {
			var checkbox = event.target;
			if ( ! checkbox.classList.contains( 'edminboost-discovered-item__checkbox' ) ) {
				return;
			}

			var row = checkbox.closest( '.edminboost-discovered-item' );
			if ( ! row ) {
				return;
			}

			if ( checkbox.checked ) {
				addToCanvas( getRowData( row ) );
			} else {
				removeFromCanvas( row.dataset.slug );
			}
		} );

		discovered.addEventListener( 'click', function ( event ) {
			if (
				event.target.closest( '.edminboost-discovered-item__handle' ) ||
				event.target.closest( '.edminboost-discovered-item__toggle' )
			) {
				return;
			}

			var row = event.target.closest( '.edminboost-discovered-item' );
			if ( ! row ) {
				return;
			}

			var checkbox = row.querySelector( '.edminboost-discovered-item__checkbox' );
			if ( ! checkbox ) {
				return;
			}

			checkbox.checked = ! checkbox.checked;
			checkbox.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		} );

		if ( searchInput ) {
			searchInput.addEventListener( 'input', function () {
				var query = searchInput.value.toLowerCase().trim();
				discovered.querySelectorAll( '.edminboost-discovered-item' ).forEach( function ( row ) {
					var label = ( row.dataset.label || '' ).toLowerCase();
					var slug = ( row.dataset.slug || '' ).toLowerCase();
					var matches = query === '' || label.indexOf( query ) !== -1 || slug.indexOf( query ) !== -1;
					row.classList.toggle( 'is-hidden-by-search', ! matches );
				} );
			} );
		}

		var customLinkPathInput   = document.getElementById( 'edminboost-custom-link-path' );
		var customLinkLabelInput  = document.getElementById( 'edminboost-custom-link-label' );
		var customLinkAnchorInput = document.getElementById( 'edminboost-custom-link-anchor' );
		var customLinkAddBtn      = document.getElementById( 'edminboost-custom-link-add' );
		var customLinkError       = document.getElementById( 'edminboost-custom-link-error' );
		var pathPattern             = /^[a-zA-Z0-9_\-\.?=&%]+$/;
		var anchorPattern           = /^[a-zA-Z0-9_\-\.]+$/;

		function showCustomLinkError( message ) {
			if ( ! customLinkError ) {
				return;
			}

			if ( message ) {
				customLinkError.textContent = message;
				customLinkError.hidden = false;
				if ( customLinkError.closest( 'details' ) ) {
					customLinkError.closest( 'details' ).open = true;
				}
				return;
			}

			customLinkError.textContent = '';
			customLinkError.hidden = true;
		}

		function parsePathAndAnchor( value ) {
			var path = ( value || '' ).trim();
			var hashIndex = path.indexOf( '#' );

			if ( hashIndex === -1 ) {
				return {
					path: path,
					anchor: ''
				};
			}

			return {
				path: path.substring( 0, hashIndex ),
				anchor: path.substring( hashIndex + 1 )
			};
		}

		function normalizeCustomPath( value ) {
			var parsed = parsePathAndAnchor( value );
			var path = parsed.path;

			if ( 0 === path.indexOf( 'http://' ) || 0 === path.indexOf( 'https://' ) ) {
				return {
					path: path,
					anchor: parsed.anchor
				};
			}

			path = path.replace( /^\/?wp-admin\//, '' );
			path = path.replace( /^\/+/, '' );

			return {
				path: path,
				anchor: parsed.anchor
			};
		}

		function normalizeAnchor( value ) {
			return ( value || '' ).trim().replace( /^#+/, '' );
		}

		function addCustomLinkToCanvas() {
			if ( ! customLinkPathInput || ! customLinkLabelInput ) {
				return;
			}

			var parsed  = normalizeCustomPath( customLinkPathInput.value );
			var slug    = parsed.path;
			var anchor  = normalizeAnchor( customLinkAnchorInput ? customLinkAnchorInput.value : '' ) || parsed.anchor;
			var label   = customLinkLabelInput.value.trim();
			var strings = ( window.edminboostData && window.edminboostData.strings ) || {};

			showCustomLinkError( '' );

			if ( ! slug ) {
				showCustomLinkError( strings.customLinkPathRequired || 'Enter an admin path.' );
				customLinkPathInput.focus();
				return;
			}

			if ( ! label ) {
				showCustomLinkError( strings.customLinkLabelRequired || 'Enter a label.' );
				customLinkLabelInput.focus();
				return;
			}

			if ( ! pathPattern.test( slug ) ) {
				showCustomLinkError( strings.customLinkPathInvalid || 'Use a relative admin path such as edit.php?post_type=page.' );
				customLinkPathInput.focus();
				return;
			}

			if ( anchor && ! anchorPattern.test( anchor ) ) {
				showCustomLinkError( strings.customLinkAnchorInvalid || 'Use letters, numbers, hyphens, underscores, or dots in the anchor.' );
				if ( customLinkAnchorInput ) {
					customLinkAnchorInput.focus();
				}
				return;
			}

			if ( findCanvasItemBySlug( slug, anchor ) ) {
				showCustomLinkError( strings.customLinkDuplicate || 'That link is already on your top bar.' );
				return;
			}

			addToCanvas( {
				slug: slug,
				anchor: anchor,
				label: label,
				icon: 'dashicons-admin-links'
			} );

			customLinkPathInput.value = '';
			customLinkLabelInput.value = '';
			if ( customLinkAnchorInput ) {
				customLinkAnchorInput.value = '';
			}
			customLinkPathInput.focus();
		}

		if ( customLinkAddBtn ) {
			customLinkAddBtn.addEventListener( 'click', addCustomLinkToCanvas );
		}

		if ( customLinkPathInput ) {
			customLinkPathInput.addEventListener( 'keydown', function ( event ) {
				if ( 'Enter' === event.key ) {
					event.preventDefault();
					addCustomLinkToCanvas();
				}
			} );
		}

		if ( customLinkLabelInput ) {
			customLinkLabelInput.addEventListener( 'keydown', function ( event ) {
				if ( 'Enter' === event.key ) {
					event.preventDefault();
					addCustomLinkToCanvas();
				}
			} );
		}

		if ( customLinkAnchorInput ) {
			customLinkAnchorInput.addEventListener( 'keydown', function ( event ) {
				if ( 'Enter' === event.key ) {
					event.preventDefault();
					addCustomLinkToCanvas();
				}
			} );
		}

		var labelInput = document.getElementById( 'edminboost-item-label' );
		if ( labelInput ) {
			labelInput.addEventListener( 'input', function () {
				if ( ! selectedItem ) {
					return;
				}
				selectedItem.dataset.label = labelInput.value;
				var text = selectedItem.querySelector( '.edminboost-topbar-item__text' );
				if ( text ) {
					text.textContent = labelInput.value;
				}
				syncHiddenInputs();
			} );
		}

		var itemAnchorInput = document.getElementById( 'edminboost-item-anchor' );
		if ( itemAnchorInput ) {
			itemAnchorInput.addEventListener( 'input', function () {
				if ( ! selectedItem ) {
					return;
				}

				selectedItem.dataset.anchor = normalizeAnchor( itemAnchorInput.value );
				syncHiddenInputs();
			} );
		}

		var interactionRadios = document.querySelectorAll( 'input[name="edminboost_item_interaction"]' );
		interactionRadios.forEach( function ( radio ) {
			radio.addEventListener( 'change', function () {
				if ( ! selectedItem || ! radio.checked ) {
					return;
				}
				selectedItem.dataset.interaction = radio.value;
				updateDrawerPreviewVisibility();
				syncHiddenInputs();
			} );
		} );

		var previewBtn = document.getElementById( 'edminboost-drawer-preview' );
		if ( previewBtn ) {
			previewBtn.addEventListener( 'click', function () {
				if ( ! selectedItem ) {
					return;
				}

				var preview = window.edminboostData && window.edminboostData.drawerPreview;
				var strings = ( window.edminboostData && window.edminboostData.strings ) || {};

				if ( ! preview || typeof window.edminboostCcOpenDrawer !== 'function' ) {
					window.alert( strings.drawerPreviewFailed || 'Could not open the drawer preview.' );
					return;
				}

				var formData = new window.FormData();
				formData.append( 'action', preview.action );
				formData.append( 'nonce', preview.nonce );
				formData.append( 'slug', selectedItem.dataset.slug || '' );
				formData.append( 'anchor', selectedItem.dataset.anchor || '' );
				formData.append( 'label', selectedItem.dataset.label || '' );

				previewBtn.disabled = true;

				window.fetch( preview.ajaxUrl, {
					method: 'POST',
					body: formData,
					credentials: 'same-origin'
				} )
					.then( function ( response ) {
						return response.json();
					} )
					.then( function ( payload ) {
						if ( ! payload.success || ! payload.data ) {
							var message = payload.data && payload.data.message
								? payload.data.message
								: ( strings.drawerPreviewFailed || 'Could not open the drawer preview.' );
							throw new Error( message );
						}

						window.edminboostCcOpenDrawer( payload.data );
					} )
					.catch( function ( error ) {
						window.alert( error.message || strings.drawerPreviewFailed || 'Could not open the drawer preview.' );
					} )
					.finally( function () {
						previewBtn.disabled = false;
					} );
			} );
		}

		var badgeSelect = document.getElementById( 'edminboost-item-badge' );
		if ( badgeSelect ) {
			badgeSelect.addEventListener( 'change', function () {
				if ( ! selectedItem ) {
					return;
				}
				selectedItem.dataset.badgeSource = badgeSelect.value;

				var btn = selectedItem.querySelector( '.edminboost-topbar-item__btn' );
				var existing = selectedItem.querySelector( '.edminboost-topbar-item__badge' );

				if ( badgeSelect.value && btn ) {
					if ( ! existing ) {
						existing = document.createElement( 'span' );
						existing.className = 'edminboost-topbar-item__badge';
						existing.setAttribute( 'aria-hidden', 'true' );
						existing.textContent = '3';
						btn.appendChild( existing );
					}
				} else if ( existing ) {
					existing.remove();
				}

				syncHiddenInputs();
			} );
		}

		var iconPicker = document.getElementById( 'edminboost-icon-picker' );
		if ( iconPicker ) {
			iconPicker.addEventListener( 'click', function ( event ) {
				var btn = event.target.closest( '.edminboost-icon-picker__btn' );
				if ( ! btn || ! selectedItem ) {
					return;
				}

				selectedItem.dataset.icon = btn.dataset.icon;
				var icon = selectedItem.querySelector( '.edminboost-topbar-item__icon' );
				if ( icon ) {
					icon.className = 'edminboost-topbar-item__icon dashicons ' + btn.dataset.icon;
				}

				iconPicker.querySelectorAll( '.edminboost-icon-picker__btn' ).forEach( function ( el ) {
					el.classList.toggle( 'is-selected', el === btn );
				} );

				syncHiddenInputs();
			} );
		}

		var closeBtn = document.getElementById( 'edminboost-drawer-close' );
		if ( closeBtn ) {
			closeBtn.addEventListener( 'click', closeDrawer );
		}

		var removeBtn = document.getElementById( 'edminboost-drawer-remove' );
		if ( removeBtn ) {
			removeBtn.addEventListener( 'click', function () {
				if ( selectedItem ) {
					removeFromCanvas( selectedItem.dataset.slug, selectedItem.dataset.anchor || '' );
				}
			} );
		}

		form.addEventListener( 'submit', syncHiddenInputs, true );
		form.edminboostSyncHiddenInputs = syncHiddenInputs;
		syncHiddenInputs();
		updateEmptyState();
	}

	function initMenuStudio( root ) {
		var form         = document.getElementById( 'edminboost-menu-studio-form' );
		var canvas       = document.getElementById( 'edminboost-sidebar-items' );
		var canvasShell  = document.getElementById( 'edminboost-sidebar-canvas' );
		var discovered   = document.getElementById( 'edminboost-menu-discovered-list' );
		var searchInput  = document.getElementById( 'edminboost-menu-search' );
		var emptyHint    = document.getElementById( 'edminboost-menu-canvas-empty' );
		var hiddenInputs = document.getElementById( 'edminboost-menu-hidden-inputs' );
		var useColors    = document.getElementById( 'edminboost_menu_studio_use_colors' );
		var colorGrid    = document.getElementById( 'edminboost-menu-color-grid' );
		var colorPreview = document.getElementById( 'edminboost-menu-color-preview' );

		if ( ! form || ! canvas || ! discovered ) {
			return;
		}

		var dragItem    = null;
		var dragPayload = null;
		var customItems = [];

		canvas.querySelectorAll( '.edminboost-sidebar-item.is-custom' ).forEach( function ( item ) {
			customItems.push( {
				id: item.dataset.slug ? item.dataset.slug.replace( /^edminboost_ms_/, '' ) : '',
				label: item.dataset.label || '',
				path: item.dataset.path || '',
				icon: item.dataset.icon || 'dashicons-admin-links',
				parent: item.dataset.parent || ''
			} );
		} );

		function getTopItems() {
			return Array.prototype.filter.call( canvas.children, function ( child ) {
				return child.classList && child.classList.contains( 'edminboost-sidebar-item' );
			} );
		}

		function getRowData( row ) {
			var children = [];
			if ( row.dataset.children ) {
				try {
					children = JSON.parse( row.dataset.children );
				} catch ( error ) {
					children = [];
				}
			}

			return {
				slug: row.dataset.slug || '',
				label: row.dataset.label || '',
				icon: row.dataset.icon || 'dashicons-admin-generic',
				children: children
			};
		}

		function findDiscoveredRow( slug ) {
			return discovered.querySelector( '.edminboost-discovered-item[data-slug="' + slug + '"]' );
		}

		function setDiscoveredVisible( slug, visible ) {
			var row = findDiscoveredRow( slug );
			if ( ! row ) {
				return;
			}

			var checkbox = row.querySelector( '.edminboost-discovered-item__checkbox' );
			if ( checkbox && ! checkbox.disabled ) {
				checkbox.checked = visible;
			}

			row.classList.toggle( 'is-hidden-item', ! visible );
		}

		function updateEmptyState() {
			if ( emptyHint ) {
				emptyHint.hidden = getTopItems().length > 0;
			}
		}

		function appendHiddenInput( name, value ) {
			var input = document.createElement( 'input' );
			input.type = 'hidden';
			input.name = name;
			input.value = value;
			hiddenInputs.appendChild( input );
		}

		function syncHiddenInputs() {
			if ( ! hiddenInputs ) {
				return;
			}

			hiddenInputs.innerHTML = '';
			var optionName = window.edminboostData.optionName || 'edminboost_settings';
			var msPrefix   = optionName + '[command_center][menu_studio]';

			var submenuParentIndex = 0;

			getTopItems().forEach( function ( item, index ) {
				appendHiddenInput( msPrefix + '[order][' + index + ']', item.dataset.slug || '' );

				var subList = item.querySelector( '.edminboost-sidebar-subitems' );
				if ( ! subList ) {
					return;
				}

				appendHiddenInput(
					msPrefix + '[submenu_parents][' + submenuParentIndex + ']',
					item.dataset.slug || ''
				);

				Array.prototype.forEach.call( subList.querySelectorAll( '.edminboost-sidebar-subitem' ), function ( subItem, subIndex ) {
					appendHiddenInput(
						msPrefix + '[submenu_order][' + submenuParentIndex + '][' + subIndex + ']',
						subItem.dataset.slug || ''
					);
				} );

				submenuParentIndex++;
			} );

			discovered.querySelectorAll( '.edminboost-discovered-item' ).forEach( function ( row ) {
				var checkbox = row.querySelector( '.edminboost-discovered-item__checkbox' );
				if ( ! checkbox || checkbox.disabled || checkbox.checked ) {
					return;
				}

				appendHiddenInput( msPrefix + '[hidden_items][]', row.dataset.slug || '' );
			} );

			customItems.forEach( function ( custom, index ) {
				Object.keys( custom ).forEach( function ( key ) {
					appendHiddenInput( msPrefix + '[custom_items][' + index + '][' + key + ']', custom[ key ] || '' );
				} );
			} );
		}

		function createSidebarItem( data ) {
			var li = document.createElement( 'li' );
			li.className = 'edminboost-sidebar-item' + ( data.custom ? ' is-custom' : '' );
			li.setAttribute( 'role', 'listitem' );
			li.draggable = true;
			li.dataset.slug = data.slug;
			li.dataset.label = data.label;
			li.dataset.icon = data.icon || 'dashicons-admin-generic';

			if ( data.custom ) {
				li.dataset.custom = '1';
				li.dataset.path = data.path || '';
				li.dataset.parent = data.parent || '';
			}

			if ( data.children && data.children.length ) {
				li.dataset.children = JSON.stringify( data.children );
			}

			var row = document.createElement( 'div' );
			row.className = 'edminboost-sidebar-item__row';

			var handle = document.createElement( 'span' );
			handle.className = 'edminboost-sidebar-item__handle dashicons dashicons-move';
			handle.setAttribute( 'aria-hidden', 'true' );

			var icon = document.createElement( 'span' );
			icon.className = 'edminboost-sidebar-item__icon dashicons ' + ( data.icon || 'dashicons-admin-generic' );
			icon.setAttribute( 'aria-hidden', 'true' );

			var label = document.createElement( 'span' );
			label.className = 'edminboost-sidebar-item__label';
			label.textContent = data.label;

			row.appendChild( handle );
			row.appendChild( icon );
			row.appendChild( label );

			if ( data.children && data.children.length ) {
				var expand = document.createElement( 'button' );
				expand.type = 'button';
				expand.className = 'edminboost-sidebar-item__expand';
				expand.setAttribute( 'aria-expanded', 'false' );
				expand.setAttribute( 'aria-label', 'Expand submenu' );
				expand.innerHTML = '<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>';
				row.appendChild( expand );

				var subList = document.createElement( 'ul' );
				subList.className = 'edminboost-sidebar-subitems';
				subList.hidden = true;

				data.children.forEach( function ( child ) {
					subList.appendChild( createSubmenuItem( child, data.slug ) );
				} );

				li.appendChild( row );
				li.appendChild( subList );
			} else {
				var badge = document.createElement( 'span' );
				badge.className = 'edminboost-sidebar-item__badge';
				badge.setAttribute( 'aria-hidden', 'true' );
				badge.textContent = '2';
				row.appendChild( badge );
				li.appendChild( row );
			}

			bindSidebarItem( li );
			return li;
		}

		function createSubmenuItem( child, parentSlug ) {
			var li = document.createElement( 'li' );
			li.className = 'edminboost-sidebar-subitem';
			li.draggable = true;
			li.dataset.slug = child.slug;
			li.dataset.label = child.label;
			li.dataset.parent = parentSlug;

			var handle = document.createElement( 'span' );
			handle.className = 'edminboost-sidebar-subitem__handle dashicons dashicons-move';
			handle.setAttribute( 'aria-hidden', 'true' );

			var label = document.createElement( 'span' );
			label.className = 'edminboost-sidebar-subitem__label';
			label.textContent = child.label;

			li.appendChild( handle );
			li.appendChild( label );
			bindSubmenuItem( li );
			return li;
		}

		function bindSubmenuItem( item ) {
			item.addEventListener( 'dragstart', function () {
				dragPayload = null;
				dragItem = item;
				item.classList.add( 'is-dragging' );
			} );

			item.addEventListener( 'dragend', function () {
				item.classList.remove( 'is-dragging' );
				dragItem = null;
				syncHiddenInputs();
			} );

			item.addEventListener( 'dragover', function ( event ) {
				event.preventDefault();
				if ( ! dragItem || dragItem === item || ! dragItem.classList.contains( 'edminboost-sidebar-subitem' ) ) {
					return;
				}

				var rect = item.getBoundingClientRect();
				var after = event.clientY > rect.top + rect.height / 2;
				item.parentNode.insertBefore( dragItem, after ? item.nextSibling : item );
				syncHiddenInputs();
			} );
		}

		function bindSidebarItem( item ) {
			var expand = item.querySelector( '.edminboost-sidebar-item__expand' );
			var subList = item.querySelector( '.edminboost-sidebar-subitems' );

			if ( expand && subList ) {
				expand.addEventListener( 'click', function () {
					var isOpen = ! subList.hidden;
					subList.hidden = isOpen;
					expand.setAttribute( 'aria-expanded', isOpen ? 'false' : 'true' );
				} );
			}

			item.addEventListener( 'dragstart', function ( event ) {
				if ( event.target.closest( '.edminboost-sidebar-item__expand' ) ) {
					event.preventDefault();
					return;
				}

				dragPayload = null;
				dragItem = item;
				item.classList.add( 'is-dragging' );
			} );

			item.addEventListener( 'dragend', function () {
				item.classList.remove( 'is-dragging' );
				dragItem = null;
				setCanvasDragOver( false );
				syncHiddenInputs();
			} );

			item.addEventListener( 'dragover', function ( event ) {
				event.preventDefault();
				if ( ! dragItem || dragItem === item || ! dragItem.classList.contains( 'edminboost-sidebar-item' ) ) {
					return;
				}

				var rect = item.getBoundingClientRect();
				var after = event.clientY > rect.top + rect.height / 2;
				canvas.insertBefore( dragItem, after ? item.nextSibling : item );
				syncHiddenInputs();
			} );

			if ( subList ) {
				subList.querySelectorAll( '.edminboost-sidebar-subitem' ).forEach( bindSubmenuItem );
			}
		}

		function setCanvasDragOver( active ) {
			if ( canvasShell ) {
				canvasShell.classList.toggle( 'is-drag-over', active );
			}
		}

		function addToCanvas( data ) {
			if ( ! data.slug ) {
				return;
			}

			var existing = canvas.querySelector( '.edminboost-sidebar-item[data-slug="' + data.slug + '"]' );
			if ( existing ) {
				return;
			}

			canvas.appendChild( createSidebarItem( data ) );
			if ( ! data.custom ) {
				setDiscoveredVisible( data.slug, true );
			}
			updateEmptyState();
			syncHiddenInputs();
		}

		function removeFromCanvas( slug ) {
			var item = canvas.querySelector( '.edminboost-sidebar-item[data-slug="' + slug + '"]' );
			if ( item ) {
				item.remove();
			}

			setDiscoveredVisible( slug, false );
			updateEmptyState();
			syncHiddenInputs();
		}

		getTopItems().forEach( bindSidebarItem );

		discovered.querySelectorAll( '.edminboost-discovered-item' ).forEach( function ( row ) {
			row.draggable = true;

			row.addEventListener( 'dragstart', function ( event ) {
				if ( event.target.closest( '.edminboost-discovered-item__toggle' ) ) {
					event.preventDefault();
					return;
				}

				dragItem = null;
				dragPayload = getRowData( row );
				row.classList.add( 'is-dragging' );
			} );

			row.addEventListener( 'dragend', function () {
				row.classList.remove( 'is-dragging' );
				dragPayload = null;
				setCanvasDragOver( false );
			} );

			var checkbox = row.querySelector( '.edminboost-discovered-item__checkbox' );
			if ( ! checkbox ) {
				return;
			}

			checkbox.addEventListener( 'change', function () {
				var slug = row.dataset.slug || '';
				if ( checkbox.checked ) {
					addToCanvas( getRowData( row ) );
				} else {
					removeFromCanvas( slug );
				}
			} );

			row.addEventListener( 'click', function ( event ) {
				if ( event.target.closest( '.edminboost-discovered-item__toggle' ) || event.target.closest( '.edminboost-discovered-item__handle' ) ) {
					return;
				}

				if ( checkbox.disabled ) {
					return;
				}

				checkbox.checked = ! checkbox.checked;
				checkbox.dispatchEvent( new Event( 'change' ) );
			} );
		} );

		function allowCanvasDrop( event ) {
			if ( ! dragItem && ! dragPayload ) {
				return;
			}

			event.preventDefault();
			setCanvasDragOver( true );
		}

		canvas.addEventListener( 'dragover', allowCanvasDrop );
		canvas.addEventListener( 'drop', function ( event ) {
			event.preventDefault();
			setCanvasDragOver( false );

			if ( dragPayload && dragPayload.slug ) {
				addToCanvas( dragPayload );
				dragPayload = null;
			}
		} );

		if ( canvasShell ) {
			canvasShell.addEventListener( 'dragover', allowCanvasDrop );
			canvasShell.addEventListener( 'dragleave', function ( event ) {
				if ( ! canvasShell.contains( event.relatedTarget ) ) {
					setCanvasDragOver( false );
				}
			} );
			canvasShell.addEventListener( 'drop', function ( event ) {
				if ( event.target.closest( '.edminboost-sidebar-item' ) ) {
					return;
				}

				event.preventDefault();
				setCanvasDragOver( false );

				if ( dragPayload && dragPayload.slug ) {
					addToCanvas( dragPayload );
					dragPayload = null;
				}
			} );
		}

		if ( searchInput ) {
			searchInput.addEventListener( 'input', function () {
				var query = searchInput.value.trim().toLowerCase();
				discovered.querySelectorAll( '.edminboost-discovered-item' ).forEach( function ( row ) {
					var label = ( row.dataset.label || '' ).toLowerCase();
					row.hidden = query !== '' && label.indexOf( query ) === -1;
				} );
			} );
		}

		var customAddBtn = document.getElementById( 'edminboost-menu-custom-add' );
		if ( customAddBtn ) {
			customAddBtn.addEventListener( 'click', function () {
				var pathInput   = document.getElementById( 'edminboost-menu-custom-path' );
				var labelInput  = document.getElementById( 'edminboost-menu-custom-label' );
				var parentInput = document.getElementById( 'edminboost-menu-custom-parent' );
				var errorEl     = document.getElementById( 'edminboost-menu-custom-error' );
				var strings     = window.edminboostData.strings || {};

				var path   = pathInput ? pathInput.value.trim().replace( /^\/+/, '' ) : '';
				var label  = labelInput ? labelInput.value.trim() : '';
				var parent = parentInput ? parentInput.value : '';

				if ( errorEl ) {
					errorEl.hidden = true;
					errorEl.textContent = '';
				}

				if ( ! path ) {
					if ( errorEl ) {
						errorEl.hidden = false;
						errorEl.textContent = strings.customMenuPathRequired || 'Enter an admin path.';
					}
					return;
				}

				if ( ! label ) {
					if ( errorEl ) {
						errorEl.hidden = false;
						errorEl.textContent = strings.customMenuLabelRequired || 'Enter a label.';
					}
					return;
				}

				if ( ! /^[a-zA-Z0-9_\-\./?=&%#]+$/.test( path ) || /^https?:\/\//i.test( path ) ) {
					if ( errorEl ) {
						errorEl.hidden = false;
						errorEl.textContent = strings.customMenuPathInvalid || 'Use a relative admin path.';
					}
					return;
				}

				var id = 'custom_' + Math.random().toString( 36 ).slice( 2, 10 );
				var slug = 'edminboost_ms_' + id;

				if ( canvas.querySelector( '.edminboost-sidebar-item[data-slug="' + slug + '"]' ) ) {
					if ( errorEl ) {
						errorEl.hidden = false;
						errorEl.textContent = strings.customMenuDuplicate || 'That link is already on your sidebar.';
					}
					return;
				}

				var custom = {
					id: id,
					label: label,
					path: path,
					icon: 'dashicons-admin-links',
					parent: parent
				};

				customItems.push( custom );

				if ( ! parent ) {
					addToCanvas( {
						slug: slug,
						label: label,
						icon: 'dashicons-admin-links',
						custom: true,
						path: path
					} );
				}

				if ( pathInput ) {
					pathInput.value = '';
				}
				if ( labelInput ) {
					labelInput.value = '';
				}
				if ( parentInput ) {
					parentInput.value = '';
				}

				syncHiddenInputs();
			} );
		}

		function applyColorPreview() {
			if ( ! colorPreview ) {
				return;
			}

			var map = {
				parent_bg: '--eb-ms-preview-parent-bg',
				parent_text: '--eb-ms-preview-parent-text',
				parent_active: '--eb-ms-preview-parent-active',
				submenu_bg: '--eb-ms-preview-submenu-bg',
				submenu_text: '--eb-ms-preview-submenu-text',
				notification_bg: '--eb-ms-preview-notification-bg',
				notification_text: '--eb-ms-preview-notification-text'
			};

			Object.keys( map ).forEach( function ( key ) {
				var input = document.getElementById( 'edminboost_menu_color_' + key );
				colorPreview.style.removeProperty( map[ key ] );
				if ( input && input.value ) {
					colorPreview.style.setProperty( map[ key ], input.value );
				}
			} );
		}

		if ( useColors && colorGrid ) {
			useColors.addEventListener( 'change', function () {
				colorGrid.hidden = ! useColors.checked;
				applyColorPreview();
			} );
		}

		root.querySelectorAll( '.edminboost-menu-color-row' ).forEach( function ( row ) {
			var picker = row.querySelector( 'input[type="color"]' );
			var text   = row.querySelector( '.edminboost-menu-color-input' );

			if ( ! picker || ! text ) {
				return;
			}

			picker.addEventListener( 'input', function () {
				text.value = picker.value;
				applyColorPreview();
			} );

			text.addEventListener( 'input', function () {
				if ( /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test( text.value ) ) {
					picker.value = text.value;
				}
				applyColorPreview();
			} );
		} );

		applyColorPreview();
		form.addEventListener( 'submit', syncHiddenInputs, true );
		form.edminboostSyncHiddenInputs = syncHiddenInputs;
		syncHiddenInputs();
		updateEmptyState();
	}

	function initBehavior( root ) {
		initBehaviorBadgePreview( root );
		initBehaviorDrawerWidthCustom( root );
		initBehaviorAnimationSpeed( root );
	}

	function initBehaviorBadgePreview( root ) {
		var styleRadios = root.querySelectorAll( 'input[name*="[badge_style]"]' );
		var previews    = root.querySelectorAll( '.edminboost-badge-preview__item' );

		if ( ! styleRadios.length || ! previews.length ) {
			return;
		}

		function updatePreview() {
			var active = 'pill';
			styleRadios.forEach( function ( radio ) {
				if ( radio.checked ) {
					active = radio.value;
				}
			} );

			previews.forEach( function ( preview ) {
				preview.hidden = preview.dataset.style !== active;
			} );
		}

		styleRadios.forEach( function ( radio ) {
			radio.addEventListener( 'change', updatePreview );
		} );

		updatePreview();
	}

	function initBehaviorDrawerWidthCustom( root ) {
		var widthRadios    = root.querySelectorAll( 'input[name*="[drawer_width]"]' );
		var customWrap     = document.getElementById( 'edminboost-drawer-width-custom' );
		var slider         = document.getElementById( 'edminboost_drawer_width_custom' );
		var valueEl        = document.getElementById( 'edminboost_drawer_width_custom_value' );
		var previewDrawer  = document.getElementById( 'edminboost_drawer_width_preview_drawer' );
		var previewCaption = document.getElementById( 'edminboost-drawer-width-preview-caption' );
		var referenceViewport = 1280;
		var presetWidths = {
			compact: 400,
			standard: 600
		};

		if ( ! widthRadios.length ) {
			return;
		}

		function getSelectedWidth() {
			var selected = 'standard';

			widthRadios.forEach( function ( radio ) {
				if ( radio.checked ) {
					selected = radio.value;
				}
			} );

			return selected;
		}

		function formatPreviewCaption( px, percent ) {
			var template = edminboostData.strings.drawerWidthPreviewCaption;

			if ( ! template ) {
				return 'Drawer uses ' + px + 'px — about ' + percent + '% of a typical desktop screen.';
			}

			return template
				.replace( '%1$s', px )
				.replace( '%2$s', percent );
		}

		function updateCustomVisibility() {
			if ( ! customWrap ) {
				return;
			}

			customWrap.hidden = getSelectedWidth() !== 'custom';
		}

		function updateWidthPreview() {
			var selected = getSelectedWidth();
			var px;
			var percent;

			if ( 'fullscreen' === selected ) {
				if ( previewDrawer ) {
					previewDrawer.style.width = '100%';
				}

				if ( previewCaption ) {
					previewCaption.textContent = edminboostData.strings.drawerWidthPreviewFullscreen
						|| 'Drawer uses the full screen width.';
				}

				return;
			}

			if ( 'custom' === selected && slider ) {
				px = parseInt( slider.value, 10 );

				if ( valueEl ) {
					valueEl.textContent = px + 'px';
				}
			} else {
				px = presetWidths[ selected ] || presetWidths.standard;
			}

			percent = Math.round( ( px / referenceViewport ) * 100 );

			if ( previewDrawer ) {
				previewDrawer.style.width = percent + '%';
			}

			if ( previewCaption ) {
				previewCaption.textContent = formatPreviewCaption( px, percent );
			}
		}

		widthRadios.forEach( function ( radio ) {
			radio.addEventListener( 'change', function () {
				updateCustomVisibility();
				updateWidthPreview();
			} );
		} );

		if ( slider ) {
			slider.addEventListener( 'input', updateWidthPreview );
		}

		updateCustomVisibility();
		updateWidthPreview();
	}

	function initBehaviorAnimationSpeed( root ) {
		var speedSelect   = document.getElementById( 'edminboost_animation_speed' );
		var speedPicker   = document.getElementById( 'edminboost-animation-speed-picker' );
		var speedToggle   = document.getElementById( 'edminboost_animation_speed_toggle' );
		var speedList     = document.getElementById( 'edminboost-animation-speed-list' );
		var speedName     = document.getElementById( 'edminboost-animation-speed-name' );
		var toggleDrawer  = document.getElementById( 'edminboost_animation_speed_toggle_drawer' );
		var togglePreview = speedToggle ? speedToggle.querySelector( '.edminboost-animation-speed-picker__preview' ) : null;
		var previewStagger = 180;
		var previewTimers  = [];

		if ( ! speedSelect || ! speedPicker ) {
			return;
		}

		function clearPreviewTimers() {
			previewTimers.forEach( function ( timerId ) {
				window.clearTimeout( timerId );
			} );
			previewTimers = [];
		}

		function getSelectedSpeed() {
			return speedSelect.value || 'normal';
		}

		function getSpeedLabel( speed ) {
			var option = speedSelect.querySelector( 'option[value="' + speed + '"]' );
			return option ? option.textContent : speed;
		}

		function getSpeedMs( speed ) {
			var listOption = speedList ? speedList.querySelector( '.edminboost-animation-speed-picker__option[data-value="' + speed + '"]' ) : null;
			if ( listOption && listOption.dataset.ms ) {
				return parseInt( listOption.dataset.ms, 10 ) || 300;
			}

			switch ( speed ) {
				case 'fast':
					return 150;
				case 'slow':
					return 500;
				default:
					return 300;
			}
		}

		function resetDrawerPreview( drawer ) {
			if ( ! drawer ) {
				return;
			}

			drawer.classList.remove( 'is-open' );
		}

		function playDrawerPreview( drawer ) {
			if ( ! drawer ) {
				return;
			}

			resetDrawerPreview( drawer );
			void drawer.offsetWidth;
			drawer.classList.add( 'is-open' );
		}

		function closeSpeedList() {
			if ( ! speedList || ! speedToggle ) {
				return;
			}

			clearPreviewTimers();
			speedList.hidden = true;
			speedToggle.setAttribute( 'aria-expanded', 'false' );

			if ( speedList.querySelectorAll ) {
				speedList.querySelectorAll( '.edminboost-animation-speed-picker__preview-drawer' ).forEach( resetDrawerPreview );
			}
		}

		function openSpeedList() {
			if ( ! speedList || ! speedToggle ) {
				return;
			}

			speedList.hidden = false;
			speedToggle.setAttribute( 'aria-expanded', 'true' );
			playListPreviews();
		}

		function toggleSpeedList() {
			if ( ! speedList ) {
				return;
			}

			if ( speedList.hidden ) {
				openSpeedList();
			} else {
				closeSpeedList();
			}
		}

		function playListPreviews() {
			if ( ! speedList ) {
				return;
			}

			var options = speedList.querySelectorAll( '.edminboost-animation-speed-picker__option' );

			options.forEach( function ( option, index ) {
				var drawer = option.querySelector( '.edminboost-animation-speed-picker__preview-drawer' );
				var delay  = index * previewStagger;

				previewTimers.push( window.setTimeout( function () {
					playDrawerPreview( drawer );
				}, delay ) );
			} );
		}

		function syncSpeedPickerSelection( playToggle ) {
			var speed = getSelectedSpeed();
			var ms    = getSpeedMs( speed );

			if ( speedName ) {
				speedName.textContent = getSpeedLabel( speed );
			}

			if ( togglePreview ) {
				togglePreview.style.setProperty( '--edminboost-animation-preview-ms', ms + 'ms' );
			}

			if ( speedList ) {
				speedList.querySelectorAll( '.edminboost-animation-speed-picker__option' ).forEach( function ( option ) {
					var isSelected = option.getAttribute( 'data-value' ) === speed;
					option.classList.toggle( 'is-selected', isSelected );
					option.setAttribute( 'aria-selected', isSelected ? 'true' : 'false' );
				} );
			}

			if ( playToggle ) {
				playDrawerPreview( toggleDrawer );
			}
		}

		function setSelectedSpeed( speed ) {
			if ( ! speedSelect.querySelector( 'option[value="' + speed + '"]' ) ) {
				return;
			}

			speedSelect.value = speed;
			syncSpeedPickerSelection( true );
		}

		if ( speedToggle ) {
			speedToggle.addEventListener( 'click', function () {
				toggleSpeedList();
			} );
		}

		if ( speedList ) {
			speedList.addEventListener( 'click', function ( event ) {
				var option = event.target.closest( '.edminboost-animation-speed-picker__option' );
				if ( ! option ) {
					return;
				}

				setSelectedSpeed( option.getAttribute( 'data-value' ) );
				closeSpeedList();
			} );

			speedList.addEventListener( 'keydown', function ( event ) {
				if ( event.key === 'Escape' ) {
					closeSpeedList();
					if ( speedToggle ) {
						speedToggle.focus();
					}
				}
			} );
		}

		document.addEventListener( 'click', function onSpeedPickerOutsideClick( event ) {
			if ( ! speedPicker || ! document.body.contains( speedPicker ) ) {
				document.removeEventListener( 'click', onSpeedPickerOutsideClick );
				return;
			}

			if ( ! speedPicker.contains( event.target ) ) {
				closeSpeedList();
			}
		} );

		syncSpeedPickerSelection( false );
	}

	function initTheme( root ) {
		var presetSelect   = document.getElementById( 'edminboost_theme_preset' );
		var presetPicker   = document.getElementById( 'edminboost-theme-preset-picker' );
		var presetToggle   = document.getElementById( 'edminboost_theme_preset_toggle' );
		var presetList     = document.getElementById( 'edminboost-theme-preset-list' );
		var presetName     = document.getElementById( 'edminboost-theme-preset-name' );
		var presetSwatches = document.getElementById( 'edminboost-theme-preset-toggle-swatches' );
		var modeSelect     = document.getElementById( 'edminboost_theme_mode' );
		var fontSelect     = document.getElementById( 'edminboost_theme_font' );
		var presetDesc     = document.getElementById( 'edminboost-theme-preset-desc' );
		var customWrap     = document.getElementById( 'edminboost-theme-custom-colors' );
		var themePresets   = edminboostData.themePresets || {};
		var colorKeys      = [ 'accent', 'surface', 'text', 'topbar', 'sidebar', 'content' ];
		var skipLiveThemePreview = document.getElementById( 'edminboost-dashboard-overview-form' );

		if ( ! presetSelect || ! presetPicker ) {
			return;
		}

		var themeClasses = [ 'edminboost-theme-active' ];

		Object.keys( themePresets ).forEach( function ( presetId ) {
			themeClasses.push( 'edminboost-theme--' + presetId );
		} );

		function addThemeClassOptions( element, prefix, fallbackValues ) {
			if ( element && element.options && element.options.length ) {
				Array.prototype.forEach.call( element.options, function ( option ) {
					themeClasses.push( prefix + option.value );
				} );
				return;
			}

			( fallbackValues || [] ).forEach( function ( value ) {
				themeClasses.push( prefix + value );
			} );
		}

		addThemeClassOptions( modeSelect, 'edminboost-theme-mode--', [ 'light', 'dark', 'auto' ] );
		addThemeClassOptions( fontSelect, 'edminboost-theme-font--', [
			'inherit', 'system', 'arial', 'verdana', 'tahoma', 'trebuchet', 'lucida',
			'palatino', 'humanist', 'mono', 'serif', 'rounded'
		] );

		var colorFields = [
			{ picker: 'edminboost_custom_accent_picker', text: 'edminboost_custom_accent', varName: '--eb-accent' },
			{ picker: 'edminboost_custom_surface_picker', text: 'edminboost_custom_surface', varName: '--eb-surface' },
			{ picker: 'edminboost_custom_text_picker', text: 'edminboost_custom_text', varName: '--eb-text' },
			{ picker: 'edminboost_custom_top_picker', text: 'edminboost_custom_top', varName: '--eb-top-bar-bg' },
			{ picker: 'edminboost_custom_sidebar_picker', text: 'edminboost_custom_sidebar', varName: '--eb-sidebar-bg' },
			{ picker: 'edminboost_custom_content_picker', text: 'edminboost_custom_content', varName: '--eb-content-bg' }
		];

		function getSelectedPreset() {
			return presetSelect ? presetSelect.value : 'default';
		}

		function isCustomPreset() {
			return getSelectedPreset() === 'custom';
		}

		function closePresetList() {
			if ( ! presetList || ! presetToggle ) {
				return;
			}

			presetList.hidden = true;
			presetToggle.setAttribute( 'aria-expanded', 'false' );
		}

		function openPresetList() {
			if ( ! presetList || ! presetToggle ) {
				return;
			}

			presetList.hidden = false;
			presetToggle.setAttribute( 'aria-expanded', 'true' );
		}

		function togglePresetList() {
			if ( ! presetList ) {
				return;
			}

			if ( presetList.hidden ) {
				openPresetList();
			} else {
				closePresetList();
			}
		}

		function renderPresetSwatches( container, colors ) {
			if ( ! container ) {
				return;
			}

			container.innerHTML = '';

			colorKeys.forEach( function ( colorKey ) {
				var chip = document.createElement( 'span' );
				chip.className = 'edminboost-theme-preset-picker__chip';
				chip.style.backgroundColor = colors && colors[ colorKey ] ? colors[ colorKey ] : '#ffffff';
				chip.setAttribute( 'aria-hidden', 'true' );
				container.appendChild( chip );
			} );
		}

		function syncPresetPickerSelection() {
			var preset = getSelectedPreset();
			var config = themePresets[ preset ] || themePresets.default || {};
			var mode   = modeSelect ? modeSelect.value : 'light';
			var colors = isCustomPreset()
				? getCustomColorValues()
				: getThemePreviewColors( themePresets, preset, mode );

			if ( presetName ) {
				presetName.textContent = config.name || preset;
			}

			if ( presetDesc ) {
				presetDesc.textContent = config.description || '';
			}

			if ( customWrap ) {
				customWrap.hidden = ! isCustomPreset();
			}

			renderPresetSwatches( presetSwatches, colors );

			if ( presetList ) {
				presetList.querySelectorAll( '.edminboost-theme-preset-picker__option' ).forEach( function ( option ) {
					var optionPreset = option.getAttribute( 'data-value' ) || '';
					var optionSwatches = option.querySelector( '.edminboost-theme-preset-picker__swatches' );

					if ( optionSwatches && optionPreset ) {
						var optionColors = 'custom' === optionPreset && isCustomPreset()
							? getCustomColorValues()
							: getThemePreviewColors( themePresets, optionPreset, mode );
						renderPresetSwatches( optionSwatches, optionColors );
					}

					var isSelected = optionPreset === preset;
					option.classList.toggle( 'is-selected', isSelected );
					option.setAttribute( 'aria-selected', isSelected ? 'true' : 'false' );
				} );
			}
		}

		function getFieldColorValue( textId, fallback ) {
			var input  = document.getElementById( textId );
			var picker = document.getElementById( textId + '_picker' );
			var value  = input && /^#[0-9a-fA-F]{3,6}$/.test( input.value ) ? input.value : '';

			if ( ! value && picker && picker.value ) {
				value = picker.value;
			}

			return value || fallback || '#ffffff';
		}

		function getCustomColorValues() {
			var defaults = themePresets.custom && themePresets.custom.colors ? themePresets.custom.colors : {};

			return {
				accent: getFieldColorValue( 'edminboost_custom_accent', defaults.accent ),
				surface: getFieldColorValue( 'edminboost_custom_surface', defaults.surface ),
				text: getFieldColorValue( 'edminboost_custom_text', defaults.text ),
				topbar: getFieldColorValue( 'edminboost_custom_top', defaults.topbar ),
				sidebar: getFieldColorValue( 'edminboost_custom_sidebar', defaults.sidebar ),
				content: getFieldColorValue( 'edminboost_custom_content', defaults.content )
			};
		}

		function setSelectedPreset( preset ) {
			if ( ! presetSelect || ! themePresets[ preset ] ) {
				return;
			}

			presetSelect.value = preset;
			syncPresetPickerSelection();
			updateThemePreview();
		}

		function applyCustomColors( target ) {
			if ( ! isCustomPreset() ) {
				target.style.removeProperty( '--eb-accent' );
				target.style.removeProperty( '--eb-surface' );
				target.style.removeProperty( '--eb-text' );
				target.style.removeProperty( '--eb-top-bar-bg' );
				target.style.removeProperty( '--eb-sidebar-bg' );
				target.style.removeProperty( '--eb-content-bg' );
				target.style.removeProperty( '--eb-badge-accent' );
				target.style.removeProperty( '--eb-drawer-panel-bg' );
				target.style.removeProperty( '--eb-drawer-header-text' );
				return;
			}

			colorFields.forEach( function ( field ) {
				var input = document.getElementById( field.text );
				var value = input && /^#[0-9a-fA-F]{3,6}$/.test( input.value ) ? input.value : '';
				if ( value ) {
					target.style.setProperty( field.varName, value );
					if ( field.varName === '--eb-accent' ) {
						target.style.setProperty( '--eb-badge-accent', value );
					}
					if ( field.varName === '--eb-surface' ) {
						target.style.setProperty( '--eb-drawer-panel-bg', value );
					}
					if ( field.varName === '--eb-text' ) {
						target.style.setProperty( '--eb-drawer-header-text', value );
					}
					if ( field.varName === '--eb-content-bg' ) {
						target.style.setProperty( '--eb-drawer-panel-bg', value );
					}
				}
			} );
		}

		function updateThemePreview() {
			if ( skipLiveThemePreview ) {
				syncPresetPickerSelection();
				return;
			}

			var preset = getSelectedPreset();
			var mode   = modeSelect ? modeSelect.value : 'light';
			var font   = fontSelect ? fontSelect.value : 'inherit';
			var body   = document.body;

			themeClasses.forEach( function ( className ) {
				body.classList.remove( className );
			} );

			body.classList.add( 'edminboost-theme-active' );
			body.classList.add( 'edminboost-theme--' + preset );
			body.classList.add( 'edminboost-theme-mode--' + mode );
			body.classList.add( 'edminboost-theme-font--' + font );

			applyCustomColors( body );
			syncPresetPickerSelection();
		}

		function bindColorField( field ) {
			var picker = document.getElementById( field.picker );
			var text   = document.getElementById( field.text );

			if ( ! picker || ! text ) {
				return;
			}

			picker.addEventListener( 'input', function () {
				text.value = picker.value;
				updateThemePreview();
			} );

			text.addEventListener( 'input', function () {
				if ( /^#[0-9a-fA-F]{6}$/.test( text.value ) ) {
					picker.value = text.value;
					updateThemePreview();
				}
			} );
		}

		if ( presetToggle ) {
			presetToggle.addEventListener( 'click', function () {
				togglePresetList();
			} );
		}

		if ( presetList ) {
			presetList.addEventListener( 'click', function ( event ) {
				var option = event.target.closest( '.edminboost-theme-preset-picker__option' );
				if ( ! option ) {
					return;
				}

				setSelectedPreset( option.getAttribute( 'data-value' ) );
				closePresetList();
			} );

			presetList.addEventListener( 'keydown', function ( event ) {
				if ( event.key === 'Escape' ) {
					closePresetList();
					if ( presetToggle ) {
						presetToggle.focus();
					}
				}
			} );
		}

		document.addEventListener( 'click', function onThemePickerOutsideClick( event ) {
			if ( ! presetPicker || ! document.body.contains( presetPicker ) ) {
				document.removeEventListener( 'click', onThemePickerOutsideClick );
				return;
			}

			if ( ! presetPicker.contains( event.target ) ) {
				closePresetList();
			}
		} );

		if ( modeSelect && modeSelect.tagName === 'SELECT' ) {
			modeSelect.addEventListener( 'change', updateThemePreview );
		}

		if ( fontSelect && fontSelect.tagName === 'SELECT' ) {
			fontSelect.addEventListener( 'change', updateThemePreview );
		}

		colorFields.forEach( bindColorField );
		updateThemePreview();
	}

	function initCommandCenterForms( root ) {
		initAjaxSaveForms( root, '.edminboost-cc-form' );
	}

	function initSettingsForm( root ) {
		initAjaxSaveForms( root, '.edminboost-settings-form' );
	}

	function initAjaxSaveForms( root, selector ) {
		var forms = root.querySelectorAll( selector );

		if ( ! forms.length || ! edminboostData.settingsSave ) {
			return;
		}

		forms.forEach( function ( form ) {
			form.addEventListener( 'submit', function ( event ) {
				event.preventDefault();
				saveSettingsForm( form );
			} );
		} );
	}

	function applySettingsSaveResult( data, form ) {
		if ( ! data ) {
			return;
		}

		if ( data.presets ) {
			edminboostData.presets = data.presets;

			if ( typeof syncPresetCatalogFn === 'function' ) {
				syncPresetCatalogFn(
					data.presets,
					data.selected_preset || '',
					data.default_preset || ''
				);
			}
		}

		if ( data.setup_complete && form && form.id === 'edminboost-setup-wizard-form' && loadCommandCenterPageRef ) {
			loadCommandCenterPageRef(
				edminboostData.currentPage,
				window.location.href,
				{ push: false }
			);
		}
	}

	function saveSettingsForm( form, options ) {
		options = options || {};
		var submitBtn  = options.submitBtn || form.querySelector( '[type="submit"]' );
		var saveConfig = edminboostData.settingsSave;

		if ( typeof form.edminboostSyncHiddenInputs === 'function' ) {
			form.edminboostSyncHiddenInputs();
		}

		var formData   = new FormData( form );

		formData.append( 'action', saveConfig.action );

		if ( submitBtn ) {
			submitBtn.disabled = true;
			submitBtn.classList.add( 'is-saving' );
		}

		window.fetch( saveConfig.ajaxUrl, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin'
		} )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( payload ) {
				if ( ! payload.success ) {
					var errorMessage = payload.data && payload.data.message
						? payload.data.message
						: edminboostData.strings.settingsSaveFailed;

					throw new Error( errorMessage );
				}

				showFormNotice(
					form,
					'success',
					options.message
						|| ( payload.data && payload.data.message
							? payload.data.message
							: edminboostData.strings.settingsSaved )
				);

				applySettingsSaveResult( payload.data || {}, form );

				if ( typeof options.onSuccess === 'function' ) {
					options.onSuccess( payload.data || {}, form );
				}
			} )
			.catch( function ( error ) {
				showFormNotice(
					form,
					'error',
					error.message || edminboostData.strings.settingsSaveFailed
				);
			} )
			.finally( function () {
				if ( submitBtn ) {
					submitBtn.disabled = false;
					submitBtn.classList.remove( 'is-saving' );
				}
			} );
	}

	function showFormNotice( form, type, message ) {
		var existing = form.querySelector( '.edminboost-save-notice' );

		if ( existing ) {
			existing.remove();
		}

		var notice = document.createElement( 'div' );
		notice.className = 'notice notice-' + type + ' is-dismissible edminboost-save-notice';
		notice.setAttribute( 'role', 'alert' );

		var paragraph = document.createElement( 'p' );
		paragraph.textContent = message;
		notice.appendChild( paragraph );

		form.insertBefore( notice, form.firstChild );
		notice.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );

		if ( type === 'success' ) {
			window.setTimeout( function () {
				if ( notice.parentNode ) {
					notice.remove();
				}
			}, 4000 );
		}
	}

	function initLayoutPresetPicker( root ) {
		var presetSelect   = document.getElementById( 'edminboost_layout_preset' );
		var presetPicker   = document.getElementById( 'edminboost-layout-preset-picker' );
		var presetToggle   = document.getElementById( 'edminboost_layout_preset_toggle' );
		var presetList     = document.getElementById( 'edminboost-layout-preset-list' );
		var presetName     = document.getElementById( 'edminboost-layout-preset-name' );
		var presetBadge    = document.getElementById( 'edminboost-layout-preset-badge' );
		var presetDesc     = document.getElementById( 'edminboost-layout-preset-desc' );
		var defaultField   = document.getElementById( 'edminboost_layout_default_preset' );
		var defaultCheckbox = document.getElementById( 'edminboost_layout_preset_default_checkbox' );
		var previewRoot    = root.querySelector( '.edminboost-preset-picker .edminboost-overview-topbar-preview' );
		var sidebarPreviewRoot = root.querySelector( '.edminboost-preset-picker .edminboost-overview-sidebar-preview' )
			|| root.querySelector( '.edminboost-layout-preset-previews .edminboost-overview-sidebar-preview' )
			|| document.getElementById( 'edminboost-overview-layout-sidebar-preview' );
		var presetCatalog  = edminboostData.presets || {};
		var badgeBuiltIn   = edminboostData.strings.presetBadgeBuiltIn || 'Built-in';
		var badgeSaved     = edminboostData.strings.presetBadgeSaved || 'Saved';
		var badgeVirtual   = edminboostData.strings.presetBadgeVirtual || 'Layout';

		if ( ! presetSelect || ! presetPicker ) {
			return;
		}

		function getSelectedPreset() {
			return presetSelect.value || '';
		}

		function isSystemPreset( presetId ) {
			var config = presetCatalog[ presetId ] || {};
			return !! config.system;
		}

		function closePresetList() {
			if ( ! presetList || ! presetToggle ) {
				return;
			}

			presetList.hidden = true;
			presetToggle.setAttribute( 'aria-expanded', 'false' );
		}

		function openPresetList() {
			if ( ! presetList || ! presetToggle ) {
				return;
			}

			presetList.hidden = false;
			presetToggle.setAttribute( 'aria-expanded', 'true' );
		}

		function togglePresetList() {
			if ( ! presetList ) {
				return;
			}

			if ( presetList.hidden ) {
				openPresetList();
			} else {
				closePresetList();
			}
		}

		function renderSidebarPreviewNode( previewRoot, items, limit ) {
			if ( ! previewRoot ) {
				return;
			}

			limit    = limit || 8;
			var visible  = [];
			var overflow = 0;

			( items || [] ).forEach( function ( item ) {
				if ( ! item || ! item.slug ) {
					return;
				}

				if ( visible.length >= limit ) {
					overflow += 1;
					return;
				}

				visible.push( item );
			} );

			previewRoot.classList.toggle( 'edminboost-overview-sidebar-preview--empty', ! visible.length );

			if ( ! visible.length ) {
				previewRoot.innerHTML = '<p class="edminboost-overview-sidebar-preview__empty">' +
					( edminboostData.strings.emptySidebarPreview || 'No sidebar items in this preview yet.' ) +
					'</p>';
				return;
			}

			var list = document.createElement( 'ul' );
			list.className = 'edminboost-overview-sidebar-preview__list';
			list.setAttribute( 'aria-hidden', 'true' );

			visible.forEach( function ( item ) {
				var li = document.createElement( 'li' );
				li.className = 'edminboost-overview-sidebar-preview__item';

				var icon = document.createElement( 'span' );
				icon.className = 'dashicons ' + ( item.icon || 'dashicons-admin-generic' );
				icon.setAttribute( 'aria-hidden', 'true' );
				icon.title = item.label || item.slug || '';

				var label = document.createElement( 'span' );
				label.className = 'edminboost-overview-sidebar-preview__label';
				label.textContent = item.label || item.slug || '';

				li.appendChild( icon );
				li.appendChild( label );
				list.appendChild( li );
			} );

			if ( overflow > 0 ) {
				var more = document.createElement( 'li' );
				more.className = 'edminboost-overview-sidebar-preview__more';
				more.textContent = '+' + overflow + ' more';
				list.appendChild( more );
			}

			previewRoot.innerHTML = '';
			previewRoot.appendChild( list );
		}

		function renderTopBarPreview( presetId ) {
			if ( ! previewRoot ) {
				return;
			}

			var isCompact = previewRoot.classList.contains( 'edminboost-overview-topbar-preview--compact' );
			var preset = presetCatalog[ presetId ] || {};
			var items  = preset.top_bar_items || [];
			var limit  = isCompact ? 10 : 6;
			var visible = [];
			var overflow = 0;

			items.forEach( function ( item ) {
				if ( ! item || ! item.slug ) {
					return;
				}

				if ( visible.length >= limit ) {
					overflow += 1;
					return;
				}

				visible.push( item );
			} );

			previewRoot.classList.toggle( 'edminboost-overview-topbar-preview--compact', isCompact );
			previewRoot.classList.toggle( 'edminboost-overview-topbar-preview--empty', ! visible.length );

			if ( ! visible.length ) {
				previewRoot.innerHTML = '<p class="edminboost-overview-topbar-preview__empty">' +
					( edminboostData.strings.emptyLayoutPreview || 'No links in this preview yet.' ) +
					'</p>';
				return;
			}

			var canvas = document.createElement( 'div' );
			canvas.className = 'edminboost-overview-topbar-preview__canvas';

			var previewStrings = getOverviewPreviewStrings();

			var brand = createTopBarPreviewTip( previewStrings.wordpressLogo, 'dashicons-wordpress' );
			brand.classList.add( 'edminboost-overview-topbar-preview__brand' );
			canvas.appendChild( brand );

			var list = document.createElement( 'ul' );
			list.className = 'edminboost-overview-topbar-preview__items';

			visible.forEach( function ( item ) {
				var li = document.createElement( 'li' );
				var interaction = item.interaction || 'redirect';
				var itemLabel = item.label || item.slug || '';
				li.className = 'edminboost-overview-topbar-preview__item ' +
					( interaction === 'drawer' ? 'is-drawer' : 'is-direct' );

				li.appendChild( createTopBarPreviewTip( itemLabel, item.icon || 'dashicons-admin-generic' ) );

				var label = document.createElement( 'span' );
				label.className = 'edminboost-overview-topbar-preview__label';
				label.textContent = itemLabel;

				li.appendChild( label );
				list.appendChild( li );
			} );

			canvas.appendChild( list );

			if ( overflow > 0 ) {
				var more = document.createElement( 'span' );
				more.className = 'edminboost-overview-topbar-preview__more';
				more.textContent = '+' + overflow;
				more.title = overflow === 1 ? '1 more link' : overflow + ' more links';
				canvas.appendChild( more );
			}

			var profile = createTopBarPreviewTip( previewStrings.profile, 'dashicons-admin-users' );
			profile.classList.add( 'edminboost-overview-topbar-preview__profile' );
			canvas.appendChild( profile );

			previewRoot.innerHTML = '';
			previewRoot.appendChild( canvas );
		}

		function renderSidebarPreview( presetId ) {
			if ( ! sidebarPreviewRoot ) {
				return;
			}

			var preset = presetCatalog[ presetId ] || {};
			renderSidebarPreviewNode( sidebarPreviewRoot, preset.sidebar_items || [], 5 );
		}

		function syncDefaultCheckbox() {
			if ( ! defaultCheckbox || ! defaultField ) {
				return;
			}

			var selected = getSelectedPreset();
			defaultCheckbox.checked = selected === 'default' || defaultField.value === selected;
		}

		function syncPresetPickerSelection() {
			var preset = getSelectedPreset();
			var config = presetCatalog[ preset ] || {};
			var system = isSystemPreset( preset );
			var virtual = !! config.virtual;

			if ( presetName ) {
				presetName.textContent = config.name || preset;
			}

			if ( presetBadge ) {
				if ( virtual ) {
					presetBadge.textContent = badgeVirtual;
				} else {
					presetBadge.textContent = system ? badgeBuiltIn : badgeSaved;
				}
			}

			if ( presetDesc ) {
				presetDesc.textContent = config.description || '';
			}

			if ( presetList ) {
				presetList.querySelectorAll( '.edminboost-layout-preset-picker__option' ).forEach( function ( option ) {
					var isSelected = option.getAttribute( 'data-value' ) === preset;
					option.classList.toggle( 'is-selected', isSelected );
					option.setAttribute( 'aria-selected', isSelected ? 'true' : 'false' );
				} );
			}

			renderTopBarPreview( preset );
			renderSidebarPreview( preset );
			syncDefaultCheckbox();
		}

		function setSelectedPreset( preset ) {
			if ( ! presetSelect || ! presetCatalog[ preset ] ) {
				return;
			}

			presetSelect.value = preset;
			syncPresetPickerSelection();
			presetSelect.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		}

		function getPresetDisplayOrder() {
			var pickerRoot = presetSelect.closest( '.edminboost-preset-picker' );
			var isWizard = pickerRoot && pickerRoot.classList.contains( 'edminboost-preset-picker--wizard' );

			return isWizard ? [ 'scenario', 'workflow' ] : [ 'source', 'scenario', 'workflow', 'saved' ];
		}

		function groupPresetsForPicker( catalog ) {
			var displayOrder = getPresetDisplayOrder();
			var grouped = {};
			var pickerRoot = presetSelect.closest( '.edminboost-preset-picker' );
			var isWizard = pickerRoot && pickerRoot.classList.contains( 'edminboost-preset-picker--wizard' );

			displayOrder.forEach( function ( categoryId ) {
				grouped[ categoryId ] = {};
			} );

			Object.keys( catalog ).forEach( function ( presetId ) {
				var preset = catalog[ presetId ];

				if ( isWizard && ! preset.system ) {
					return;
				}

				var categoryId = preset.category || ( preset.system ? 'workflow' : 'saved' );

				if ( ! grouped[ categoryId ] ) {
					grouped[ categoryId ] = {};
				}

				grouped[ categoryId ][ presetId ] = preset;
			} );

			return {
				order: displayOrder,
				groups: grouped
			};
		}

		function createPresetListOption( presetId, preset, isSelected ) {
			var isSystem = !! preset.system;
			var isVirtual = !! preset.virtual;
			var presetNameText = preset.name || presetId;
			var presetDescText = preset.description || '';
			var badgeLabel = isVirtual ? badgeVirtual : ( isSystem ? badgeBuiltIn : badgeSaved );
			var li = document.createElement( 'li' );

			li.className = 'edminboost-layout-preset-picker__option' + ( isSelected ? ' is-selected' : '' );
			li.setAttribute( 'role', 'option' );
			li.setAttribute( 'tabindex', '-1' );
			li.setAttribute( 'data-value', presetId );
			li.setAttribute( 'data-system', isSystem ? '1' : '0' );
			li.setAttribute( 'aria-selected', isSelected ? 'true' : 'false' );

			var main = document.createElement( 'span' );
			main.className = 'edminboost-layout-preset-picker__option-main';

			var nameSpan = document.createElement( 'span' );
			nameSpan.className = 'edminboost-layout-preset-picker__option-name';
			nameSpan.textContent = presetNameText;

			var badgeSpan = document.createElement( 'span' );
			badgeSpan.className = 'edminboost-layout-preset-picker__option-badge';
			badgeSpan.textContent = badgeLabel;

			main.appendChild( nameSpan );
			main.appendChild( badgeSpan );
			li.appendChild( main );

			if ( presetDescText ) {
				var descSpan = document.createElement( 'span' );
				descSpan.className = 'edminboost-layout-preset-picker__option-desc';
				descSpan.textContent = presetDescText;
				li.appendChild( descSpan );
			}

			return li;
		}

		function rebuildPresetPickerOptions( catalog, selectedPreset ) {
			if ( ! presetSelect || ! presetList ) {
				return;
			}

			var categories = edminboostData.presetCategories || {};
			var groupedData = groupPresetsForPicker( catalog );
			var currentValue = selectedPreset || getSelectedPreset();
			var hasCurrent = !! catalog[ currentValue ];

			presetSelect.innerHTML = '';

			groupedData.order.forEach( function ( categoryId ) {
				var presetsInGroup = groupedData.groups[ categoryId ] || {};
				var presetIds = Object.keys( presetsInGroup );

				if ( ! presetIds.length ) {
					return;
				}

				var optgroup = document.createElement( 'optgroup' );
				optgroup.label = categories[ categoryId ] || categoryId;

				presetIds.forEach( function ( presetId ) {
					var preset = presetsInGroup[ presetId ];
					var option = document.createElement( 'option' );
					option.value = presetId;
					option.textContent = preset.name || presetId;
					option.setAttribute( 'data-system', preset.system ? '1' : '0' );

					if ( hasCurrent && currentValue === presetId ) {
						option.selected = true;
					}

					optgroup.appendChild( option );
				} );

				presetSelect.appendChild( optgroup );
			} );

			presetList.innerHTML = '';

			groupedData.order.forEach( function ( categoryId ) {
				var presetsInGroup = groupedData.groups[ categoryId ] || {};
				var presetIds = Object.keys( presetsInGroup );

				if ( ! presetIds.length ) {
					return;
				}

				var groupItem = document.createElement( 'li' );
				groupItem.className = 'edminboost-layout-preset-picker__group';
				groupItem.setAttribute( 'role', 'presentation' );

				var groupLabel = document.createElement( 'span' );
				groupLabel.className = 'edminboost-layout-preset-picker__group-label';
				groupLabel.id = 'edminboost-layout-preset-group-' + categoryId;
				groupLabel.textContent = categories[ categoryId ] || categoryId;
				groupItem.appendChild( groupLabel );

				var groupList = document.createElement( 'ul' );
				groupList.className = 'edminboost-layout-preset-picker__group-list';
				groupList.setAttribute( 'role', 'group' );
				groupList.setAttribute( 'aria-labelledby', groupLabel.id );

				presetIds.forEach( function ( presetId ) {
					var preset = presetsInGroup[ presetId ];
					groupList.appendChild(
						createPresetListOption( presetId, preset, hasCurrent && currentValue === presetId )
					);
				} );

				groupItem.appendChild( groupList );
				presetList.appendChild( groupItem );
			} );

			if ( hasCurrent ) {
				presetSelect.value = currentValue;
			}
		}

		syncPresetCatalogFn = function ( catalog, selectedPreset, defaultPreset ) {
			presetCatalog = catalog || {};

			if ( selectedPreset && presetCatalog[ selectedPreset ] ) {
				rebuildPresetPickerOptions( presetCatalog, selectedPreset );
				setSelectedPreset( selectedPreset );
			} else {
				rebuildPresetPickerOptions( presetCatalog, getSelectedPreset() );
				syncPresetPickerSelection();
			}

			if ( defaultField && defaultPreset ) {
				defaultField.value = defaultPreset;

				if ( defaultCheckbox ) {
					defaultCheckbox.checked = defaultField.value === getSelectedPreset();
				}
			}
		};

		if ( presetToggle ) {
			presetToggle.addEventListener( 'click', function () {
				togglePresetList();
			} );
		}

		if ( presetList ) {
			presetList.addEventListener( 'click', function ( event ) {
				var option = event.target.closest( '.edminboost-layout-preset-picker__option' );
				if ( ! option ) {
					return;
				}

				setSelectedPreset( option.getAttribute( 'data-value' ) || '' );
				closePresetList();
			} );

			presetList.addEventListener( 'keydown', function ( event ) {
				if ( event.key === 'Escape' ) {
					closePresetList();
					if ( presetToggle ) {
						presetToggle.focus();
					}
				}
			} );
		}

		document.addEventListener( 'click', function onLayoutPickerOutsideClick( event ) {
			if ( ! presetPicker || ! document.body.contains( presetPicker ) ) {
				document.removeEventListener( 'click', onLayoutPickerOutsideClick );
				return;
			}

			if ( ! presetPicker.contains( event.target ) ) {
				closePresetList();
			}
		} );

		if ( defaultCheckbox && defaultField ) {
			defaultCheckbox.addEventListener( 'change', function () {
				if ( ! defaultCheckbox.checked ) {
					return;
				}

				var selected = getSelectedPreset();
				if ( selected && 'default' !== selected ) {
					defaultField.value = selected;
				}
			} );
		}

		presetSelect.addEventListener( 'change', syncPresetPickerSelection );
		syncPresetPickerSelection();
	}

	function initDashboardOverview( root ) {
		var form = document.getElementById( 'edminboost-dashboard-overview-form' );

		if ( ! form ) {
			return;
		}

		var applyField         = document.getElementById( 'edminboost_dashboard_apply_preset' );
		var layoutList         = form.querySelector( '#edminboost-layout-preset-list' );
		var themeList          = form.querySelector( '#edminboost-theme-preset-list' );
		var topbarToggle       = document.getElementById( 'edminboost_overview_topbar_links_toggle' );
		var topbarList         = document.getElementById( 'edminboost-overview-topbar-links-list' );
		var topbarLinksPicker  = document.getElementById( 'edminboost-overview-topbar-links-picker' );
		var layoutSidebarPreview = document.getElementById( 'edminboost-overview-layout-sidebar-preview' );
		var themePreview       = document.getElementById( 'edminboost-overview-theme-preview' );
		var topbarPreview      = document.getElementById( 'edminboost-overview-topbar-preview' );
		var topbarDesc         = document.getElementById( 'edminboost-overview-topbar-desc' );
		var topbarSummary      = document.getElementById( 'edminboost-overview-topbar-links-summary' );
		var presetCatalog      = edminboostData.presets || {};
		var themePresets       = edminboostData.themePresets || {};
		var strings            = edminboostData.strings || {};
		var dashboardSaving    = false;

		function closeTopbarLinksList() {
			if ( ! topbarList || ! topbarToggle ) {
				return;
			}

			topbarList.hidden = true;
			topbarToggle.setAttribute( 'aria-expanded', 'false' );
		}

		function openTopbarLinksList() {
			if ( ! topbarList || ! topbarToggle ) {
				return;
			}

			topbarList.hidden = false;
			topbarToggle.setAttribute( 'aria-expanded', 'true' );
		}

		function toggleTopbarLinksList() {
			if ( ! topbarList ) {
				return;
			}

			if ( topbarList.hidden ) {
				openTopbarLinksList();
			} else {
				closeTopbarLinksList();
			}
		}

		function renderSidebarPreviewNode( previewRoot, items, limit ) {
			if ( ! previewRoot ) {
				return;
			}

			limit    = limit || 8;
			var visible  = [];
			var overflow = 0;

			( items || [] ).forEach( function ( item ) {
				if ( ! item || ! item.slug ) {
					return;
				}

				if ( visible.length >= limit ) {
					overflow += 1;
					return;
				}

				visible.push( item );
			} );

			previewRoot.classList.toggle( 'edminboost-overview-sidebar-preview--empty', ! visible.length );

			if ( ! visible.length ) {
				previewRoot.innerHTML = '<p class="edminboost-overview-sidebar-preview__empty">' +
					( strings.emptySidebarPreview || 'No sidebar items in this preview yet.' ) +
					'</p>';
				return;
			}

			var list = document.createElement( 'ul' );
			list.className = 'edminboost-overview-sidebar-preview__list';
			list.setAttribute( 'aria-hidden', 'true' );

			visible.forEach( function ( item ) {
				var li = document.createElement( 'li' );
				li.className = 'edminboost-overview-sidebar-preview__item';

				var icon = document.createElement( 'span' );
				icon.className = 'dashicons ' + ( item.icon || 'dashicons-admin-generic' );
				icon.setAttribute( 'aria-hidden', 'true' );
				icon.title = item.label || item.slug || '';

				var label = document.createElement( 'span' );
				label.className = 'edminboost-overview-sidebar-preview__label';
				label.textContent = item.label || item.slug || '';

				li.appendChild( icon );
				li.appendChild( label );
				list.appendChild( li );
			} );

			if ( overflow > 0 ) {
				var more = document.createElement( 'li' );
				more.className = 'edminboost-overview-sidebar-preview__more';
				more.textContent = '+' + overflow + ' more';
				list.appendChild( more );
			}

			previewRoot.innerHTML = '';
			previewRoot.appendChild( list );
		}

		function renderTopBarPreviewNode( previewRoot, items, options ) {
			if ( ! previewRoot ) {
				return;
			}

			options  = options || {};
			var isCompact = !! options.compact;
			var limit    = options.limit || ( isCompact ? 10 : 6 );
			var visible  = [];
			var overflow = 0;

			( items || [] ).forEach( function ( item ) {
				if ( ! item || ! item.slug ) {
					return;
				}

				if ( visible.length >= limit ) {
					overflow += 1;
					return;
				}

				visible.push( item );
			} );

			previewRoot.classList.toggle( 'edminboost-overview-topbar-preview--compact', isCompact );
			previewRoot.classList.toggle( 'edminboost-overview-topbar-preview--empty', ! visible.length );

			if ( ! visible.length ) {
				previewRoot.innerHTML = '<p class="edminboost-overview-topbar-preview__empty">' +
					( strings.emptyLayoutPreview || 'No links in this preview yet.' ) +
					'</p>';
				return;
			}

			var canvas = document.createElement( 'div' );
			canvas.className = 'edminboost-overview-topbar-preview__canvas';

			var previewStrings = getOverviewPreviewStrings();

			var brand = createTopBarPreviewTip( previewStrings.wordpressLogo, 'dashicons-wordpress' );
			brand.classList.add( 'edminboost-overview-topbar-preview__brand' );
			canvas.appendChild( brand );

			var list = document.createElement( 'ul' );
			list.className = 'edminboost-overview-topbar-preview__items';

			visible.forEach( function ( item ) {
				var li = document.createElement( 'li' );
				var interaction = item.interaction || 'redirect';
				var itemLabel = item.label || item.slug || '';
				li.className = 'edminboost-overview-topbar-preview__item ' +
					( interaction === 'drawer' ? 'is-drawer' : 'is-direct' );

				li.appendChild( createTopBarPreviewTip( itemLabel, item.icon || 'dashicons-admin-generic' ) );

				var label = document.createElement( 'span' );
				label.className = 'edminboost-overview-topbar-preview__label';
				label.textContent = itemLabel;

				li.appendChild( label );
				list.appendChild( li );
			} );

			canvas.appendChild( list );

			if ( overflow > 0 ) {
				var more = document.createElement( 'span' );
				more.className = 'edminboost-overview-topbar-preview__more';
				more.textContent = '+' + overflow;
				more.title = overflow === 1 ? '1 more link' : overflow + ' more links';
				canvas.appendChild( more );
			}

			var profile = createTopBarPreviewTip( previewStrings.profile, 'dashicons-admin-users' );
			profile.classList.add( 'edminboost-overview-topbar-preview__profile' );
			canvas.appendChild( profile );

			previewRoot.innerHTML = '';
			previewRoot.appendChild( canvas );
		}

		function buildTopBarDescription( items ) {
			var redirectCount = 0;
			var drawerCount   = 0;

			( items || [] ).forEach( function ( item ) {
				if ( ! item ) {
					return;
				}

				if ( 'drawer' === ( item.interaction || 'redirect' ) ) {
					drawerCount += 1;
				} else {
					redirectCount += 1;
				}
			} );

			if ( ! items || ! items.length ) {
				return 'Admin link shortcuts can appear in your WordPress top bar. Add links in the Top Bar editor and choose whether each opens directly or in a slide-out drawer.';
			}

			var parts = [ 'Admin link shortcuts appear in your WordPress top bar.' ];

			if ( redirectCount > 0 ) {
				parts.push(
					redirectCount === 1
						? '1 opens directly'
						: redirectCount + ' open directly'
				);
			}

			if ( drawerCount > 0 ) {
				parts.push(
					drawerCount === 1
						? '1 opens in a slide-out drawer'
						: drawerCount + ' open in a slide-out drawer'
				);
			}

			return parts.join( ' ' ) + ( parts.length > 1 ? '.' : '' );
		}

		function rebuildTopbarLinksList( items ) {
			if ( ! topbarList || ! topbarSummary ) {
				return;
			}

			var count = items ? items.length : 0;
			topbarSummary.textContent = count === 0
				? 'No links configured'
				: count === 1
					? '1 link configured'
					: count + ' links configured';

			topbarList.innerHTML = '';

			if ( ! count ) {
				var emptyItem = document.createElement( 'li' );
				emptyItem.className = 'edminboost-overview-topbar-links-picker__empty';
				emptyItem.setAttribute( 'role', 'presentation' );
				emptyItem.textContent = 'Add links in the Top Bar editor to see them here.';
				topbarList.appendChild( emptyItem );
				return;
			}

			items.forEach( function ( item ) {
				var li = document.createElement( 'li' );
				li.className = 'edminboost-overview-topbar-links-picker__option';
				li.setAttribute( 'role', 'option' );
				li.setAttribute( 'tabindex', '-1' );
				li.setAttribute( 'aria-selected', 'false' );

				var main = document.createElement( 'span' );
				main.className = 'edminboost-overview-topbar-links-picker__option-main';

				var icon = document.createElement( 'span' );
				icon.className = 'dashicons ' + ( item.icon || 'dashicons-admin-generic' );
				icon.setAttribute( 'aria-hidden', 'true' );

				var name = document.createElement( 'span' );
				name.className = 'edminboost-overview-topbar-links-picker__option-name';
				name.textContent = item.label || item.slug || '';

				main.appendChild( icon );
				main.appendChild( name );

				var meta = document.createElement( 'span' );
				meta.className = 'edminboost-overview-topbar-links-picker__option-meta';
				meta.textContent = 'drawer' === ( item.interaction || 'redirect' )
					? 'Opens in drawer'
					: 'Opens directly';

				li.appendChild( main );
				li.appendChild( meta );
				topbarList.appendChild( li );
			} );
		}

		function getDashboardThemeMode() {
			var modeField = document.getElementById( 'edminboost_theme_mode' );
			return modeField ? modeField.value : 'light';
		}

		function updateThemeOverviewPreview( presetId ) {
			if ( ! themePreview ) {
				return;
			}

			var colors = getThemePreviewColors( themePresets, presetId, getDashboardThemeMode() );

			applyThemePreviewColorVars( themePreview, colors );

			var swatches = themePreview.querySelectorAll( '.edminboost-overview-theme-preview__swatch' );
			var colorKeys = [ 'accent', 'surface', 'text', 'topbar', 'sidebar', 'content' ];

			swatches.forEach( function ( swatch, index ) {
				var colorKey = colorKeys[ index ];
				if ( colorKey && colors[ colorKey ] ) {
					swatch.style.backgroundColor = colors[ colorKey ];
				}
			} );
		}

		function syncLayoutOverview( preset ) {
			var items        = preset.top_bar_items || [];
			var sidebarItems = preset.sidebar_items || [];

			renderSidebarPreviewNode( layoutSidebarPreview, sidebarItems, 5 );
			syncTopBarOverview( items );
		}

		function syncTopBarOverview( items ) {
			renderTopBarPreviewNode( topbarPreview, items, { compact: true } );

			if ( topbarDesc ) {
				topbarDesc.textContent = buildTopBarDescription( items );
			}

			rebuildTopbarLinksList( items );
		}

		function saveDashboardOverview( options ) {
			if ( dashboardSaving ) {
				return;
			}

			dashboardSaving = true;

			saveSettingsForm( form, {
				message: options && options.message ? options.message : undefined,
				onSuccess: function ( data ) {
					dashboardSaving = false;

					if ( applyField ) {
						applyField.value = '';
					}

					if ( options && typeof options.onSuccess === 'function' ) {
						options.onSuccess( data || {} );
					}
				}
			} );

			window.setTimeout( function () {
				dashboardSaving = false;
			}, 4000 );
		}

		if ( layoutList ) {
			layoutList.addEventListener( 'click', function ( event ) {
				var option = event.target.closest( '.edminboost-layout-preset-picker__option' );
				if ( ! option ) {
					return;
				}

				var presetId = option.getAttribute( 'data-value' ) || '';
				if ( ! presetId || ! presetCatalog[ presetId ] ) {
					return;
				}

				window.setTimeout( function () {
					if ( applyField ) {
						applyField.value = presetId;
					}

					saveDashboardOverview( {
						message: strings.presetApplied || 'Preset applied.',
						onSuccess: function () {
							window.location.reload();
						}
					} );
				}, 0 );
			} );
		}

		if ( themeList ) {
			themeList.addEventListener( 'click', function ( event ) {
				var option = event.target.closest( '.edminboost-theme-preset-picker__option' );
				if ( ! option ) {
					return;
				}

				var presetId = option.getAttribute( 'data-value' ) || '';
				if ( ! presetId || ! themePresets[ presetId ] ) {
					return;
				}

				window.setTimeout( function () {
					updateThemeOverviewPreview( presetId );
					saveDashboardOverview();
				}, 0 );
			} );
		}

		if ( topbarToggle ) {
			topbarToggle.addEventListener( 'click', function () {
				toggleTopbarLinksList();
			} );
		}

		if ( topbarList ) {
			topbarList.addEventListener( 'keydown', function ( event ) {
				if ( event.key === 'Escape' ) {
					closeTopbarLinksList();
					if ( topbarToggle ) {
						topbarToggle.focus();
					}
				}
			} );
		}

		document.addEventListener( 'click', function onDashboardTopbarOutsideClick( event ) {
			if ( ! topbarLinksPicker || ! document.body.contains( topbarLinksPicker ) ) {
				document.removeEventListener( 'click', onDashboardTopbarOutsideClick );
				return;
			}

			if ( ! topbarLinksPicker.contains( event.target ) ) {
				closeTopbarLinksList();
			}
		} );

		var themePresetSelect = document.getElementById( 'edminboost_theme_preset' );
		if ( themePresetSelect ) {
			updateThemeOverviewPreview( themePresetSelect.value );
		}
	}

	function initSetupWizard( root ) {
		var form         = document.getElementById( 'edminboost-setup-wizard-form' );
		var backBtn      = document.getElementById( 'edminboost-setup-back' );
		var nextBtn      = document.getElementById( 'edminboost-setup-next' );
		var submitBtn    = document.getElementById( 'edminboost-setup-submit' );
		var applyField   = document.getElementById( 'edminboost_wizard_apply_preset' );
		var defaultField = document.getElementById( 'edminboost_wizard_default_preset' );
		var summaryRoot  = document.getElementById( 'edminboost-wizard-topbar-summary' );
		var summaryList  = document.getElementById( 'edminboost-wizard-topbar-summary-list' );
		var layoutSelect = document.getElementById( 'edminboost_layout_preset' );
		var stepperItems = root.querySelectorAll( '.edminboost-setup-stepper__item' );
		var steps        = root.querySelectorAll( '.edminboost-setup-step' );
		var presetCatalog = edminboostData.presets || {};
		var themePresets  = edminboostData.themePresets || {};
		var currentStep   = 1;
		var totalSteps    = 4;

		if ( ! form || ! nextBtn ) {
			return;
		}

		function getSelectedLayoutPreset() {
			return layoutSelect ? layoutSelect.value : '';
		}

		function getSelectedThemeName() {
			var presetSelect = document.getElementById( 'edminboost_theme_preset' );
			if ( ! presetSelect ) {
				return '';
			}
			var config = themePresets[ presetSelect.value ] || {};
			return config.name || presetSelect.value;
		}

		function renderTopBarSummary( presetId ) {
			if ( ! summaryRoot ) {
				return;
			}

			var preset = presetCatalog[ presetId ] || {};
			var items  = preset.top_bar_items || [];
			var countEl = summaryRoot.querySelector( '.edminboost-topbar-summary__count' );
			var emptyEl = summaryRoot.querySelector( '.edminboost-topbar-summary__empty' );

			summaryRoot.setAttribute( 'data-item-count', String( items.length ) );

			if ( countEl ) {
				var countLabel = items.length === 1
					? '1 link in your top bar'
					: items.length + ' links in your top bar';
				countEl.textContent = countLabel;
			}

			if ( ! summaryList ) {
				return;
			}

			summaryList.innerHTML = '';

			if ( ! items.length ) {
				if ( emptyEl ) {
					emptyEl.hidden = false;
				}
				return;
			}

			if ( emptyEl ) {
				emptyEl.hidden = true;
			}

			items.forEach( function ( item ) {
				var li = document.createElement( 'li' );
				li.className = 'edminboost-topbar-summary__item';

				var icon = document.createElement( 'span' );
				icon.className = 'dashicons ' + ( item.icon || 'dashicons-admin-generic' );
				icon.setAttribute( 'aria-hidden', 'true' );

				var label = document.createElement( 'span' );
				label.className = 'edminboost-topbar-summary__label';
				label.textContent = item.label || item.slug || '';

				li.appendChild( icon );
				li.appendChild( label );
				summaryList.appendChild( li );
			} );
		}

		function updateReviewPanel() {
			var layoutId   = getSelectedLayoutPreset();
			var layoutName = ( presetCatalog[ layoutId ] && presetCatalog[ layoutId ].name ) || layoutId || '—';
			var themeName  = getSelectedThemeName() || '—';
			var itemCount  = ( presetCatalog[ layoutId ] && presetCatalog[ layoutId ].top_bar_items )
				? presetCatalog[ layoutId ].top_bar_items.length
				: 0;
			var sidebarCount = ( presetCatalog[ layoutId ] && presetCatalog[ layoutId ].sidebar_items )
				? presetCatalog[ layoutId ].sidebar_items.length
				: 0;

			var layoutReview = document.getElementById( 'edminboost-review-layout' );
			var themeReview  = document.getElementById( 'edminboost-review-theme' );
			var sidebarReview = document.getElementById( 'edminboost-review-sidebar' );
			var topbarReview = document.getElementById( 'edminboost-review-topbar' );

			if ( layoutReview ) {
				layoutReview.textContent = layoutName;
			}
			if ( themeReview ) {
				themeReview.textContent = themeName;
			}
			if ( sidebarReview ) {
				sidebarReview.textContent = sidebarCount
					? String( sidebarCount ) + ( sidebarCount === 1 ? ' item' : ' items' )
					: '—';
			}
			if ( topbarReview ) {
				topbarReview.textContent = itemCount
					? String( itemCount ) + ( itemCount === 1 ? ' link' : ' links' )
					: '—';
			}
		}

		function syncPresetFields( presetId ) {
			if ( applyField ) {
				applyField.value = presetId || '';
			}
			if ( defaultField ) {
				defaultField.value = presetId || '';
			}
			renderTopBarSummary( presetId );
		}

		function setStep( step ) {
			currentStep = Math.max( 1, Math.min( totalSteps, step ) );

			steps.forEach( function ( panel ) {
				var panelStep = parseInt( panel.getAttribute( 'data-step' ), 10 );
				var isActive  = panelStep === currentStep;
				panel.classList.toggle( 'is-active', isActive );
				panel.hidden = ! isActive;
			} );

			stepperItems.forEach( function ( item ) {
				var itemStep = parseInt( item.getAttribute( 'data-step' ), 10 );
				item.classList.toggle( 'is-active', itemStep === currentStep );
				item.classList.toggle( 'is-complete', itemStep < currentStep );
			} );

			if ( backBtn ) {
				backBtn.hidden = currentStep <= 1;
			}

			if ( nextBtn ) {
				nextBtn.hidden = currentStep >= totalSteps;
			}

			if ( submitBtn ) {
				submitBtn.style.display = currentStep >= totalSteps ? '' : 'none';
			}

			if ( currentStep === 4 ) {
				updateReviewPanel();
			}
		}

		if ( layoutSelect ) {
			layoutSelect.addEventListener( 'change', function () {
				syncPresetFields( layoutSelect.value );
			} );
		}

		var themePresetSelect = document.getElementById( 'edminboost_theme_preset' );
		if ( themePresetSelect ) {
			themePresetSelect.addEventListener( 'change', updateReviewPanel );
		}

		nextBtn.addEventListener( 'click', function () {
			if ( currentStep === 1 && ! getSelectedLayoutPreset() ) {
				showFormNotice(
					form,
					'error',
					edminboostData.strings.selectLayoutPreset || 'Select a layout preset to continue.'
				);
				return;
			}

			if ( currentStep === 1 ) {
				syncPresetFields( getSelectedLayoutPreset() );
			}

			setStep( currentStep + 1 );
		} );

		if ( backBtn ) {
			backBtn.addEventListener( 'click', function () {
				setStep( currentStep - 1 );
			} );
		}

		form.addEventListener( 'submit', function () {
			syncPresetFields( getSelectedLayoutPreset() );
		} );

		var initialPreset = getSelectedLayoutPreset();
		if ( initialPreset ) {
			syncPresetFields( initialPreset );
		}

		setStep( 1 );
	}

	function initPresets( root ) {
		var form            = document.getElementById( 'edminboost-presets-form' );
		var applyField      = document.getElementById( 'edminboost_apply_preset' );
		var saveNameField   = document.getElementById( 'edminboost_save_custom_preset_name' );
		var savePresetBtn   = document.getElementById( 'edminboost-save-preset-btn' );
		var savePresetRoot  = document.getElementById( 'edminboost-save-preset' );
		var savePresetForm  = document.getElementById( 'edminboost-save-preset-form' );
		var savePresetInput = document.getElementById( 'edminboost_save_preset_name_input' );
		var savePresetConfirmBtn = document.getElementById( 'edminboost-save-preset-confirm-btn' );
		var savePresetCancelBtn  = document.getElementById( 'edminboost-save-preset-cancel-btn' );
		var applyBtn        = document.getElementById( 'edminboost-preset-apply-btn' );
		var exportBtn       = document.getElementById( 'edminboost-preset-export-btn' );
		var layoutSelect    = document.getElementById( 'edminboost_layout_preset' );
		var defaultField    = document.getElementById( 'edminboost_layout_default_preset' );
		var defaultCheckbox = document.getElementById( 'edminboost_layout_preset_default_checkbox' );
		var presetCatalog   = edminboostData.presets || {};

		if ( ! form ) {
			return;
		}

		function getSelectedPresetId() {
			return layoutSelect ? layoutSelect.value : '';
		}

		function clearActionFields() {
			if ( applyField ) {
				applyField.value = '';
			}
			if ( saveNameField ) {
				saveNameField.value = '';
			}
		}

		function preparePresetAction( field, value ) {
			clearActionFields();

			if ( field ) {
				field.value = value;
			}
		}

		form.addEventListener( 'submit', function () {
			if ( defaultCheckbox && defaultCheckbox.checked && defaultField ) {
				var presetId = getSelectedPresetId();
				if ( presetId && 'default' !== presetId ) {
					defaultField.value = presetId;
				}
			}
		} );

		if ( applyBtn ) {
			applyBtn.addEventListener( 'click', function () {
				if ( ! applyField ) {
					return;
				}

				var presetId = getSelectedPresetId();
				if ( ! presetId ) {
					return;
				}

				preparePresetAction( applyField, presetId );

				if ( defaultCheckbox && defaultCheckbox.checked && defaultField && 'default' !== presetId ) {
					defaultField.value = presetId;
				}

				saveSettingsForm( form, {
					onSuccess: function () {
						clearActionFields();
						window.location.reload();
					}
				} );
			} );
		}

		function openSavePresetForm() {
			if ( ! savePresetRoot || ! savePresetForm ) {
				return;
			}

			savePresetRoot.classList.add( 'is-editing' );

			if ( savePresetInput ) {
				savePresetInput.value = '';
				savePresetInput.focus();
			}
		}

		function closeSavePresetForm() {
			if ( ! savePresetRoot ) {
				return;
			}

			savePresetRoot.classList.remove( 'is-editing' );

			if ( savePresetInput ) {
				savePresetInput.value = '';
			}
		}

		function submitSavePreset() {
			if ( ! form || ! saveNameField || ! savePresetInput ) {
				return;
			}

			var name = savePresetInput.value.trim();
			if ( ! name ) {
				showFormNotice(
					form,
					'error',
					edminboostData.strings.presetNameRequired || 'Enter a name for your preset.'
				);
				savePresetInput.focus();
				return;
			}

			preparePresetAction( saveNameField, name );
			saveSettingsForm( form, {
				submitBtn: savePresetConfirmBtn,
				message: edminboostData.strings.presetSaved || 'Preset saved.',
				onSuccess: function () {
					clearActionFields();
					closeSavePresetForm();
				}
			} );
		}

		if ( savePresetBtn ) {
			savePresetBtn.addEventListener( 'click', openSavePresetForm );
		}

		if ( savePresetCancelBtn ) {
			savePresetCancelBtn.addEventListener( 'click', closeSavePresetForm );
		}

		if ( savePresetConfirmBtn ) {
			savePresetConfirmBtn.addEventListener( 'click', submitSavePreset );
		}

		if ( savePresetInput ) {
			savePresetInput.addEventListener( 'keydown', function ( event ) {
				if ( event.key === 'Enter' ) {
					event.preventDefault();
					submitSavePreset();
				} else if ( event.key === 'Escape' ) {
					event.preventDefault();
					closeSavePresetForm();
				}
			} );
		}

		if ( exportBtn ) {
			exportBtn.addEventListener( 'click', function () {
				var presetId = getSelectedPresetId() || 'preset';
				var preset   = presetCatalog[ presetId ] || {};
				var payload  = {
					id: presetId,
					name: preset.name || presetId,
					description: preset.description || '',
					top_bar_items: preset.top_bar_items || [],
					menu_studio: preset.menu_studio || {},
					sidebar_items: preset.sidebar_items || [],
					exported: new Date().toISOString(),
					source: 'EdminBoost'
				};
				var blob = new Blob( [ JSON.stringify( payload, null, 2 ) ], { type: 'application/json' } );
				var url  = URL.createObjectURL( blob );
				var link = document.createElement( 'a' );
				link.href = url;
				link.download = presetId + '.json';
				link.click();
				URL.revokeObjectURL( url );
			} );
		}

		initRoleMatrix( form, presetCatalog );
	}

	/**
	 * Sync role visibility checkboxes with assigned layout presets.
	 *
	 * @param {HTMLFormElement} form Presets page form.
	 * @param {Object} presetCatalog Resolved preset layouts from edminboostData.presets.
	 */
		function initRoleMatrix( form, presetCatalog ) {
		var matrix = form.querySelector( '.edminboost-role-matrix' );

		if ( ! matrix ) {
			return;
		}

		var roleMatrixData     = edminboostData.roleMatrix || {};
		var protectedSlugs     = roleMatrixData.protectedSlugs || [];
		var protectedByRole    = roleMatrixData.protectedSlugsByRole || {};
		var accessibleByRole   = roleMatrixData.accessibleSlugsByRole || {};

		function extractSlugs( items ) {
			var slugs = [];

			( items || [] ).forEach( function ( item ) {
				if ( item && item.slug ) {
					slugs.push( item.slug );
				}
			} );

			return slugs;
		}

		function getRoleKey( row ) {
			return row ? ( row.getAttribute( 'data-edminboost-role' ) || '' ) : '';
		}

		function getAccessibleSlugs( roleKey ) {
			return accessibleByRole[ roleKey ] || [];
		}

		function isProtectedSlug( slug, roleKey ) {
			var list = protectedByRole[ roleKey ] || protectedSlugs;

			return list.indexOf( slug ) !== -1;
		}

		function getRoleLayoutSlugs( presetId, roleKey ) {
			var preset;
			var slugs;
			var accessible = getAccessibleSlugs( roleKey );

			if ( ! presetId ) {
				preset = presetCatalog.custom || {};
			} else {
				preset = presetCatalog[ presetId ] || {};
			}

			if ( preset.visible_top_level_menu_slugs && preset.visible_top_level_menu_slugs.length ) {
				slugs = preset.visible_top_level_menu_slugs.slice();
			} else if ( preset.visible_menu_slugs && preset.visible_menu_slugs.length ) {
				slugs = preset.visible_menu_slugs.slice();
			} else {
				slugs = extractSlugs( preset.top_bar_items );
			}

			if ( ! accessible.length ) {
				return slugs;
			}

			return slugs.filter( function ( slug ) {
				return accessible.indexOf( slug ) !== -1;
			} );
		}

		function syncRoleRowVisibility( selectEl, resetVisibility ) {
			var row = selectEl.closest( '.edminboost-role-matrix__row' );

			if ( ! row ) {
				return;
			}

			var roleKey     = getRoleKey( row );
			var presetId    = selectEl.value;
			var layoutSlugs = getRoleLayoutSlugs( presetId, roleKey );
			var hasPreset   = presetId !== '';

			row.querySelectorAll( '.edminboost-role-matrix__check input[type="checkbox"]:not(:disabled)' ).forEach( function ( input ) {
				var slug          = input.value;
				var inLayout      = layoutSlugs.indexOf( slug ) !== -1;
				var cell          = input.closest( '.edminboost-role-matrix__check' );
				var protectedSlug = isProtectedSlug( slug, roleKey );

				if ( protectedSlug ) {
					input.checked = true;
					if ( cell ) {
						cell.classList.remove( 'is-not-in-layout' );
					}
					return;
				}

				var unavailable = hasPreset && ! inLayout;

				if ( cell ) {
					cell.classList.toggle( 'is-not-in-layout', unavailable );
				}

				if ( resetVisibility ) {
					input.checked = ! unavailable;
				} else if ( unavailable ) {
					input.checked = false;
				}
			} );
		}

		matrix.querySelectorAll( '.edminboost-role-preset-select' ).forEach( function ( selectEl ) {
			syncRoleRowVisibility( selectEl, false );

			selectEl.addEventListener( 'change', function () {
				syncRoleRowVisibility( selectEl, true );
			} );
		} );
	}

	function initBackupSettings( root ) {
		var exportBtn = root.querySelector( '#edminboost-export-settings' );
		var importBtn = root.querySelector( '#edminboost-import-settings' );
		var importArea = root.querySelector( '#edminboost-import-json' );

		if ( exportBtn ) {
			exportBtn.addEventListener( 'click', function () {
				var formData = new FormData();
				formData.append( 'action', 'edminboost_export_settings' );
				formData.append( 'nonce', exportBtn.getAttribute( 'data-nonce' ) || '' );

				window.fetch( edminboostData.settingsSave.ajaxUrl, {
					method: 'POST',
					body: formData,
					credentials: 'same-origin'
				} )
					.then( function ( response ) { return response.json(); } )
					.then( function ( payload ) {
						if ( ! payload.success || ! payload.data || ! payload.data.json ) {
							return;
						}

						var blob = new Blob( [ payload.data.json ], { type: 'application/json' } );
						var url  = URL.createObjectURL( blob );
						var link = document.createElement( 'a' );
						link.href = url;
						link.download = 'edminboost-settings.json';
						link.click();
						URL.revokeObjectURL( url );
					} );
			} );
		}

		if ( importBtn && importArea ) {
			importBtn.addEventListener( 'click', function () {
				var formData = new FormData();
				formData.append( 'action', 'edminboost_import_settings' );
				formData.append( 'nonce', importBtn.getAttribute( 'data-nonce' ) || '' );
				formData.append( 'json', importArea.value );

				window.fetch( edminboostData.settingsSave.ajaxUrl, {
					method: 'POST',
					body: formData,
					credentials: 'same-origin'
				} )
					.then( function ( response ) { return response.json(); } )
					.then( function ( payload ) {
						if ( payload.success ) {
							window.location.reload();
						}
					} );
			} );
		}
	}
} )();
