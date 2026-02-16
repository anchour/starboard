import {useBlockProps, useInnerBlocksProps, RichText} from '@wordpress/block-editor';

export default function save(props) {
	const {blockId, title, content, isOpen} = props.attributes;

	return (
		<details {...useBlockProps.save({className: 'faq', open: isOpen, 'data-block-id': blockId})}>
			<summary className={'faq__title'}>
				<RichText.Content tagName={null} value={title}/>

				<div className={'faq__icon'} role={'presentation'} aria-hidden></div>
			</summary>

			<div {...useInnerBlocksProps.save({className: 'faq__content'})}/>
		</details>
	)
}
