import './lib/store.js'
import metadata from './block.json';
import {createBlock, registerBlockType} from '@wordpress/blocks';
import edit from './edit.js';
import save from './save.js';

registerBlockType(metadata, {
	edit,
	save,
	transforms: {
		from: [
			{
				type: 'block',
				blocks: ['anchour/accordion'],
				transform: (attributes, innerBlocks) => {
					// Convert innerBlocks from anchour/each-accordion to starboard/faq-item
					const newInnerBlocks = innerBlocks.map((block) => {
						const newAttributes = {
							title: block.attributes.accordionLabel,
						}

						if (block.name === 'anchour/each-accordion') {
							return createBlock('starboard/faq-item', newAttributes, block.innerBlocks);
						}

						return block;
					});

					return createBlock('starboard/faq', attributes, newInnerBlocks);
				}
			}
		]
	}
});
