import metadata from './block.json';
import {createBlock, registerBlockType} from '@wordpress/blocks';
import edit from './edit.js'
import save from './save.js'
import v1Save from './deprecated/v1/save.js'

registerBlockType(metadata, {
	edit,
	save,
	deprecated: [
		{
			attributes: metadata.attributes,

			// Convert attributes.content to innerBlocks
			migrate: (attributes, innerBlocks) => {
				const {content, ...restAttributes} = attributes

				return [
					restAttributes,
					[
						createBlock('core/paragraph', {content}),
						...innerBlocks
					],
				];
			},

			save: v1Save,
		}
	],
});
