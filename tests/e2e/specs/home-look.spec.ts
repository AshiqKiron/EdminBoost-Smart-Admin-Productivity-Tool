import { test, expect } from '@playwright/test';
import { pages } from '../fixtures/pages';

test.describe( 'Appearance settings', () => {
	test( 'badge style preview updates on selection', async ( { page } ) => {
		await page.goto( pages.appearance );
		await expect( page.locator( '.edminboost-badge-preview' ) ).toBeVisible();

		const pillRadio = page.locator( 'input[name="edminboost_settings[command_center][behavior][badge_style]"][value="pill"]' );
		await pillRadio.check();

		await expect(
			page.locator( '.edminboost-badge-preview__item[data-style="pill"]' )
		).toBeVisible();
	} );

	test( 'drawer width options are present in appearance settings', async ( { page } ) => {
		await page.goto( pages.appearance );

		await expect(
			page.locator( 'input[name="edminboost_settings[command_center][behavior][drawer_width]"]' )
		).toHaveCount( 4 );
		await expect( page.locator( '#edminboost_drawer_width_custom' ) ).toHaveAttribute( 'min', '400' );
		await expect( page.locator( '#edminboost_drawer_width_custom' ) ).toHaveAttribute( 'max', '800' );

		await expect( page.locator( '#edminboost-drawer-width-preview' ) ).toBeVisible();
		await expect( page.locator( '#edminboost-drawer-width-preview-caption' ) ).not.toBeEmpty();

		const compactRadio = page.locator(
			'input[name="edminboost_settings[command_center][behavior][drawer_width]"][value="compact"]'
		);
		await compactRadio.check();
		await expect( page.locator( '#edminboost-drawer-width-preview-caption' ) ).toContainText( '400' );

		const customRadio = page.locator(
			'input[name="edminboost_settings[command_center][behavior][drawer_width]"][value="custom"]'
		);
		await customRadio.check();
		await expect( page.locator( '#edminboost-drawer-width-custom' ) ).toBeVisible();
	} );

	test( 'declutter toggles are available on appearance page', async ( { page } ) => {
		await page.goto( pages.appearance );
		await expect( page.locator( '#edminboost_hide_wp_logo' ) ).toBeVisible();
		await expect( page.locator( '#edminboost_hide_comments' ) ).toBeVisible();
	} );

	test( 'declutter preview hides admin bar items when toggles are checked', async ( { page } ) => {
		await page.goto( pages.appearance );
		await expect( page.locator( '#edminboost-declutter-preview' ) ).toBeVisible();

		const logoItem = page.locator( '#edminboost-declutter-preview [data-preview="hide_wp_logo"]' );
		await expect( logoItem ).toBeVisible();
		await expect( logoItem ).not.toHaveClass( /is-hidden/ );

		await page.locator( '#edminboost_hide_wp_logo' ).check();
		await expect( logoItem ).toHaveClass( /is-hidden/ );

		await page.locator( '#edminboost_hide_wp_logo' ).uncheck();
		await expect( logoItem ).not.toHaveClass( /is-hidden/ );
	} );

	test( 'legacy behavior URL redirects to Appearance', async ( { page } ) => {
		await page.goto( pages.behavior );
		await expect( page ).toHaveURL( new RegExp( `page=${ pages.appearance.split( 'page=' )[1] }$` ) );
	} );
} );
