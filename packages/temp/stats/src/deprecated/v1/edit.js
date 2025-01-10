/**
 * Internal dependencies.
 */
import classNames from 'classnames';
import {blockClasses, statClasses} from '../../utils';

/**
 * WordPress dependencies.
 */
const {
  blockEditor: {useBlockProps, InnerBlocks},
} = wp;

const edit = (props) => {
  let {
    attributes: {breakpoints},
  } = props;

  const blockProps = useBlockProps({
    className: classNames(blockClasses(), statClasses(breakpoints)),
  });

  return (
    <div {...blockProps}>
      <InnerBlocks
        renderAppender={InnerBlocks.ButtonBlockAppender}
        allowedBlocks={['anchour/stats-item']}
        template={[
          ['anchour/stats-item'],
          ['anchour/stats-item'],
          ['anchour/stats-item'],
          ['anchour/stats-item'],
        ]}
        orientation="vertical"
      />
    </div>
  );
};

export default edit;
