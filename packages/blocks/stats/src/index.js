/**
 * Internal dependencies.
 */
import {breakpoints} from './breakpoints';
import edit from './edit';
import metadata from './block.json';
import save from './save';
import v1Save from '@scripts/blocks/stats/deprecated/v1/save';
import v2Save from '@scripts/blocks/stats/deprecated/v2/save';

/**
 * WordPress dependencies.
 */
const {
  blocks: {registerBlockType},
  hooks: {addFilter},
} = wp;

/**
 * Register block.
 */
registerBlockType(metadata, {
  edit,
  save,
  deprecated: [
    {
      attributes: metadata.attributes,
      supports: metadata.supports,
      save: v1Save,
    },
    {
      attributes: metadata.attributes,
      supports: metadata.supports,
      save: v2Save,
    }
  ],
});

addFilter(
  'blocks.registerBlockType',
  'anchour/stats',

  (settings, name) => {
    if (name !== 'anchour/stats') {
      return settings;
    }

    settings.attributes = {
      ...settings.attributes,

      breakpoints: {
        type: 'array',
        default: [
          {
            name: 'Default',
            settings: {
              perRow: 4,
            },
          },
        ],
      },
    };
  },
);
addFilter('editor.BlockEdit', 'anchour/stats', breakpoints);
