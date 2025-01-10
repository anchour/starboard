/**
 * Internal dependencies.
 */
import classNames from 'classnames';
import {blockClasses} from '../utils';

/**
 * WordPress dependencies.
 */
const {
  blockEditor: {useBlockProps},
} = wp;

const save = (props) => {
  let {
    attributes: {statsHeading, statsDescription, headingFontSize, paragraphFontSize},
  } = props;

  const className = classNames(blockClasses(props.attributes));

  const blockProps = useBlockProps.save({
    className,
  });

  return (
    <article {...blockProps}>
      <h4
        className={`anchour-stats-item__stat has-${headingFontSize}-font-size`}>
        {statsHeading}

        {statsDescription && (
          <span className={`anchour-stats-item__description has-${paragraphFontSize}-font-size`}>
            {statsDescription}
          </span>
        )}
      </h4>
    </article>
  );
};

export default save;
