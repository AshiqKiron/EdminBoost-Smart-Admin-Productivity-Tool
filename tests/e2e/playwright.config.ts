import { defineConfig, devices } from '@playwright/test';
import dotenv from 'dotenv';
import path from 'path';

dotenv.config( { path: path.resolve( __dirname, '../../.env.qa' ) } );

const rawBaseUrl = process.env.BASE_URL || 'http://localhost:8888/wordpress';
const baseURL = rawBaseUrl.endsWith( '/' ) ? rawBaseUrl : `${ rawBaseUrl }/`;

export default defineConfig( {
	testDir: './specs',
	fullyParallel: false,
	forbidOnly: !! process.env.CI,
	retries: process.env.CI ? 1 : 0,
	workers: 1,
	reporter: [ [ 'html', { open: 'never' } ], [ 'list' ] ],
	timeout: 60000,
	use: {
		baseURL,
		trace: 'on-first-retry',
		screenshot: 'only-on-failure',
		video: 'retain-on-failure',
	},
	projects: [
		{
			name: 'setup',
			testMatch: /global\.setup\.ts/,
		},
		{
			name: 'chromium',
			use: {
				...devices['Desktop Chrome'],
				storageState: path.join( __dirname, '.auth/admin.json' ),
			},
			dependencies: [ 'setup' ],
		},
	],
} );
