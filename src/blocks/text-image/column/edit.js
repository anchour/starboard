/**
 * External dependencies.
 */
import classNames from 'classnames';

/**
 * WordPress dependencies.
 */
const {
  blockEditor: {InnerBlocks, useBlockProps, store: blockEditorStore},
  data: {useSelect},
} = wp;

/**
 * Allowed blocks for the copy side column
 */
const ALLOWED_COPY_BLOCKS = [
  'core/heading',
  'core/list',
  'core/paragraph',
  'core/buttons',
  'core/spacer',
  'core/separator',
  'core/code',
  'core/shortcode',
  'core/file',
];

/**
 * Allowed blocks for the image side column
 */
const ALLOWED_IMAGE_BLOCKS = ['core/image', 'core/video', 'core/cover'];

const edit = (props) => {
  const {
    attributes: {columnType},
    clientId,
  } = props;

  const {hasChildBlocks} = useSelect(
    (select) => {
      const {getBlockOrder} = select(blockEditorStore);
      return {
        hasChildBlocks: getBlockOrder(clientId).length > 0,
      };
    },
    [clientId]
  );

  const className = classNames({
    'text-image__copy-side': columnType === 'copy',
    'text-image__img-side': columnType === 'image',
  });

  const blockProps = useBlockProps({
    className,
  });

  const allowedBlocks = columnType === 'copy' ? ALLOWED_COPY_BLOCKS : ALLOWED_IMAGE_BLOCKS;

  return (
    <div {...blockProps}>
      <InnerBlocks
        allowedBlocks={allowedBlocks}
        renderAppender={hasChildBlocks ? undefined : InnerBlocks.ButtonBlockAppender}
      />
    </div>
  );
};

export default edit;
