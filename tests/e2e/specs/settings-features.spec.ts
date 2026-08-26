import { test, expect } from '@playwright/test';
import { pages } from '../fixtures/pages';
import { waitForSettingsSaved } from '../fixtures/helpers';

test.describe( 'Settings & features', () => {
	test( 'settings page loads and saves global enable toggle', async ( { page } ) => {
		await page.goto( pages.settings );
		await expect( page.locator( '.edminboost-wrap' ) ).toBeVisible();

		const enabledCheckbox = page.locator( 'input[name="edminboost_settings[enabled]"]' );
		await expect( enabledCheckbox ).toBeVisible();

		const initialUrl = page.url();
		const wasChecked = await enabledCheckbox.isChecked();
		if ( wasChecked ) {
			await enabledCheckbox.uncheck();
		} else {
			await enabledCheckbox.check();
		}

		await page.click( '#submit' );
		await waitForSettingsSaved( page );
		expect( page.url() ).toBe( initialUrl );
		await expect( page.locator( '.edminboost-save-notice.notice-success' ) ).toBeVisible();

		await enabledCheckbox.setChecked( wasChecked );
		await page.click( '#submit' );
		await waitForSettingsSaved( page );
	} );

	test( 'hide admin notices checkbox is present', async ( { page } ) => {
		await page.goto( pages.settings );
		await expect(
			page.locator( 'input[name="edminboost_settings[features][hide_admin_notices]"]' )
		).toBeVisible();
	} );
} );
