/**
 * Internal dependencies.
 */
import classNames from 'classnames';
import {blockClasses, statClasses} from './utils';

/**
 * WordPress dependencies.
 */
const {
  blockEditor: {useBlockProps, InnerBlocks},
} = wp;

const save = (props) => {
  let {
    attributes: {headingFontSize, statsHeader, breakpoints},
  } = props;

  const className = classNames(blockClasses(), statClasses(breakpoints));

  const blockProps = useBlockProps.save({
    className,
  });

  return (
    <>
      {blockProps.className.includes('is-style-alternate') && statsHeader && (
        <div className="block md:hidden remove-bottom-spacing w-10/12 offset-1/12">
          <h3 className={`has-${headingFontSize}-font-size`}>{statsHeader}</h3>
        </div>
      )}

      <div {...blockProps}>
        {statsHeader && (
          <>
            {blockProps.className.includes('is-style-alternate') ? (
              <div className="w-10/12 offset-1/12 hidden md:block">
                <h3 className={`has-${headingFontSize}-font-size`}>
                  {statsHeader}
                </h3>
              </div>
            ) : (
              <div className="w-full">
                <h3 className={`has-${headingFontSize}-font-size`}>
                  {statsHeader}
                </h3>
              </div>
            )}
          </>
        )}

        <InnerBlocks.Content/>
      </div>
    </>
  );
};

export default save;
