import {
	useBlockProps,
	InnerBlocks,
	RichText,
	BlockControls,
	InspectorControls
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	ColorPalette,
	ToolbarGroup,
	ToolbarDropdownMenu
} from '@wordpress/components';

export default function Edit(props) {
	const {
		attributes: { heading, headingLevel, backgroundColor, columns, maxItems },
		setAttributes
	} = props;

		// Create template for InnerBlocks with exactly maxItems numbered-feature-items
	const TEMPLATE = Array(maxItems).fill(['starboard/numbered-feature-item']);

	const blockProps = useBlockProps({
		className: `columns-${columns}`,
		style: {
			backgroundColor: backgroundColor
		}
	});

	// Heading level options for toolbar
	const headingLevelOptions = [1, 2, 3, 4, 5, 6].map(level => ({
		title: `H${level}`,
		onClick: () => setAttributes({ headingLevel: level })
	}));

	const HeadingTag = `h${headingLevel}`;

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarDropdownMenu
						icon="heading"
						label="Change heading level"
						controls={headingLevelOptions}
					/>
				</ToolbarGroup>
			</BlockControls>

			<InspectorControls>
				<PanelBody title="Layout Settings">
					<RangeControl
						label="Columns"
						value={columns}
						onChange={(value) => setAttributes({ columns: value })}
						min={2}
						max={3}
						help="Number of columns in the grid layout"
					/>
					<RangeControl
						label="Number of Items"
						value={maxItems}
						onChange={(value) => setAttributes({ maxItems: value })}
						min={2}
						max={9}
						step={1}
						help="Total number of feature items"
					/>
				</PanelBody>

				<PanelBody title="Style Settings">
					<p>Background Color</p>
					<ColorPalette
						value={backgroundColor}
						onChange={(color) => setAttributes({ backgroundColor: color })}
						clearable={false}
					/>
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="container">
					<RichText
						tagName={HeadingTag}
						className="section-heading"
						value={heading}
						onChange={(value) => setAttributes({ heading: value })}
						placeholder="Enter section heading..."
						allowedFormats={['core/bold', 'core/italic']}
					/>

					<div className="features-grid">
						<InnerBlocks
							allowedBlocks={['starboard/numbered-feature-item']}
							template={TEMPLATE}
							templateLock="all"
							orientation="horizontal"
						/>
					</div>
				</div>
			</section>
		</>
	);
}
