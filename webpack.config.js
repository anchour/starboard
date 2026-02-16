/**
 * WordPress dependencies
 */
import defaultConfig from './node_modules/@wordpress/scripts/config/webpack.config.js';
import {merge} from 'webpack-merge';
import path from 'path';
import fs from 'fs';
import {UpdateBlockJsonPathsPlugin} from "./build/UpdateBlockJsonPathsPlugin.js";
import CopyPlugin from 'copy-webpack-plugin';

/**
 * Starboard externals configuration.
 *
 * These allow blocks to import shared utilities that are exposed
 * as globals by the starboard-shared script. This enables theme
 * overrides to use the same imports without bundling the code.
 *
 * Usage in blocks:
 *   import { Breakpoint } from '@starboard/components';
 *   import { screens } from '@starboard/utils';
 */
const starboardExternals = {
	'@starboard/components': ['starboard', 'components'],
	'@starboard/utils': ['starboard', 'utils'],
	'@starboard/config': ['starboard', 'config'],
};

/**
 * Get list of block directories from src/blocks.
 *
 * @returns {string[]} Array of block directory names
 */
function getBlockDirectories() {
	const blocksPath = path.resolve(process.cwd(), 'src/blocks');

	if (!fs.existsSync(blocksPath)) {
		console.warn('Starboard: src/blocks directory not found');
		return [];
	}

	try {
		return fs.readdirSync(blocksPath).filter((item) => {
			const itemPath = path.join(blocksPath, item);
			return fs.statSync(itemPath).isDirectory();
		});
	} catch (error) {
		console.error('Starboard: Error reading blocks directory:', error.message);
		return [];
	}
}

const scripts = getBlockDirectories();

// Editor script entries
const entries = scripts.reduce((memo, script) => {
	const blockPath = path.resolve(process.cwd(), `src/blocks/${script}`);

	// Add editor entry if it exists
	const editorPath = `${blockPath}/index.js`;
	if (fs.existsSync(editorPath)) {
		memo[`blocks/${script}/${script}.editor`] = editorPath;
	}

	// Add frontend entry if it exists
	const frontendPath = `${blockPath}/frontend.js`;
	if (fs.existsSync(frontendPath)) {
		memo[`blocks/${script}/${script}.frontend`] = frontendPath;
	}

	return memo;
}, {});

/**
 * Find nested sub-blocks (blocks inside other blocks).
 *
 * @returns {Array<{from: string, to: string}>} Copy patterns for nested block.json files
 */
function getNestedBlockPatterns() {
	const patterns = [];

	scripts.forEach((script) => {
		const blockPath = path.resolve(process.cwd(), `src/blocks/${script}`);

		try {
			if (!fs.existsSync(blockPath) || !fs.statSync(blockPath).isDirectory()) {
				return;
			}

			// Look for subdirectories with block.json
			const items = fs.readdirSync(blockPath);
			items.forEach((item) => {
				const itemPath = path.join(blockPath, item);
				const blockJsonPath = path.join(itemPath, 'block.json');

				try {
					if (fs.statSync(itemPath).isDirectory() && fs.existsSync(blockJsonPath)) {
						patterns.push({
							from: blockJsonPath,
							to: path.join('blocks', script, item, 'block.json'),
						});
					}
				} catch {
					// Skip items that can't be accessed
				}
			});
		} catch (error) {
			console.warn(`Starboard: Error scanning ${script} for nested blocks:`, error.message);
		}
	});

	return patterns;
}

const nestedBlockPatterns = getNestedBlockPatterns();

const config = merge(defaultConfig, {
	entry: {
		'starboard': path.resolve(process.cwd(), 'src/index.js'),
		'starboard-shared': path.resolve(process.cwd(), 'src/shared/index.js'),

		...entries
	},

	output: {
		path: path.resolve(process.cwd(), 'dist/'),
		publicPath: 'auto',
	},

	externals: starboardExternals,

	plugins: [
		new UpdateBlockJsonPathsPlugin(),
		// Copy nested sub-block block.json files
		...(nestedBlockPatterns.length > 0 ? [new CopyPlugin({patterns: nestedBlockPatterns})] : []),
	],

	resolve: {
		extensions: ['.js',],
	}
});

export const parallelism = 1;

export default config;
