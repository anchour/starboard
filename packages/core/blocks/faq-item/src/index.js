import metadata from './block.json';
import {Panel, PanelBody, ToggleControl} from '@wordpress/components';
import {useDispatch, dispatch} from '@wordpress/data';
import {registerBlockType} from '@wordpress/blocks';
import {useBlockProps, BlockControls, InspectorControls, RichText} from '@wordpress/block-editor';
import {useEffect} from '@wordpress/element';
import {ToolbarGroup, ToolbarButton} from '@wordpress/components';

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

registerBlockType(
	metadata,
	{
		edit(props) {
			const {
				setAttributes,
				clientId,
				insertBlocksAfter,
				mergeBlocks,
				attributes: {blockId, title, content, isOpen}
			} = props;
			const {addOrUpdateFaqItem} = useDispatch('starboard/faq-store');

			if (!blockId || blockId.length === 0) {
				setAttributes({blockId: clientId})
			}

			const handleReplace = (blocks) => {
				// Replace the current block with the provided blocks
				if (insertBlocksAfter) {
					insertBlocksAfter(blocks);
				}

				// Remove the current block
				dispatch('core/block-editor').removeBlock(clientId);
			};

			// Update the Redux store whenever attributes change
			useEffect(() => {
				addOrUpdateFaqItem(blockId, {title, content});
			}, [title, content, blockId]);

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
						style={{fontSize: '2.25rem', paddingBottom: '1rem', lineHeight: 1.1,}}
						onChange={(title) => {
							setAttributes({title})
						}}
						onMerge={mergeBlocks}
					/>

					<RichText
						identifier={'content'}
						className='faq__item'
						onReplace={handleReplace}
						value={content}
						placeholder={'FAQ Content'}
						style={{fontSize: '1rem', paddingBottom: '1rem',}}
						onChange={(content) => {
							setAttributes({content})
						}}
						onMerge={mergeBlocks}
					/>
				</div>
			);
		},

		save(props) {
			const {blockId, title, content, isOpen} = props.attributes;

			return (
				<details {...useBlockProps.save({className: 'faq', open: isOpen, 'data-block-id': blockId})}>
					<RichText.Content tagName="summary" className={'faq__title'} value={title}/>

					<RichText.Content tagName={'div'} value={content} className={'faq__content'}/>
				</details>
			)
		}
	}
);
