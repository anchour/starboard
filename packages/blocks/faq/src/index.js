import './lib/store'
import metadata from './block.json';
import {registerBlockType} from '@wordpress/blocks';
import edit from './edit';
import save from './save';

registerBlockType(metadata, {
	edit,
	save,
});
