/**
 * Internal dependencies.
 */
import classNames from 'classnames';
import {blockClasses, statClasses} from '../../utils';

/**
 * WordPress dependencies.
 */
const {InnerBlocks, useBlockProps} = wp.blockEditor;

const save = (props) => {
  let {
    attributes: {breakpoints},
  } = props;

  const className = classNames(blockClasses(), statClasses(breakpoints));

  const blockProps = useBlockProps.save({
    className,
  });

  return (
    <div {...blockProps}>
      <InnerBlocks.Content />
    </div>
  );
};

export default save;
