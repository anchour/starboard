/**
 * WordPress dependencies
 */
import {registerBlockType} from '@wordpress/blocks';
import {addFilter} from '@wordpress/hooks';

/**
 * Editor styles
 */
import './editor.css';

/**
 * Internal dependencies - main block
 */
import metadata from './block.json';
import edit from './edit.js';
import save from './save.js';
import variations from './variations.js';
import {breakpoints} from './breakpoints.js';

/**
 * Internal dependencies - column sub-block
 */
import columnMetadata from './column/block.json';
import columnEdit from './column/edit.js';
import columnSave from './column/save.js';

/**
 * Register the column sub-block first (must exist before parent)
 */
registerBlockType(columnMetadata, {
  edit: columnEdit,
  save: columnSave,
});

/**
 * Register the main text-image block
 */
registerBlockType(metadata, {
  edit,
  save,
  variations,
});

/**
 * Add breakpoints HOC for layout controls
 */
addFilter('editor.BlockEdit', 'starboard/text-image-breakpoints', breakpoints);
