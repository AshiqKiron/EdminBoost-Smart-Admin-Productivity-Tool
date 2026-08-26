import { test, expect } from '@playwright/test';
import { pages } from '../fixtures/pages';

test.describe( 'Home', () => {
	test( 'loads plugin home with ready marker', async ( { page } ) => {
		await page.goto( pages.dashboard );
		await expect( page.locator( '.edminboost-wrap' ) ).toBeVisible();
		await expect( page.locator( '.edminboost-wrap[data-edminboost-ready="true"]' ) ).toBeVisible();
	} );

	test( 'shows home dashboard sections', async ( { page } ) => {
		await page.goto( pages.dashboard );
		await expect( page.locator( '.edminboost-dashboard' ) ).toBeVisible();
		await expect( page.locator( '#edminboost-look-skins' ) ).toBeVisible();
	} );
} );
