import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import metadata from './block.json';
import './style.css';

registerBlockType(metadata, {
	edit(props) {
		const {
			setAttributes,
			clientId,
			attributes: { heading, description, showBorder }
		} = props;

		// Get the block's index within its parent to determine the number
		const blockIndex = useSelect(
			(select) => {
				const { getBlockRootClientId, getBlockIndex } = select('core/block-editor');
				const rootClientId = getBlockRootClientId(clientId);
				return getBlockIndex(clientId, rootClientId) + 1;
			},
			[clientId]
		);

		const blockProps = useBlockProps({
			className: `numbered-feature-item ${showBorder ? 'has-border' : ''}`
		});

		return (
			<div {...blockProps}>
				<div className="feature-number" aria-hidden="true">
					{blockIndex}
				</div>
				<div className="feature-content">
					<RichText
						tagName="h3"
						className="feature-heading"
						value={heading}
						onChange={(value) => setAttributes({ heading: value })}
						placeholder="Feature heading..."
						allowedFormats={['core/bold']}
					/>
					<RichText
						tagName="p"
						className="feature-description"
						value={description}
						onChange={(value) => setAttributes({ description: value })}
						placeholder="Feature description..."
						allowedFormats={['core/bold', 'core/italic', 'core/link']}
					/>
				</div>
			</div>
		);
	},

	save(props) {
		const { heading, description, showBorder } = props.attributes;
		const blockProps = useBlockProps.save({
			className: `numbered-feature-item ${showBorder ? 'has-border' : ''}`
		});

		// Number will be injected via CSS counter or JS on frontend
		return (
			<div {...blockProps}>
				<div className="feature-number" aria-hidden="true"></div>
				<div className="feature-content">
					<RichText.Content
						tagName="h3"
						className="feature-heading"
						value={heading}
					/>
					<RichText.Content
						tagName="p"
						className="feature-description"
						value={description}
					/>
				</div>
			</div>
		);
	}
});