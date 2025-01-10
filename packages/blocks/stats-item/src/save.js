/**
 * Internal dependencies.
 */
import {blockClasses} from './utils';

/**
 * WordPress dependencies.
 */
const {
  blockEditor: {useBlockProps},
} = wp;

const save = (props) => {
  let {
    attributes: {
      statsHeading,
      statsDescription,
      statsFootnote,
      headingFontSize,
      paragraphFontSize,
    },
  } = props;

  const className = blockClasses()

  const blockProps = useBlockProps.save({
    className,
  });

  return (
    <article {...blockProps}>
      <h4
        className={`stats-item__stat has-${headingFontSize}-font-size`}>
        {statsHeading}

        {statsDescription && (
          <span
            className={`stats-item__description has-${paragraphFontSize}-font-size`}>
            {statsDescription}
          </span>
        )}
      </h4>
      {statsFootnote && <span className="stats-item__footnote"> {statsFootnote} </span>}
    </article>
  );
};

export default save;
