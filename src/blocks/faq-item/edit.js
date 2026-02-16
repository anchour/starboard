import {dispatch, useDispatch, useSelect} from '@wordpress/data';
import {useEffect} from '@wordpress/element';
import {Panel, PanelBody, ToggleControl, ToolbarButton, ToolbarGroup} from '@wordpress/components';
import {
	BlockControls,
	InspectorControls,
	RichText,
	store as blockEditorStore,
	useBlockProps,
	useInnerBlocksProps
} from '@wordpress/block-editor';

import {v4 as uuid} from 'uuid';
import {getTagNameFromBlockName} from "@starboard/utils";

const OpenToolbar = (props) => {
	const {setAttributes, attributes: {isOpen}} = props;

	return (
		<ToolbarGroup>
			<ToolbarButton
				icon={isOpen ? 'minus' : 'plus'}
				title="Open by default"
				onClick={() => {
					setAttributes({isOpen: !isOpen})
				}}
			/>
		</ToolbarGroup>
	)
};

const getInnerBlocksOriginalContent = (blocks) => {
	return blocks.map((block) => {
		let tagName = getTagNameFromBlockName(block);
		let innerContent = "";

		// If there are inner blocks, recursively fetch their content
		if (block.innerBlocks.length > 0) {
			innerContent = getInnerBlocksOriginalContent(block.innerBlocks).join("");
		}

		if (innerContent.length > 0) {
			// Return the inner content wrapped in the tag
			return `<${tagName}>${innerContent}</${tagName}>`;
		}

		// Check if the block has originalContent; if so, use that. Otherwise, check for attributes.content and use that instead.
		if (block.originalContent) {
			return block.originalContent;
		}

		// If the block doesn't have originalContent, use the content attribute
		let content = block.attributes.content;

		return `<${tagName}>${content}</${tagName}>`;
	});
};

export default function edit(props) {
	const {
		setAttributes,
		clientId,
		insertBlocksAfter,
		mergeBlocks,
		attributes: {blockId, title, isOpen}
	} = props;
	const {addOrUpdateFaqItem} = useDispatch('starboard/faq-store');

	/**
	 * Find all blocks with the same blockId attribute as the current block
	 */
	const matchingBlockIds = useSelect(
		(select) => {
			const {getBlock, getBlockParents, getSelectedBlockClientId} =
				select(blockEditorStore);

			const selectedBlockClientId = getSelectedBlockClientId();

			if (!selectedBlockClientId) {
				return null; // No block is selected
			}

			// Get the list of parent client IDs (ordered from closest to furthest ancestor)
			const parentClientIds = getBlockParents(selectedBlockClientId);

			// Get the direct parent (first in the array)
			const directParentClientId = parentClientIds.length > 0 ? parentClientIds.reverse()[0] : null;

			let parent = directParentClientId ? getBlock(directParentClientId) : null;

			if (!parent || parent.name !== 'starboard/faq') {
				return null;
			}

			// Check for any duplicate blockId attributes in the nested FAQ items
			return parent.innerBlocks.filter((block) => block.attributes.blockId === blockId);
		},
		[]
	);

	/**
	 * Set the blockId attribute if there are no matching blockIds, or if there are more than one
	 * matching blockIds.
	 *
	 * We need unique blockIds for each FAQ item to ensure that the schema is generated correctly.
	 */
	useEffect(() => {
		if (!matchingBlockIds) {
			return;
		}

		if (matchingBlockIds?.length > 1) {
			setAttributes({blockId: uuid()});

			return;
		}

		if (!blockId || blockId.length === 0) {
			setAttributes({blockId: clientId, anchor: clientId});
		}
	}, [matchingBlockIds]);

	const handleReplace = (blocks) => {
		// Replace the current block with the provided blocks
		if (insertBlocksAfter) {
			insertBlocksAfter(blocks);
		}

		// Remove the current block
		dispatch('core/block-editor').removeBlock(clientId);
	};

	/* Get the inner blocks of the current block */
	const innerBlocks = useSelect(
		(select) => {
			return select('core/block-editor').getBlocks(clientId);
		},
		[clientId]
	);

	/* Update the FAQ item in the store when the title or content changes */
	useEffect(() => {
		/*
		Combine the content of the inner blocks to get the raw content of the FAQ item.
		Since some blocks may have nested innerblocks, we need recursion.
		*/
		const rawContent = getInnerBlocksOriginalContent(innerBlocks).join('');

		addOrUpdateFaqItem(blockId, {title, content: rawContent});
	}, [title, innerBlocks, blockId, addOrUpdateFaqItem]);

	return (
		<div {...useBlockProps({style: {borderTop: '1px solid currentcolor'}})}>
			<InspectorControls>
				<Panel>
					<PanelBody title={'FAQ Item Settings'}>
						<ToggleControl
							checked={isOpen}
							label={'Open by default'}
							onChange={() => {
								setAttributes({isOpen: !isOpen})
							}}
						/>
					</PanelBody>
				</Panel>
			</InspectorControls>

			<BlockControls>
				<OpenToolbar {...props} />
			</BlockControls>

			<RichText
				identifier={'title'}
				tagName="div"
				placeholder="FAQ Title"
				onReplace={handleReplace}
				value={title}
				className={'faq__title'}
				style={{fontSize: '2.25rem', paddingBottom: '1rem', lineHeight: 1.1}}
				onChange={(title) => setAttributes({title})}
				onMerge={mergeBlocks}
			/>

			<div
				{
					...useInnerBlocksProps({className: 'faq__content'}, {
						template: [['core/paragraph']],
						allowedBlocks: ['core/paragraph', 'core/list', 'core/heading'],
					})
				}
			/>
		</div>
	);
}
