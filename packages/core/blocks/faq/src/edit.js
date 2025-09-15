import {Panel, PanelBody} from '@wordpress/components';
import {useSelect} from '@wordpress/data';
import {useEffect} from '@wordpress/element';
import {useBlockProps, useInnerBlocksProps, InspectorControls} from '@wordpress/block-editor';
import SchemaPreview from "./SchemaPreview";

const Edit = (props) => {
	const {attributes: {schema}, setAttributes} = props;
	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps({}, {
		template: [
			['starboard/faq-item'],
			['starboard/faq-item'],
			['starboard/faq-item']
		],
		allowedBlocks: ['starboard/faq-item'],
	});

// Get state via useSelect
	const faqItems = useSelect((select) => select('starboard/faq-store').getFaqItems());

	/*
	 Get the updated schema object when faqItems from the redux store above are updated.
	 Filter out any items that do not have a title or content.
	 */
	useEffect(() => {
		if (faqItems) {
			const schemaItems = faqItems.map(({title = '', content = ''}) =>
				(title.length > 0 && content.length > 0)
					? ({
						'@type': 'Question',
						name: title,
						acceptedAnswer: {
							'@type': 'Answer',
							text: content,
						},
					})
					: null
			).filter(Boolean);

			const newSchema = {
				"@context": "https://schema.org",
				"@type": "FAQPage",
				"mainEntity": schemaItems,
			}

			setAttributes({schema: newSchema});
		}
	}, [faqItems]);

	return (
		<div {...blockProps}>
			<InspectorControls>
				<Panel>
					<PanelBody title={"Schema"} defaultOpen={false}>
						<SchemaPreview schema={schema}/>
					</PanelBody>
				</Panel>
			</InspectorControls>

			<div {...innerBlocksProps}/>
		</div>
	);
}

export default Edit
