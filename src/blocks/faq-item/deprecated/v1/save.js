import {useBlockProps, RichText} from '@wordpress/block-editor';

export default function save(props) {
	const {blockId, title, content, isOpen} = props.attributes;

	return (
		<details {...useBlockProps.save({className: 'faq', open: isOpen, 'data-block-id': blockId})}>
			<RichText.Content tagName="summary" className={'faq__title'} value={title}/>

			<RichText.Content tagName={'div'} value={content} className={'faq__content'}/>
		</details>
	)
}
