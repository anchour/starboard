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
const {
  blockEditor: {
    useBlockProps,
    InnerBlocks,
    BlockControls,
    BlockVerticalAlignmentToolbar,
    __experimentalBlockVariationPicker,
    store: blockEditorStore,
  },
  data: {useSelect, useDispatch},
  blocks: {createBlocksFromInnerBlocksTemplate, store: blocksStore},
} = wp;

const ALLOWED_BLOCKS = ['starboard/text-image-column', 'core/image'];

function Placeholder({clientId, name, setAttributes}) {
  const {blockType, defaultVariation, variations} = useSelect(
    (select) => {
      const {getBlockVariations, getBlockType, getDefaultBlockVariation} = select(blocksStore);
      return {
        blockType: getBlockType(name),
        defaultVariation: getDefaultBlockVariation(name, 'block'),
        variations: getBlockVariations(name, 'block'),
      };
    },
    [name]
  );
  const {replaceInnerBlocks} = useDispatch(blockEditorStore);
  const blockProps = useBlockProps();

  return (
    <div {...blockProps}>
      <__experimentalBlockVariationPicker
        icon={blockType?.icon?.src}
        label={blockType?.title}
        variations={variations}
        onSelect={(nextVariation = defaultVariation) => {
          setAttributes({
            selectedVariationTitle: nextVariation.title,
            selectedVariationName: nextVariation.name,
          });

          if (nextVariation.attributes) {
            setAttributes(nextVariation.attributes);
          }

          if (nextVariation.innerBlocks) {
            replaceInnerBlocks(
              clientId,
              createBlocksFromInnerBlocksTemplate(nextVariation.innerBlocks),
              true
            );
          }
        }}
        allowSkip
      />
    </div>
  );
}

function ColumnsEditContainer({attributes, setAttributes}) {
  const {verticalAlignment, mobileReverse, copySide, selectedVariationName, spacing} = attributes;

  const blockProps = useBlockProps({
    className: classNames(
      mobileReverse,
      copySide,
      `items-aligned-${verticalAlignment}`,
      selectedVariationName
    ),
    style: {
      ...generateSpacingVars(spacing),
      ...generateGridVars(selectedVariationName, copySide),
    },
  });

  return (
    <>
      <BlockControls>
        <BlockVerticalAlignmentToolbar
          onChange={(value) => setAttributes({verticalAlignment: value})}
          value={verticalAlignment}
        />
      </BlockControls>

      <div {...blockProps}>
        <div className="block-row">
          <InnerBlocks
            allowedBlocks={ALLOWED_BLOCKS}
            orientation="horizontal"
            renderAppender={false}
          />
        </div>
      </div>
    </>
  );
}

const edit = (props) => {
  const {clientId} = props;

  const hasInnerBlocks = useSelect(
    (select) => select(blockEditorStore).getBlocks(clientId).length > 0,
    [clientId]
  );

  const Component = hasInnerBlocks ? ColumnsEditContainer : Placeholder;

  return <Component {...props} />;
};

export default edit;
