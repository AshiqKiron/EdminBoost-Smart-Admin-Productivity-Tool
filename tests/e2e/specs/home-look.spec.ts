import { test, expect } from '@playwright/test';
import { pages } from '../fixtures/pages';

test.describe( 'Home look settings', () => {
	test( 'badge style preview updates on selection', async ( { page } ) => {
		await page.goto( pages.dashboard );
		await page.locator( '.edminboost-advanced-look summary' ).click();
		await expect( page.locator( '.edminboost-badge-preview' ) ).toBeVisible();

		const pillRadio = page.locator( 'input[name="edminboost_settings[command_center][behavior][badge_style]"][value="pill"]' );
		await pillRadio.check();

		await expect(
			page.locator( '.edminboost-badge-preview__item[data-style="pill"]' )
		).toBeVisible();
	} );

	test( 'drawer width options are present in advanced settings', async ( { page } ) => {
		await page.goto( pages.dashboard );
		await page.locator( '.edminboost-advanced-look summary' ).click();

		await expect(
			page.locator( 'input[name="edminboost_settings[command_center][behavior][drawer_width]"]' )
		).toHaveCount( 4 );
		await expect( page.locator( '#edminboost_drawer_width_custom' ) ).toHaveAttribute( 'min', '400' );
		await expect( page.locator( '#edminboost_drawer_width_custom' ) ).toHaveAttribute( 'max', '800' );

		const customRadio = page.locator(
			'input[name="edminboost_settings[command_center][behavior][drawer_width]"][value="custom"]'
		);
		await customRadio.check();
		await expect( page.locator( '#edminboost-drawer-width-preview' ) ).toBeVisible();
		await expect( page.locator( '#edminboost-drawer-width-preview-caption' ) ).not.toBeEmpty();
	} );

	test( 'legacy behavior URL redirects to Home', async ( { page } ) => {
		await page.goto( pages.behavior );
		await expect( page ).toHaveURL( new RegExp( `page=${ pages.dashboard.split( 'page=' )[1] }$` ) );
	} );
} );
