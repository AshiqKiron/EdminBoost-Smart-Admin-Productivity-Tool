import dotenv from 'dotenv';
import path from 'path';

dotenv.config( { path: path.resolve( __dirname, '../../../.env.qa' ) } );

const pluginSlug = process.env.PLUGIN_SLUG || 'edminboost-smart-admin-productivity-tool';

export const pages = {
	dashboard: `wp-admin/admin.php?page=${ pluginSlug }`,
	appearance: `wp-admin/admin.php?page=${ pluginSlug }-appearance`,
	settings: `wp-admin/admin.php?page=${ pluginSlug }-settings`,
	onboarding: `wp-admin/admin.php?page=${ pluginSlug }-onboarding`,
	mapper: `wp-admin/admin.php?page=${ pluginSlug }-mapper`,
	presets: `wp-admin/admin.php?page=${ pluginSlug }-presets`,
	behavior: `wp-admin/admin.php?page=${ pluginSlug }-behavior`,
};
