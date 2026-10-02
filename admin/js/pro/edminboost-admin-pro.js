(function () {
	'use strict';
	if ( ! window.edminboostData || ! window.edminboostData.isPremiumBuild ) {
		return;
	}

	window.edminboostInitProAdmin = function ( root ) {
	function initBehavior( root ) {
			initBehaviorBadgePreview( root );
			initBehaviorDrawerWidthCustom( root );
			initBehaviorAnimationSpeed( root );
		}
	
		function initSecurityFeatures( root ) {
			var enabledToggle = root.querySelector( '#edminboost_login_redirects_enabled' );
			var optionsSection = root.querySelector( '#edminboost-login-redirects-options' );
	
			if ( ! enabledToggle || ! optionsSection ) {
				return;
			}
	
			function syncLoginRedirectsOptions() {
				syncDependentSection( optionsSection, enabledToggle.checked );
			}
	
			enabledToggle.addEventListener( 'change', syncLoginRedirectsOptions );
			syncLoginRedirectsOptions();
		}
		function initWhiteLabel( root ) {
			var enabledToggle = root.querySelector( '#edminboost_wl_enabled' );
			var dependentSections = [
				root.querySelector( '#edminboost-wl-status-section' ),
				root.querySelector( '#edminboost-wl-rebrand-section' )
			].filter( function ( section ) {
				return !! section;
			} );
	
			function setWhiteLabelSectionEnabled( section, isEnabled ) {
				section.classList.toggle( 'is-disabled', ! isEnabled );
				section.setAttribute( 'aria-disabled', isEnabled ? 'false' : 'true' );
	
				section.querySelectorAll( 'input, textarea, select, button, a[href]' ).forEach( function ( control ) {
					if ( isEnabled ) {
						if ( Object.prototype.hasOwnProperty.call( control.dataset, 'edminboostWlTabindex' ) ) {
							if ( '' === control.dataset.edminboostWlTabindex ) {
								control.removeAttribute( 'tabindex' );
							} else {
								control.setAttribute( 'tabindex', control.dataset.edminboostWlTabindex );
							}
	
							delete control.dataset.edminboostWlTabindex;
						}
	
						control.removeAttribute( 'aria-disabled' );
						return;
					}
	
					if ( ! Object.prototype.hasOwnProperty.call( control.dataset, 'edminboostWlTabindex' ) ) {
						control.dataset.edminboostWlTabindex = control.getAttribute( 'tabindex' ) || '';
					}
	
					control.setAttribute( 'tabindex', '-1' );
					control.setAttribute( 'aria-disabled', 'true' );
				} );
			}
	
			function syncWhiteLabelDependentSections() {
				var isEnabled = enabledToggle && enabledToggle.checked;
	
				dependentSections.forEach( function ( section ) {
					setWhiteLabelSectionEnabled( section, isEnabled );
				} );
			}
	
			if ( enabledToggle && dependentSections.length ) {
				enabledToggle.addEventListener( 'change', syncWhiteLabelDependentSections );
				syncWhiteLabelDependentSections();
			}
	
			initWhiteLabelStatusPreview( root );
			initWhiteLabelRebrandPreview( root );
		}
	
		function syncWhiteLabelStatusPreviewItem( item, isVisible ) {
			item.classList.toggle( 'is-hidden', ! isVisible );
	
			var tooltip = item.querySelector( '.edminboost-wl-status-preview__tooltip' );
			var tooltipText = isVisible
				? item.getAttribute( 'data-tooltip-visible' )
				: item.getAttribute( 'data-tooltip-hidden' );
	
			item.setAttribute( 'aria-label', tooltipText || '' );
	
			if ( tooltip ) {
				tooltip.textContent = tooltipText || '';
			}
		}
	
		function initWhiteLabelStatusPreview( root ) {
			var statusSection = root.querySelector( '#edminboost-wl-status-section' );
	
			if ( ! statusSection ) {
				return;
			}
	
			var preview = statusSection.querySelector( '#edminboost-wl-status-preview' );
	
			if ( ! preview ) {
				return;
			}
	
			var previewLine = preview.querySelector( '#edminboost-wl-status-preview-line' );
			var previewEmpty = preview.querySelector( '#edminboost-wl-status-preview-empty' );
			var statusToggles = {
				show_ip: statusSection.querySelector( '#edminboost_wl_show_ip' ),
				show_php_version: statusSection.querySelector( '#edminboost_wl_show_php_version' ),
				show_wp_version: statusSection.querySelector( '#edminboost_wl_show_wp_version' ),
				show_memory_usage: statusSection.querySelector( '#edminboost_wl_show_memory_usage' ),
				show_memory_limit: statusSection.querySelector( '#edminboost_wl_show_memory_limit' ),
				show_memory_available: statusSection.querySelector( '#edminboost_wl_show_memory_available' )
			};
	
			function syncStatusFooterPreview() {
				var visibleCount = 0;
	
				Object.keys( statusToggles ).forEach( function ( key ) {
					var toggle = statusToggles[ key ];
					var items = preview.querySelectorAll( '[data-preview="' + key + '"]' );
					var isVisible = toggle && toggle.checked;
	
					if ( isVisible ) {
						visibleCount += 1;
					}
	
					items.forEach( function ( item ) {
						syncWhiteLabelStatusPreviewItem( item, isVisible );
					} );
				} );
	
				var hasVisible = visibleCount > 0;
	
				preview.classList.toggle( 'is-empty', ! hasVisible );
	
				if ( previewLine ) {
					previewLine.hidden = ! hasVisible;
				}
	
				if ( previewEmpty ) {
					previewEmpty.hidden = hasVisible;
				}
			}
	
			Object.keys( statusToggles ).forEach( function ( key ) {
				var toggle = statusToggles[ key ];
	
				if ( toggle ) {
					toggle.addEventListener( 'change', syncStatusFooterPreview );
				}
			} );
	
			syncStatusFooterPreview();
		}
	
		function initWhiteLabelRebrandPreview( root ) {
			var section = root.querySelector( '#edminboost-wl-rebrand-section' );
	
			if ( ! section ) {
				return;
			}
	
			var preview = section.querySelector( '#edminboost-wl-rebrand-preview' );
			var nameEl = section.querySelector( '#edminboost_wl_plugin_name' );
			var descriptionEl = section.querySelector( '#edminboost_wl_plugin_description' );
			var authorEl = section.querySelector( '#edminboost_wl_plugin_author' );
			var uriEl = section.querySelector( '#edminboost_wl_plugin_uri' );
			var menuLabelEl = section.querySelector( '#edminboost_wl_menu_label' );
			var previewName = section.querySelector( '#edminboost-wl-preview-name' );
			var previewDescription = section.querySelector( '#edminboost-wl-preview-description' );
			var previewAuthorLink = section.querySelector( '#edminboost-wl-preview-author-link' );
			var previewMenuLabel = section.querySelector( '#edminboost-wl-preview-menu-label' );
	
			if ( ! preview || ! previewName || ! previewDescription || ! previewAuthorLink || ! previewMenuLabel ) {
				return;
			}
	
			function resolveFieldValue( input, defaultKey ) {
				var value = input && typeof input.value === 'string' ? input.value.trim() : '';
	
				if ( '' !== value ) {
					return value;
				}
	
				return preview.getAttribute( 'data-default-' + defaultKey ) || '';
			}
	
			function updatePreview() {
				var name = resolveFieldValue( nameEl, 'name' );
				var description = resolveFieldValue( descriptionEl, 'description' );
				var author = resolveFieldValue( authorEl, 'author' );
				var uri = resolveFieldValue( uriEl, 'uri' );
				var menuLabel = resolveFieldValue( menuLabelEl, 'menu-label' );
	
				previewName.textContent = name;
				previewDescription.textContent = description;
				previewAuthorLink.textContent = author;
				previewMenuLabel.textContent = menuLabel;
	
				if ( uri ) {
					previewAuthorLink.setAttribute( 'href', uri );
					previewAuthorLink.removeAttribute( 'tabindex' );
				} else {
					previewAuthorLink.setAttribute( 'href', '#' );
					previewAuthorLink.setAttribute( 'tabindex', '-1' );
				}
			}
	
			[ nameEl, descriptionEl, authorEl, uriEl, menuLabelEl ].forEach( function ( input ) {
				if ( ! input ) {
					return;
				}
	
				input.addEventListener( 'input', updatePreview );
			} );
	
			previewAuthorLink.addEventListener( 'click', function ( event ) {
				if ( ! resolveFieldValue( uriEl, 'uri' ) ) {
					event.preventDefault();
				}
			} );
	
			updatePreview();
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
		function initBackupSettings( root ) {
			var backupSection = root.querySelector( '#edminboost-backup-section' );
	
			if ( ! backupSection ) {
				return;
			}
	
			var exportBtn = backupSection.querySelector( '#edminboost-export-settings' );
			var importBtn = backupSection.querySelector( '#edminboost-import-settings' );
			var importArea = backupSection.querySelector( '#edminboost-import-json' );
			var importFile = backupSection.querySelector( '#edminboost-import-file' );
			var fileRadio = backupSection.querySelector( '#edminboost-import-method-file' );
			var strings = edminboostData.strings || {};
	
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
								var exportMessage = payload.data && payload.data.message
									? payload.data.message
									: ( strings.exportFailed || 'Could not export settings. Please try again.' );
	
								throw new Error( exportMessage );
							}
	
							var blob     = new Blob( [ payload.data.json ], { type: 'application/json' } );
							var url      = URL.createObjectURL( blob );
							var link     = document.createElement( 'a' );
							var exportDate = new Date();
							var dateStamp  = exportDate.getFullYear()
								+ '-' + String( exportDate.getMonth() + 1 ).padStart( 2, '0' )
								+ '-' + String( exportDate.getDate() ).padStart( 2, '0' );
							link.href = url;
							link.download = 'export-' + dateStamp + '.json';
							link.click();
							URL.revokeObjectURL( url );
						} )
						.catch( function ( error ) {
							window.alert( error.message || strings.exportFailed || 'Could not export settings. Please try again.' );
						} );
				} );
			}
	
			function readImportJson() {
				var useFile = fileRadio && fileRadio.checked;
	
				if ( ! useFile ) {
					return Promise.resolve( importArea ? importArea.value : '' );
				}
	
				if ( ! importFile || ! importFile.files || ! importFile.files.length ) {
					return Promise.reject( new Error( strings.importFileRequired || 'Choose a JSON file to import.' ) );
				}
	
				return new Promise( function ( resolve, reject ) {
					var reader = new FileReader();
	
					reader.onload = function () {
						resolve( reader.result );
					};
	
					reader.onerror = function () {
						reject( new Error( strings.importReadFailed || 'Could not read the selected file.' ) );
					};
	
					reader.readAsText( importFile.files[0] );
				} );
			}
	
			if ( importBtn ) {
				importBtn.addEventListener( 'click', function () {
					readImportJson()
						.then( function ( json ) {
							if ( ! json || ! String( json ).trim() ) {
								throw new Error( strings.importJsonRequired || 'Paste exported JSON or choose a file to import.' );
							}
	
							var formData = new FormData();
							formData.append( 'action', 'edminboost_import_settings' );
							formData.append( 'nonce', importBtn.getAttribute( 'data-nonce' ) || '' );
							formData.append( 'json', json );
	
							return window.fetch( edminboostData.settingsSave.ajaxUrl, {
								method: 'POST',
								body: formData,
								credentials: 'same-origin'
							} );
						} )
						.then( function ( response ) { return response.json(); } )
						.then( function ( payload ) {
							if ( payload.success ) {
								window.location.reload();
								return;
							}
	
							var message = payload.data && payload.data.message
								? payload.data.message
								: ( strings.importFailed || 'Could not import settings. Check the JSON and try again.' );
	
							window.alert( message );
						} )
						.catch( function ( error ) {
							window.alert( error.message || strings.importFailed || 'Could not import settings. Check the JSON and try again.' );
						} );
				} );
			}
		}

		initBehavior( root );
		initWhiteLabel( root );
		initSecurityFeatures( root );
		initBackupSettings( root );
	};
} )();
