import { test, expect } from '@playwright/test';
import { pages } from '../fixtures/pages';
import { waitForSettingsSaved } from '../fixtures/helpers';

test.describe( 'Live admin bar', () => {
	test( 'configured drawer item opens and closes drawer', async ( { page } ) => {
		await page.goto( pages.mapper );

		const discovered = page.locator( '#edminboost-discovered-list .edminboost-discovered-item' ).first();
		if ( await discovered.count() === 0 ) {
			test.skip();
		}

		const slug = await discovered.getAttribute( 'data-slug' );
		const checkbox = discovered.locator( '.edminboost-discovered-item__checkbox' );

		if ( ! await checkbox.isChecked() ) {
			await checkbox.check();
		}

		const canvasItem = page.locator( `#edminboost-topbar-items .edminboost-topbar-item[data-slug="${ slug }"]` );
		await canvasItem.click();
		await page.locator( 'input[name="edminboost_item_interaction"][value="drawer"]' ).check();

		await page.getByRole( 'button', { name: 'Save top bar' } ).click();
		await waitForSettingsSaved( page );

		await page.goto( pages.dashboard );

		const barTrigger = page.locator( '#wpadminbar .edminboost-cc-bar-drawer-trigger > .ab-item' ).first();
		await expect( barTrigger ).toBeVisible( { timeout: 10000 } );

		await barTrigger.click();

		const drawer = page.locator( '#edminboost-cc-drawer' );
		await expect( drawer ).toHaveClass( /is-open/ );

		await page.keyboard.press( 'Escape' );
		await expect( drawer ).not.toHaveClass( /is-open/ );
	} );
} );
