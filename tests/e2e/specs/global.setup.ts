import { test as setup, expect } from '@playwright/test';
import path from 'path';
import fs from 'fs';
import dotenv from 'dotenv';

dotenv.config( { path: path.resolve( __dirname, '../../../.env.qa' ) } );

const authDir = path.join( __dirname, '../.auth' );
const authFile = path.join( authDir, 'admin.json' );
const adminUser = process.env.ADMIN_USER || 'qaadmin';
const adminPassword = process.env.ADMIN_PASSWORD || 'qaadmin123';

setup( 'authenticate as admin', async ( { page } ) => {
	await page.goto( 'wp-login.php' );
	await page.getByRole( 'textbox', { name: 'Username or Email Address' } ).fill( adminUser );
	await page.getByRole( 'textbox', { name: 'Password', exact: true } ).fill( adminPassword );
	await page.getByRole( 'button', { name: 'Log In' } ).click();
	await page.waitForURL( /\/wp-admin\// );
	await expect( page.locator( '#wpadminbar' ) ).toBeVisible( { timeout: 15000 } );
	fs.mkdirSync( authDir, { recursive: true } );
	await page.context().storageState( { path: authFile } );
} );
