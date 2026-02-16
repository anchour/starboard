import { Panel, PanelBody } from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { useEffect } from "@wordpress/element";
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	RichText,
} from "@wordpress/block-editor";
import { __ } from "@wordpress/i18n";
import SchemaPreview from "./SchemaPreview.js";
import { generateSpacingVars } from "./utils.js";

const Edit = (props) => {
	const {
		attributes: { schema, spacing, title },
		setAttributes,
	} = props;

	const blockProps = useBlockProps({
		style: generateSpacingVars(spacing),
	});

	const innerBlocksProps = useInnerBlocksProps(
		{},
		{
			template: [
				["starboard/faq-item"],
				["starboard/faq-item"],
				["starboard/faq-item"],
			],
			allowedBlocks: ["starboard/faq-item"],
		},
	);

	// Subscribe to Redux store changes to ensure the parent gets updates
	const faqItems = useSelect(
		(select) => select("starboard/faq-store").getFaqItems(),
		[], // Dependencies are automatically tracked by WordPress
	);

	// Dynamically update schema when faqItems change
	useEffect(() => {
		if (faqItems) {
			const schemaItems = faqItems
				.map(({ title = "", content = "" }) => {
					return title.length > 0 && content.length > 0
						? {
								"@type": "Question",
								name: title,
								acceptedAnswer: {
									"@type": "Answer",
									text: content,
								},
						  }
						: null;
				})
				.filter(Boolean);

			const newSchema = {
				"@context": "https://schema.org",
				"@type": "FAQPage",
				mainEntity: schemaItems,
			};

			setAttributes({ schema: newSchema });
		}
	}, [faqItems]);

	return (
		<div {...blockProps}>
			<InspectorControls>
				<Panel>
					<PanelBody title={"Schema"} defaultOpen={false}>
						<SchemaPreview schema={schema} />
					</PanelBody>
				</Panel>
			</InspectorControls>

			<RichText
				tagName="h2"
				value={title}
				onChange={(title) => setAttributes({ title })}
				placeholder={__("FAQs")}
			/>

			<div {...innerBlocksProps} />
		</div>
	);
};

export default Edit;
