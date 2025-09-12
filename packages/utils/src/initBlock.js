import { registerBlockType } from '@wordpress/blocks';

export const initBlock(block) => {
  if (!block) {
    return;
  }

  const { name, metadata, settings } = block;

  registerBlockType({ name, ...metadata }, settings);
}