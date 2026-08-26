import { test, expect } from '@playwright/test';
import { pages } from '../fixtures/pages';

test.describe( 'Home setup', () => {
	test( 'shows setup wizard or dashboard overview', async ( { page } ) => {
		await page.goto( pages.dashboard );
		await expect( page.locator( '.edminboost-wrap[data-edminboost-ready="true"]' ) ).toBeVisible();
		await expect(
			page.locator( '.edminboost-setup-wizard, .edminboost-dashboard-overview' )
		).toBeVisible();
	} );

	test( 'setup wizard stepper is visible when setup is incomplete', async ( { page } ) => {
		await page.goto( pages.dashboard );
		const wizard = page.locator( '.edminboost-setup-wizard' );

		if ( await wizard.isVisible() ) {
			await expect( page.locator( '.edminboost-setup-stepper' ) ).toBeVisible();
			await expect( page.locator( '#edminboost-setup-step-1' ) ).toBeVisible();
		}
	} );

	test( 'legacy onboarding URL redirects to Home', async ( { page } ) => {
		await page.goto( pages.onboarding );
		await expect( page ).toHaveURL( new RegExp( `page=${ pages.dashboard.split( 'page=' )[1] }$` ) );
	} );
} );
