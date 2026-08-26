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

		initHome( root );
		initMapper( root );
		initMenuStudio( root );
		initBehavior( root );
		initTheme( root );
		initPresets( root );
		initCommandCenterForms( root );
		initSettingsForm( root );
	} );

	function initHome( root ) {
		var skinCards = root.querySelectorAll( '.edminboost-skin-card' );
		var homeForm  = document.getElementById( 'edminboost-home-form' );
		var applySkin = document.getElementById( 'edminboost_apply_look_skin' );
		var keepAdvanced = document.getElementById( 'edminboost_keep_advanced_behavior' );
		var advancedLook = root.querySelector( '.edminboost-advanced-look' );

		if ( skinCards.length ) {
			skinCards.forEach( function ( card ) {
				var input = card.querySelector( '.edminboost-skin-card__input' );

				card.addEventListener( 'click', function () {
					skinCards.forEach( function ( other ) {
						other.classList.remove( 'is-selected' );
						var otherInput = other.querySelector( '.edminboost-skin-card__input' );
						if ( otherInput ) {
							otherInput.checked = false;
						}
					} );

					card.classList.add( 'is-selected' );
					if ( input ) {
						input.checked = true;
					}
				} );
			} );
		}

		if ( ! homeForm ) {
			return;
		}

		homeForm.addEventListener( 'submit', function () {
			var selectedSkin = homeForm.querySelector( '.edminboost-skin-card__input:checked' );
			var advancedOpen = advancedLook && advancedLook.open;

			if ( applySkin ) {
				applySkin.value = '';
			}

			if ( keepAdvanced ) {
				keepAdvanced.value = '';
			}

			if ( advancedOpen ) {
				if ( keepAdvanced ) {
					keepAdvanced.value = '1';
				}
				return;
			}

			if ( selectedSkin && applySkin ) {
				applySkin.value = selectedSkin.value;
			}
		} );
	}

	function initMapper( root ) {
		var form         = document.getElementById( 'edminboost-mapper-form' );
		var canvas       = document.getElementById( 'edminboost-topbar-items' );
		var canvasShell  = document.getElementById( 'edminboost-topbar-canvas' );
		var discovered   = document.getElementById( 'edminboost-discovered-list' );
		var searchInput  = document.getElementById( 'edminboost-plugin-search' );
		var drawer       = document.getElementById( 'edminboost-item-drawer' );
		var mapperMain   = root.querySelector( '.edminboost-mapper-main' );
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

			if ( mapperMain ) {
				mapperMain.classList.add( 'is-drawer-open' );
			}

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

			if ( mapperMain ) {
				mapperMain.classList.remove( 'is-drawer-open' );
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

		form.addEventListener( 'submit', syncHiddenInputs );
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
			return Array.prototype.slice.call( canvas.querySelectorAll( ':scope > .edminboost-sidebar-item' ) );
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

			getTopItems().forEach( function ( item, index ) {
				appendHiddenInput( msPrefix + '[order][' + index + ']', item.dataset.slug || '' );

				var subList = item.querySelector( '.edminboost-sidebar-subitems' );
				if ( ! subList ) {
					return;
				}

				var parentSlug = item.dataset.slug || '';
				Array.prototype.forEach.call( subList.querySelectorAll( '.edminboost-sidebar-subitem' ), function ( subItem, subIndex ) {
					appendHiddenInput(
						msPrefix + '[submenu_order][' + parentSlug + '][' + subIndex + ']',
						subItem.dataset.slug || ''
					);
				} );
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
		form.addEventListener( 'submit', syncHiddenInputs );
		syncHiddenInputs();
		updateEmptyState();
	}

	function initBehavior( root ) {
		initBehaviorBadgePreview( root );
		initBehaviorDrawerWidthCustom( root );
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
		var widthRadios   = root.querySelectorAll( 'input[name*="[drawer_width]"]' );
		var customWrap    = document.getElementById( 'edminboost-drawer-width-custom' );
		var slider        = document.getElementById( 'edminboost_drawer_width_custom' );
		var valueEl       = document.getElementById( 'edminboost_drawer_width_custom_value' );
		var previewDrawer = document.getElementById( 'edminboost_drawer_width_preview_drawer' );
		var previewCaption = document.getElementById( 'edminboost-drawer-width-preview-caption' );
		var referenceViewport = 1280;

		if ( ! widthRadios.length || ! customWrap || ! slider ) {
			return;
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
			var isCustom = false;

			widthRadios.forEach( function ( radio ) {
				if ( radio.checked && radio.value === 'custom' ) {
					isCustom = true;
				}
			} );

			customWrap.hidden = ! isCustom;
		}

		function updateSliderPreview() {
			var px      = parseInt( slider.value, 10 );
			var percent = Math.round( ( px / referenceViewport ) * 100 );

			if ( valueEl ) {
				valueEl.textContent = px + 'px';
			}

			if ( previewDrawer ) {
				previewDrawer.style.width = percent + '%';
			}

			if ( previewCaption ) {
				previewCaption.textContent = formatPreviewCaption( px, percent );
			}
		}

		widthRadios.forEach( function ( radio ) {
			radio.addEventListener( 'change', updateCustomVisibility );
		} );

		slider.addEventListener( 'input', updateSliderPreview );

		updateCustomVisibility();
		updateSliderPreview();
	}

	function initTheme( root ) {
		var presetRadios = root.querySelectorAll( '.edminboost-theme-card__input' );
		var modeSelect   = document.getElementById( 'edminboost_theme_mode' );
		var fontSelect   = document.getElementById( 'edminboost_theme_font' );
		var useCustom    = document.getElementById( 'edminboost_use_custom_colors' );
		var customWrap   = document.getElementById( 'edminboost-theme-custom-colors' );
		var preview      = document.getElementById( 'edminboost-theme-preview' );

		if ( ! presetRadios.length ) {
			return;
		}

		var themeClasses = [
			'edminboost-theme-active',
			'edminboost-theme--default',
			'edminboost-theme--midnight',
			'edminboost-theme--terminal',
			'edminboost-theme--neon-outrun',
			'edminboost-theme--vapor',
			'edminboost-theme--desert',
			'edminboost-theme-mode--light',
			'edminboost-theme-mode--dark',
			'edminboost-theme-mode--auto',
			'edminboost-theme-font--inherit',
			'edminboost-theme-font--system',
			'edminboost-theme-font--mono',
			'edminboost-theme-font--serif',
			'edminboost-theme-font--rounded'
		];

		var colorFields = [
			{ picker: 'edminboost_custom_accent_picker', text: 'edminboost_custom_accent', varName: '--eb-accent' },
			{ picker: 'edminboost_custom_surface_picker', text: 'edminboost_custom_surface', varName: '--eb-surface' },
			{ picker: 'edminboost_custom_text_picker', text: 'edminboost_custom_text', varName: '--eb-text' }
		];

		function getSelectedPreset() {
			var preset = 'default';
			presetRadios.forEach( function ( radio ) {
				if ( radio.checked ) {
					preset = radio.value;
				}
			} );
			return preset;
		}

		function syncThemeCards() {
			presetRadios.forEach( function ( radio ) {
				var card = radio.closest( '.edminboost-theme-card' );
				if ( card ) {
					card.classList.toggle( 'is-selected', radio.checked );
				}
			} );
		}

		function applyCustomColors( target ) {
			if ( ! useCustom || ! useCustom.checked ) {
				target.style.removeProperty( '--eb-accent' );
				target.style.removeProperty( '--eb-surface' );
				target.style.removeProperty( '--eb-text' );
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
				}
			} );
		}

		function updateThemePreview() {
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

			if ( customWrap ) {
				customWrap.hidden = ! useCustom || ! useCustom.checked;
			}

			applyCustomColors( body );
			if ( preview ) {
				applyCustomColors( preview );
			}

			syncThemeCards();
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

		presetRadios.forEach( function ( radio ) {
			radio.addEventListener( 'change', updateThemePreview );
		} );

		if ( modeSelect ) {
			modeSelect.addEventListener( 'change', updateThemePreview );
		}

		if ( fontSelect ) {
			fontSelect.addEventListener( 'change', updateThemePreview );
		}

		if ( useCustom ) {
			useCustom.addEventListener( 'change', updateThemePreview );
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

	function saveSettingsForm( form, options ) {
		options = options || {};
		var submitBtn  = form.querySelector( '[type="submit"]' );
		var saveConfig = edminboostData.settingsSave;
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
					payload.data && payload.data.message
						? payload.data.message
						: edminboostData.strings.settingsSaved
				);

				if ( options.reload ) {
					window.setTimeout( function () {
						window.location.reload();
					}, 600 );
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

	function initPresets( root ) {
		var form           = document.getElementById( 'edminboost-presets-form' );
		var applyField     = document.getElementById( 'edminboost_apply_preset' );
		var duplicateField = document.getElementById( 'edminboost_duplicate_preset' );
		var saveNameField  = document.getElementById( 'edminboost_save_custom_preset_name' );
		var savePresetBtn  = document.getElementById( 'edminboost-save-preset-btn' );
		var exportButtons  = root.querySelectorAll( '.edminboost-preset-export' );
		var applyButtons   = root.querySelectorAll( '.edminboost-preset-apply' );
		var duplicateButtons = root.querySelectorAll( '.edminboost-preset-duplicate' );
		var presetCatalog  = edminboostData.presets || {};

		function clearActionFields() {
			if ( applyField ) {
				applyField.value = '';
			}
			if ( duplicateField ) {
				duplicateField.value = '';
			}
			if ( saveNameField ) {
				saveNameField.value = '';
			}
		}

		applyButtons.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				if ( ! form || ! applyField ) {
					return;
				}

				clearActionFields();
				applyField.value = btn.dataset.presetId || '';

				var defaultRadio = form.querySelector( 'input[name*="[default_preset]"][value="' + applyField.value + '"]' );
				if ( defaultRadio ) {
					defaultRadio.checked = true;
				}

				saveSettingsForm( form, { reload: true } );
			} );
		} );

		duplicateButtons.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				if ( ! form || ! duplicateField ) {
					return;
				}

				clearActionFields();
				duplicateField.value = btn.dataset.presetId || '';
				saveSettingsForm( form, { reload: true } );
			} );
		} );

		if ( savePresetBtn ) {
			savePresetBtn.addEventListener( 'click', function () {
				if ( ! form || ! saveNameField ) {
					return;
				}

				var name = window.prompt(
					edminboostData.strings.presetNameRequired || 'Enter a name for your preset.',
					''
				);

				if ( ! name || ! name.trim() ) {
					return;
				}

				clearActionFields();
				saveNameField.value = name.trim();
				saveSettingsForm( form, { reload: true } );
			} );
		}

		exportButtons.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				var presetId = btn.dataset.presetId || 'preset';
				var preset   = presetCatalog[ presetId ] || {};
				var payload  = {
					id: presetId,
					name: preset.name || presetId,
					description: preset.description || '',
					top_bar_items: preset.top_bar_items || [],
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
		} );
	}
} )();
