import { Page } from '@playwright/test';

/**
 * Wait for a successful settings save (AJAX notice or legacy redirect).
 */
export async function waitForSettingsSaved( page: Page ) {
	const notice = page.locator( '.edminboost-save-notice.notice-success' );

	try {
		await notice.waitFor( { state: 'visible', timeout: 10000 } );
		return;
	} catch ( error ) {
		await page.waitForURL( /settings-updated=true/, { timeout: 15000 } );
	}
}
