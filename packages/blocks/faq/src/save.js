import {useInnerBlocksProps} from '@wordpress/block-editor';
import Schema from "./Schema";
const Save = (props) => {
	const {schema} = props.attributes;
	const innerBlocksProps = useInnerBlocksProps.save({className: 'faqs-wrapper'});

	return (
		<>
			<div {...innerBlocksProps}/>
			<Schema schema={schema}/>
		</>
	);
}

export default Save;
