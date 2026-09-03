import { test, expect } from '@playwright/test';
import { pages } from '../fixtures/pages';
import { waitForSettingsSaved } from '../fixtures/helpers';

test.describe( 'Presets', () => {
	test( 'presets page loads layout preset picker', async ( { page } ) => {
		await page.goto( pages.presets );
		await expect( page.locator( '#edminboost-layout-preset-picker' ) ).toBeVisible();
		await expect( page.locator( '#edminboost-layout-preset-preview' ) ).toBeVisible();
		await expect( page.getByRole( 'button', { name: 'Apply preset' } ) ).toBeVisible();
	} );

	test( 'can save the current layout as a named custom preset', async ( { page } ) => {
		await page.goto( pages.presets );

		const saveButton = page.getByRole( 'button', { name: 'Save current layout as preset' } );
		await expect( saveButton ).toBeEnabled();

		await saveButton.click();
		await page.locator( '#edminboost_save_preset_name_input' ).fill( 'QA Named Layout' );
		await page.getByRole( 'button', { name: 'Save preset', exact: true } ).click();

		await waitForSettingsSaved( page );
		await expect( page.locator( '#edminboost-layout-preset-name' ) ).toHaveText( 'QA Named Layout' );
		await expect(
			page.locator( '#edminboost-layout-preset-list .edminboost-layout-preset-picker__option-name', {
				hasText: 'QA Named Layout',
			} )
		).toHaveCount( 1 );
	} );
} );
