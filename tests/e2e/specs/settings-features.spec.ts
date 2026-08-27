import { test, expect } from '@playwright/test';
import { pages } from '../fixtures/pages';
import { waitForSettingsSaved } from '../fixtures/helpers';

test.describe( 'Productivity features', () => {
	test( 'productivity page loads and saves a feature toggle', async ( { page } ) => {
		await page.goto( pages.productivity );
		await expect( page.locator( '.edminboost-wrap' ) ).toBeVisible();
		await expect( page.locator( '.edminboost-cc-nav__link.is-active' ) ).toHaveText( 'Productivity' );

		const noticesCheckbox = page.locator(
			'input[name="edminboost_settings[features][hide_admin_notices]"]'
		);
		await expect( noticesCheckbox ).toBeVisible();

		const initialUrl = page.url();
		const wasChecked = await noticesCheckbox.isChecked();
		if ( wasChecked ) {
			await noticesCheckbox.uncheck();
		} else {
			await noticesCheckbox.check();
		}

		await page.click( '#submit' );
		await waitForSettingsSaved( page );
		expect( page.url() ).toBe( initialUrl );
		await expect( page.locator( '.edminboost-save-notice.notice-success' ) ).toBeVisible();

		await noticesCheckbox.setChecked( wasChecked );
		await page.click( '#submit' );
		await waitForSettingsSaved( page );
	} );

	test( 'hide admin notices checkbox is present', async ( { page } ) => {
		await page.goto( pages.productivity );
		await expect(
			page.locator( 'input[name="edminboost_settings[features][hide_admin_notices]"]' )
		).toBeVisible();
	} );
} );
