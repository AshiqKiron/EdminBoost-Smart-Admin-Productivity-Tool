#!/usr/bin/env node
/**
 * Capture 15 WordPress.org plugin screenshots (free-tier UI) on local MAMP.
 *
 * Usage: node bin/capture-wporg-screenshots.mjs
 * Env: BASE_URL, ADMIN_USER, ADMIN_PASSWORD, PLUGIN_SLUG, WP_PATH (for seeding)
 */
import { execSync } from 'child_process';
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
const wpPath = process.env.WP_PATH || '/Applications/MAMP/htdocs/wordpress';
const mampPhp = process.env.MAMP_PHP || '/Applications/MAMP/bin/php/php8.3.28/bin/php';
const wpCli = path.join( root, 'bin', 'wp-cli.phar' );

const outDir = path.join( root, 'assets' );

if ( ! process.env.PLAYWRIGHT_BROWSERS_PATH ) {
	process.env.PLAYWRIGHT_BROWSERS_PATH = path.join( root, '.playwright-browsers' );
}

const pageUrl = ( suffix ) => `wp-admin/admin.php?page=${ pluginSlug }${ suffix }`;

/**
 * @type {Array<{ file: string; path: string; scroll?: string; fullPage?: boolean }>}
 */
const shots = [
	{ file: 'screenshot-1.png', path: pageUrl( '' ), scroll: '#edminboost-setup-wizard-form' },
	{ file: 'screenshot-2.png', path: pageUrl( '' ), scroll: '#edminboost-dashboard-overview-form' },
	{ file: 'screenshot-3.png', path: pageUrl( '-presets' ) },
	{ file: 'screenshot-4.png', path: pageUrl( '-appearance' ), scroll: '#edminboost-theme-preset-picker' },
	{ file: 'screenshot-5.png', path: pageUrl( '-appearance' ), scroll: '#edminboost-declutter-preview' },
	{ file: 'screenshot-6.png', path: pageUrl( '-mapper' ) },
	{ file: 'screenshot-7.png', path: pageUrl( '-menu' ) },
	{ file: 'screenshot-8.png', path: pageUrl( '-productivity' ), scroll: '#edminboost-productivity-hide-notices-layout' },
	{ file: 'screenshot-9.png', path: pageUrl( '-productivity' ), scroll: '#edminboost-productivity-dashboard-widgets-layout' },
	{ file: 'screenshot-10.png', path: pageUrl( '-productivity' ), scroll: '#edminboost-admin-footer-options' },
	{ file: 'screenshot-11.png', path: pageUrl( '-security' ) },
	{ file: 'screenshot-12.png', path: pageUrl( '-performance' ), scroll: '#edminboost-performance-emoji-preview' },
	{ file: 'screenshot-13.png', path: pageUrl( '-performance' ), scroll: '#edminboost-performance-assets-preview' },
	{ file: 'screenshot-14.png', path: 'wp-admin/index.php' },
	{ file: 'screenshot-15.png', path: 'wp-admin/edit.php' },
];

const localhostLoader = path.join( root, 'edminboost-localhost.php' );
const localhostBackup = path.join( root, 'edminboost-localhost.php.wporg-capture-bak' );

function disableLocalhostPro() {
	if ( fs.existsSync( localhostLoader ) && ! fs.existsSync( localhostBackup ) ) {
		fs.renameSync( localhostLoader, localhostBackup );
		console.log( 'Temporarily disabled edminboost-localhost.php for free-tier screenshots.' );
	}
}

function restoreLocalhostPro() {
	if ( fs.existsSync( localhostBackup ) && ! fs.existsSync( localhostLoader ) ) {
		fs.renameSync( localhostBackup, localhostLoader );
		console.log( 'Restored edminboost-localhost.php.' );
	}
}

function runSeed( mode ) {
	const env = { ...process.env, EDMINBOOST_SCREENSHOT_SEED: mode };
	const cmd = `"${ mampPhp }" -d memory_limit=512M "${ wpCli }" eval-file "${ path.join( root, 'bin', 'seed-wporg-screenshot-demo.php' ) }" --path="${ wpPath }"`;
	execSync( cmd, { stdio: 'inherit', env } );
}

async function login( page ) {
	await page.goto( `${ baseUrl }/wp-login.php`, { waitUntil: 'domcontentloaded' } );
	await page.getByRole( 'textbox', { name: 'Username or Email Address' } ).fill( adminUser );
	await page.getByRole( 'textbox', { name: 'Password', exact: true } ).fill( adminPassword );
	await page.getByRole( 'button', { name: 'Log In' } ).click();
	await page.waitForURL( /\/wp-admin\//, { timeout: 30000 } );
	await page.locator( '#wpadminbar' ).waitFor( { state: 'visible', timeout: 15000 } );
}

async function waitForEdminboost( page, shot ) {
	if ( shot.path.includes( 'page=' + pluginSlug ) ) {
		await page.locator( '.edminboost-wrap[data-edminboost-ready="true"]' ).waitFor( {
			state: 'visible',
			timeout: 30000,
		} );
	} else {
		await page.waitForLoadState( 'networkidle', { timeout: 60000 } ).catch( () => {} );
	}
}

async function dismissAdminNotices( page ) {
	const dismiss = page.locator( '.notice.is-dismissible .notice-dismiss' );
	const count = await dismiss.count();
	for ( let i = 0; i < Math.min( count, 6 ); i++ ) {
		await dismiss.nth( i ).click( { timeout: 2000 } ).catch( () => {} );
	}
}

async function captureShot( page, shot ) {
	const url = `${ baseUrl }/${ shot.path }`;
	await page.goto( url, { waitUntil: 'domcontentloaded', timeout: 60000 } );
	await waitForEdminboost( page, shot );
	await dismissAdminNotices( page );

	if ( shot.scroll ) {
		const target = page.locator( shot.scroll ).first();
		if ( await target.count() ) {
			await target.scrollIntoViewIfNeeded();
			await page.waitForTimeout( 400 );
		}
	} else {
		await page.waitForTimeout( 300 );
	}

	const dest = path.join( outDir, shot.file );
	await page.screenshot( {
		path: dest,
		fullPage: shot.fullPage ?? false,
	} );
	console.log( 'Wrote', dest );
}

async function capture() {
	if ( ! fs.existsSync( wpCli ) ) {
		console.error( 'Missing bin/wp-cli.phar — run bin/setup-qa-site.sh first.' );
		process.exit( 1 );
	}

	fs.mkdirSync( outDir, { recursive: true } );

	disableLocalhostPro();

	try {
		runSeed( 'wizard' );

		const browser = await chromium.launch( { headless: true } );
		const context = await browser.newContext( {
			viewport: { width: 1280, height: 900 },
			deviceScaleFactor: 2,
		} );
		const page = await context.newPage();

		await login( page );
		await captureShot( page, shots[ 0 ] );

		runSeed( 'demo' );

		await login( page );

		for ( let i = 1; i < shots.length; i++ ) {
			await captureShot( page, shots[ i ] );
		}

		await browser.close();
	} finally {
		restoreLocalhostPro();
	}
}

capture().catch( ( err ) => {
	restoreLocalhostPro();
	console.error( err );
	process.exit( 1 );
} );
