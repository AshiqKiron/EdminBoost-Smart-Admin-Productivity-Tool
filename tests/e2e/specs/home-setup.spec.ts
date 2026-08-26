import { test, expect } from '@playwright/test';
import { pages } from '../fixtures/pages';

test.describe( 'Home setup', () => {
	test( 'shows setup cards on first visit', async ( { page } ) => {
		await page.goto( pages.dashboard );
		await expect( page.locator( '.edminboost-wrap[data-edminboost-ready="true"]' ) ).toBeVisible();
		await expect( page.locator( '.edminboost-setup-grid, .edminboost-home-status' ) ).toBeVisible();
	} );

	test( 'look skin cards select on click', async ( { page } ) => {
		await page.goto( pages.dashboard );
		await expect( page.locator( '.edminboost-skin-grid' ) ).toBeVisible();

		const focusedCard = page.locator( '.edminboost-skin-card' ).filter( {
			has: page.locator( 'input[value="focused"]' ),
		} );
		await focusedCard.click();
		await expect( focusedCard ).toHaveClass( /is-selected/ );
	} );

	test( 'legacy onboarding URL redirects to Home', async ( { page } ) => {
		await page.goto( pages.onboarding );
		await expect( page ).toHaveURL( new RegExp( `page=${ pages.dashboard.split( 'page=' )[1] }$` ) );
	} );
} );
