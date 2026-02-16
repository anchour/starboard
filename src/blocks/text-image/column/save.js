/**
 * External dependencies.
 */
import classNames from 'classnames';

/**
 * WordPress dependencies.
 */
const {
  blockEditor: {useBlockProps, useInnerBlocksProps},
} = wp;

const save = ({attributes}) => {
  const {columnType} = attributes;

  const className = classNames({
    'text-image__copy-side': columnType === 'copy',
    'text-image__img-side': columnType === 'image',
  });

  const blockProps = useBlockProps.save({
    className,
  });

  const innerBlocksProps = useInnerBlocksProps.save(blockProps);

  // Wrap copy side content in inner-wrapper for styling
  if (columnType === 'copy') {
    const {children} = innerBlocksProps;
    return (
      <div {...blockProps}>
        <div className="column--inner-wrapper">{children}</div>
      </div>
    );
  }

  return <div {...innerBlocksProps} />;
};

export default save;
