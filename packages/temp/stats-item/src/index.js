/**
 * Internal dependencies.
 */
import edit from './edit';
import metadata from './block.json';
import save from './save';
import v1Save from './v1/save';
import v2Save from './v2/save';
import v2Metadata from './v2/block.json';

/**
 * WordPress dependencies.
 */
const {registerBlockType} = wp.blocks;

/**
 * Register block.
 */
registerBlockType(metadata, {
  edit,
  save,
  deprecated: [
    {
      attributes: metadata.attributes,
      save: v1Save,
    },
    {
      attributes: v2Metadata.attributes,
      save: v2Save
    }
  ],
});
