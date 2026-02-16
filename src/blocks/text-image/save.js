/**
 * External dependencies.
 */
import classNames from 'classnames';

/**
 * Internal dependencies.
 */
import {generateSpacingVars, generateGridVars} from './utils.js';

/**
 * WordPress dependencies.
 */
const {useBlockProps, InnerBlocks} = wp.blockEditor;

const save = (props) => {
  const {
    attributes: {verticalAlignment, selectedVariationName, mobileReverse, copySide, spacing},
  } = props;

  const className = classNames(
    selectedVariationName,
    mobileReverse,
    copySide,
    `items-aligned-${verticalAlignment}`
  );

  const blockProps = useBlockProps.save({
    className,
    style: {
      ...generateSpacingVars(spacing),
      ...generateGridVars(selectedVariationName, copySide),
    },
  });

  return (
    <div {...blockProps}>
      <div className={`block-row items-aligned-${verticalAlignment}`}>
        <InnerBlocks.Content />
      </div>
    </div>
  );
};

export default save;
