import { test, expect } from '@playwright/test';
import { pages } from '../fixtures/pages';

test.describe( 'Presets', () => {
	test( 'presets page loads layout preset picker', async ( { page } ) => {
		await page.goto( pages.presets );
		await expect( page.locator( '#edminboost-layout-preset-picker' ) ).toBeVisible();
		await expect( page.locator( '#edminboost-layout-preset-preview' ) ).toBeVisible();
		await expect( page.getByRole( 'button', { name: 'Apply preset' } ) ).toBeVisible();
	} );
} );
