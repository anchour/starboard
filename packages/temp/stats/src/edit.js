/**
 * Internal dependencies.
 */
import classNames from 'classnames';
import {blockClasses, statClasses} from './utils';
import {HeaderInput} from './HeaderInput';
import {HeadingFontSize} from '../block-utilities/headingFontSize';

/**
 * WordPress dependencies.
 */
const {
  i18n: {__},
  blockEditor: {useBlockProps, InnerBlocks, InspectorControls},
  components: {Panel, PanelBody},
} = wp;

const edit = (props) => {
  let {
    attributes: {breakpoints},
  } = props;

  const gridWrapClass = classNames(statClasses(breakpoints));

  const blockProps = useBlockProps({
    className: classNames(blockClasses(props.attributes)),
  });

  return (
    <div {...blockProps}>
      <InspectorControls>
        <Panel>
          <PanelBody title={__('Block Content')}>
            <HeaderInput {...props} />
          </PanelBody>
        </Panel>

        <Panel>
          <PanelBody title={__('Font Size')}>
            <HeadingFontSize {...props} />
          </PanelBody>
        </Panel>
      </InspectorControls>

      <HeaderInput {...props} />

      <div className={gridWrapClass}>
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
    </div>
  );
};

export default edit;
