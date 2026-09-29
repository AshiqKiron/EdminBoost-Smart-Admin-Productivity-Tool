#!/usr/bin/env node
/**
 * Capture EdminBoost admin screenshots for docs (local MAMP).
 *
 * Usage: node bin/capture-doc-screenshots.mjs
 * Env: BASE_URL, ADMIN_USER, ADMIN_PASSWORD, PLUGIN_SLUG (or .env.qa)
 */
import dotenv from 'dotenv';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { chromium } from '@playwright/test';

const __dirname = path.dirname( fileURLToPath( import.meta.url ) );
const root = path.resolve( __dirname, '..' );

dotenv.config( { path: path.join( root, '.env.qa' ) } );

const baseUrl = ( process.env.BASE_URL || 'http://localhost:8888/wordpress' ).replace( /\/$/, '' );
const adminUser = process.env.ADMIN_USER || 'qaadmin';
const adminPassword = process.env.ADMIN_PASSWORD || 'qaadmin123';
const pluginSlug = process.env.PLUGIN_SLUG || 'edminboost-admin-customization';

const shots = [
	{ file: 'command-center/dashboard.png', path: `wp-admin/admin.php?page=${ pluginSlug }` },
	{ file: 'command-center/layout-presets.png', path: `wp-admin/admin.php?page=${ pluginSlug }-presets` },
	{ file: 'command-center/theme-appearance.png', path: `wp-admin/admin.php?page=${ pluginSlug }-appearance` },
	{ file: 'command-center/top-bar.png', path: `wp-admin/admin.php?page=${ pluginSlug }-mapper` },
	{ file: 'command-center/menu-studio.png', path: `wp-admin/admin.php?page=${ pluginSlug }-menu` },
	{ file: 'features/productivity-tools.png', path: `wp-admin/admin.php?page=${ pluginSlug }-productivity` },
	{ file: 'features/security-tools.png', path: `wp-admin/admin.php?page=${ pluginSlug }-security` },
	{ file: 'features/performance-tools.png', path: `wp-admin/admin.php?page=${ pluginSlug }-performance` },
];

const outRoot = path.join( root, 'docs', 'screenshots' );

async function login( page ) {
	await page.goto( `${ baseUrl }/wp-login.php`, { waitUntil: 'domcontentloaded' } );
	await page.getByRole( 'textbox', { name: 'Username or Email Address' } ).fill( adminUser );
	await page.getByRole( 'textbox', { name: 'Password', exact: true } ).fill( adminPassword );
	await page.getByRole( 'button', { name: 'Log In' } ).click();
	await page.waitForURL( /\/wp-admin\//, { timeout: 30000 } );
	await page.locator( '#wpadminbar' ).waitFor( { state: 'visible', timeout: 15000 } );
}

async function capture() {
	fs.mkdirSync( outRoot, { recursive: true } );
	for ( const dir of [ 'command-center', 'features' ] ) {
		fs.mkdirSync( path.join( outRoot, dir ), { recursive: true } );
		fs.writeFileSync(
			path.join( outRoot, dir, 'index.php' ),
			'<?php\n// Silence is golden.\n'
		);
	}
	fs.writeFileSync( path.join( outRoot, 'index.php' ), '<?php\n// Silence is golden.\n' );

	const browser = await chromium.launch( { headless: true } );
	const context = await browser.newContext( {
		viewport: { width: 1440, height: 900 },
		deviceScaleFactor: 2,
	} );
	const page = await context.newPage();

	await login( page );

	for ( const shot of shots ) {
		const url = `${ baseUrl }/${ shot.path }`;
		await page.goto( url, { waitUntil: 'networkidle', timeout: 60000 } );
		await page.locator( '.edminboost-wrap[data-edminboost-ready="true"]' ).waitFor( {
			state: 'visible',
			timeout: 30000,
		} );
		await page.waitForTimeout( 500 );
		const dest = path.join( outRoot, shot.file );
		await page.screenshot( { path: dest, fullPage: true } );
		console.log( 'Wrote', dest );
	}

	await browser.close();
}

capture().catch( ( err ) => {
	console.error( err );
	process.exit( 1 );
} );
