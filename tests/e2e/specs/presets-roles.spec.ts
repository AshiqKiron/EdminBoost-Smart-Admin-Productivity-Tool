import { test, expect } from '@playwright/test';
import { pages } from '../fixtures/pages';

test.describe( 'Presets', () => {
	test( 'presets page loads preset grid', async ( { page } ) => {
		await page.goto( pages.presets );
		await expect( page.locator( '.edminboost-preset-grid' ) ).toBeVisible();
		await expect( page.getByRole( 'button', { name: 'Apply preset' } ).first() ).toBeVisible();
	} );
} );
