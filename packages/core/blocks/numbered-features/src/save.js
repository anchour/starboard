import { useBlockProps, InnerBlocks, RichText } from '@wordpress/block-editor';

export default function Save(props) {
	const {
		attributes: { heading, headingLevel, backgroundColor, columns }
	} = props;

	const blockProps = useBlockProps.save({
		className: `columns-${columns}`,
		style: {
			backgroundColor: backgroundColor
		}
	});

	const HeadingTag = `h${headingLevel}`;

	return (
		<section {...blockProps}>
			<div className="container">
				<RichText.Content
					tagName={HeadingTag}
					className="section-heading"
					value={heading}
				/>
				
				<div className="features-grid">
					<InnerBlocks.Content />
				</div>
			</div>
		</section>
	);
}