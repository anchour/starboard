/**
 * Internal dependencies.
 */
import classNames from 'classnames';
import {blockClasses} from './utils';
import {StatsInput} from './StatsInput';
import {HeadingFontSize} from '../block-utilities/headingFontSize'
import {ParagraphFontSize} from '../block-utilities/paragraphFontSize'

/**
 * WordPress dependencies.
 */
const {
  i18n: {__},
  blockEditor: {useBlockProps, InspectorControls},
  components: {Panel, PanelBody},
} = wp;


const edit = (props) => {
  const blockProps = useBlockProps({
    className: classNames(blockClasses(props.attributes)),
  });

  return (
    <div {...blockProps}>
      <InspectorControls>
        <Panel>
          <PanelBody title={__('Block Content')}>
            <StatsInput {...props} />
          </PanelBody>
        </Panel>

        <Panel> 
          <PanelBody title={__('Font Sizes')}>
            <HeadingFontSize {...props}/>
            <ParagraphFontSize {...props}/>
          </PanelBody>
        </Panel>
      </InspectorControls>

      <StatsInput {...props} />
    </div>
  );
};

export default edit;
