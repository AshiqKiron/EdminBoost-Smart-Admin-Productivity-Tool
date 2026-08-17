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

		initOnboarding( root );
		initMapper( root );
		initBehavior( root );
		initPresets( root );
	} );

	function initOnboarding( root ) {
		var personaCards = root.querySelectorAll( '.edminboost-persona-card' );
		var presetInput  = document.getElementById( 'edminboost_default_preset' );

		if ( ! personaCards.length ) {
			return;
		}

		personaCards.forEach( function ( card ) {
			var input = card.querySelector( '.edminboost-persona-card__input' );

			card.addEventListener( 'click', function () {
				personaCards.forEach( function ( other ) {
					other.classList.remove( 'is-selected' );
					var otherInput = other.querySelector( '.edminboost-persona-card__input' );
					if ( otherInput ) {
						otherInput.checked = false;
					}
				} );

				card.classList.add( 'is-selected' );
				if ( input ) {
					input.checked = true;
				}

				if ( presetInput && input && input.dataset.preset ) {
					presetInput.value = input.dataset.preset;
				}
			} );
		} );
	}

	function initMapper( root ) {
		var form         = document.getElementById( 'edminboost-mapper-form' );
		var canvas       = document.getElementById( 'edminboost-topbar-items' );
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

		function getItems() {
			return Array.prototype.slice.call( canvas.querySelectorAll( '.edminboost-topbar-item' ) );
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
			if ( canvas.querySelector( '[data-slug="' + data.slug + '"]' ) ) {
				return;
			}

			canvas.appendChild( createTopBarItem( data ) );
			updateEmptyState();
			syncHiddenInputs();
		}

		function removeFromCanvas( slug ) {
			var item = canvas.querySelector( '[data-slug="' + slug + '"]' );
			if ( item ) {
				item.remove();
			}

			var checkbox = discovered.querySelector( '.edminboost-discovered-item[data-slug="' + slug + '"] .edminboost-discovered-item__checkbox' );
			if ( checkbox ) {
				checkbox.checked = false;
			}

			if ( selectedItem && selectedItem.dataset.slug === slug ) {
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
				subtitle.textContent = item.dataset.slug;
			}

			var labelInput = document.getElementById( 'edminboost-item-label' );
			if ( labelInput ) {
				labelInput.value = item.dataset.label || '';
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
				dragItem = item;
				item.classList.add( 'is-dragging' );
				event.dataTransfer.effectAllowed = 'move';
			} );

			item.addEventListener( 'dragend', function () {
				item.classList.remove( 'is-dragging' );
				dragItem = null;
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

		getItems().forEach( bindTopBarItem );

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
				addToCanvas( {
					slug: row.dataset.slug,
					label: row.dataset.label,
					icon: row.dataset.icon
				} );
			} else {
				removeFromCanvas( row.dataset.slug );
			}
		} );

		if ( searchInput ) {
			searchInput.addEventListener( 'input', function () {
				var query = searchInput.value.toLowerCase().trim();
				discovered.querySelectorAll( '.edminboost-discovered-item' ).forEach( function ( row ) {
					var label = ( row.dataset.label || '' ).toLowerCase();
					row.classList.toggle( 'is-hidden-by-search', query !== '' && label.indexOf( query ) === -1 );
				} );
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

		var interactionRadios = document.querySelectorAll( 'input[name="edminboost_item_interaction"]' );
		interactionRadios.forEach( function ( radio ) {
			radio.addEventListener( 'change', function () {
				if ( ! selectedItem || ! radio.checked ) {
					return;
				}
				selectedItem.dataset.interaction = radio.value;
				syncHiddenInputs();
			} );
		} );

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
					removeFromCanvas( selectedItem.dataset.slug );
				}
			} );
		}

		form.addEventListener( 'submit', syncHiddenInputs );
		syncHiddenInputs();
		updateEmptyState();
	}

	function initBehavior( root ) {
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

	function initPresets( root ) {
		var exportButtons = root.querySelectorAll( '.edminboost-preset-export' );

		exportButtons.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				var presetId = btn.dataset.presetId || 'preset';
				var payload = {
					id: presetId,
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
