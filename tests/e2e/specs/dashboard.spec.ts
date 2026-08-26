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

	test( 'dashboard overview cards show dropdown pickers when setup is complete', async ( { page } ) => {
		await page.goto( pages.dashboard );
		const overview = page.locator( '.edminboost-dashboard-overview' );

		if ( await overview.isVisible() ) {
			await expect( page.locator( '#edminboost-layout-preset-picker' ) ).toBeVisible();
			await expect( page.locator( '#edminboost-theme-preset-picker' ) ).toBeVisible();
			await expect( page.locator( '#edminboost-overview-topbar-links-picker' ) ).toBeVisible();
		}
	} );

	test( 'dashboard overview dropdowns stay open when toggled', async ( { page } ) => {
		await page.goto( pages.dashboard );
		const overview = page.locator( '.edminboost-dashboard-overview' );

		if ( ! await overview.isVisible() ) {
			return;
		}

		const layoutToggle = page.locator( '#edminboost_layout_preset_toggle' );
		const layoutList = page.locator( '#edminboost-layout-preset-list' );
		const themeToggle = page.locator( '#edminboost_theme_preset_toggle' );
		const themeList = page.locator( '#edminboost-theme-preset-list' );

		await layoutToggle.click();
		await expect( layoutList ).toBeVisible();
		await expect( layoutToggle ).toHaveAttribute( 'aria-expanded', 'true' );

		await layoutToggle.click();

		await themeToggle.click();
		await expect( themeList ).toBeVisible();
		await expect( themeToggle ).toHaveAttribute( 'aria-expanded', 'true' );
	} );
} );
