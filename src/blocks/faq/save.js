/**
 * Internal dependencies.
 */
import Schema from "./Schema.js";
import { generateSpacingVars } from "./utils.js";

/**
 * WordPress dependencies.
 */
import { useBlockProps, useInnerBlocksProps, RichText} from "@wordpress/block-editor";

const Save = (props) => {
	const { schema, spacing, title } = props.attributes;

	const blockProps = useBlockProps.save({
		className: "wp-block-anchour-faqs",
		style: generateSpacingVars(spacing),
	});

	const innerBlocksProps = useInnerBlocksProps.save();

	return (
		<>
			<div {...blockProps}>
				<RichText.Content
					className="has-heading-xl-alt-font-size"
					tagName="h2"
					value={title}
				/>
				<div className="faqs-wrapper">
					<Schema schema={schema} />
					<div {...innerBlocksProps} />
				</div>
			</div>
		</>
	);
};

export default Save;
