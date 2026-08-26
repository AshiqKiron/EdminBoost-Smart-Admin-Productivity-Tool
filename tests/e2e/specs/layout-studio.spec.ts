import { test, expect } from '@playwright/test';
import { pages } from '../fixtures/pages';
import { waitForSettingsSaved } from '../fixtures/helpers';

test.describe( 'Top Bar editor', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( pages.mapper );
		await expect( page.locator( '#edminboost-mapper-form' ) ).toBeVisible();
	} );

	test( 'mapper initializes with ready marker', async ( { page } ) => {
		await expect( page.locator( '.edminboost-wrap[data-edminboost-ready="true"]' ) ).toBeVisible();
	} );

	test( 'search filters discovered admin pages', async ( { page } ) => {
		const search = page.locator( '#edminboost-plugin-search' );
		const items = page.locator( '#edminboost-discovered-list .edminboost-discovered-item' );

		if ( await items.count() === 0 ) {
			test.skip();
		}

		const firstLabel = await items.first().locator( '.edminboost-discovered-item__label' ).textContent();
		if ( ! firstLabel ) {
			test.skip();
		}

		await search.fill( firstLabel.trim().slice( 0, 4 ) );
		await expect( items.first() ).toBeVisible();
	} );

	test( 'checkbox adds item to canvas', async ( { page } ) => {
		const item = page.locator( '#edminboost-discovered-list .edminboost-discovered-item' ).first();
		if ( await item.count() === 0 ) {
			test.skip();
		}

		const slug = await item.getAttribute( 'data-slug' );
		const checkbox = item.locator( '.edminboost-discovered-item__checkbox' );

		if ( ! await checkbox.isChecked() ) {
			await checkbox.check();
		}

		await expect(
			page.locator( `#edminboost-topbar-items .edminboost-topbar-item[data-slug="${ slug }"]` )
		).toBeVisible();
	} );

	test( 'save layout persists canvas items', async ( { page } ) => {
		const item = page.locator( '#edminboost-discovered-list .edminboost-discovered-item' ).first();
		if ( await item.count() === 0 ) {
			test.skip();
		}

		const slug = await item.getAttribute( 'data-slug' );
		const checkbox = item.locator( '.edminboost-discovered-item__checkbox' );

		if ( ! await checkbox.isChecked() ) {
			await checkbox.check();
		}

		await page.getByRole( 'button', { name: 'Save top bar' } ).click();
		await waitForSettingsSaved( page );

		await page.reload();
		await expect(
			page.locator( `#edminboost-topbar-items .edminboost-topbar-item[data-slug="${ slug }"]` )
		).toBeVisible();
	} );

	test( 'drawer preview button opens slide-out drawer', async ( { page } ) => {
		const canvasItem = page.locator( '#edminboost-topbar-items .edminboost-topbar-item' ).first();
		if ( await canvasItem.count() === 0 ) {
			const discovered = page.locator( '#edminboost-discovered-list .edminboost-discovered-item' ).first();
			if ( await discovered.count() === 0 ) {
				test.skip();
			}
			await discovered.locator( '.edminboost-discovered-item__checkbox' ).check();
			await canvasItem.waitFor( { state: 'visible' } );
		}

		await canvasItem.click();
		await page.locator( 'input[name="edminboost_item_interaction"][value="drawer"]' ).check();
		await expect( page.locator( '#edminboost-drawer-preview-wrap' ) ).toBeVisible();

		await page.click( '#edminboost-drawer-preview' );

		const drawer = page.locator( '#edminboost-cc-drawer' );
		await expect( drawer ).toHaveClass( /is-open/ );
		await expect( drawer ).not.toHaveAttribute( 'hidden', '' );

		const iframe = page.locator( '#edminboost-cc-drawer-iframe' );
		await expect( iframe ).toHaveAttribute( 'src', /edminboost_drawer=1/ );
	} );
} );
