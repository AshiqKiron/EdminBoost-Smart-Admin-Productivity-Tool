import { test, expect } from '@playwright/test';
import { pages } from '../fixtures/pages';

test.describe( 'Dashboard', () => {
	test( 'loads plugin home with ready marker', async ( { page } ) => {
		await page.goto( pages.dashboard );
		await expect( page.locator( '.edminboost-wrap' ) ).toBeVisible();
		await expect( page.locator( '.edminboost-wrap[data-edminboost-ready="true"]' ) ).toBeVisible();
	} );

	test( 'shows dashboard hub content', async ( { page } ) => {
		await page.goto( pages.dashboard );
		await expect( page.locator( '.edminboost-dashboard' ) ).toBeVisible();
		await expect(
			page.locator( '.edminboost-setup-wizard, .edminboost-dashboard-overview' )
		).toBeVisible();
	} );
} );
