import {Breakpoint, createBreakpointFromScreen} from '@starboard/components';
import {
  defaultSpacingValues,
  screenKeys,
  breakpointLabels,
  getSpacingDescription,
} from '@starboard/utils';

const {
  i18n: {__},
  blockEditor: {InspectorControls, BlockControls},
  components: {
    PanelBody,
    Button,
    ButtonGroup,
    BaseControl,
    SelectControl,
    ToolbarGroup,
    ToolbarDropdownMenu,
    RangeControl,
  },
  compose: {createHigherOrderComponent},
  element: {useCallback},
} = wp;

/**
 * Built-in layout variations
 */
const BUILT_IN_VARIATIONS = [
  {
    name: 'text-image__narrow-content',
    title: 'Narrow Content (4/5)',
  },
  {
    name: 'text-image__wide-image',
    title: 'Wide Image (4/7)',
  },
  {
    name: 'text-image__balanced',
    title: 'Balanced (6/5)',
  },
];

/**
 * Get all available layout variations (built-in + custom)
 * Called at render time to ensure starboardConfig is available
 */
const getVariations = () => {
  const customVariants = window.starboardConfig?.customVariants?.variations || [];
  const custom = customVariants.map((v) => ({
    name: v.name,
    title: v.title,
  }));
  return [...BUILT_IN_VARIATIONS, ...custom];
};

const allowlist = ['starboard/text-image'];

/**
 * Layout icon - Content on left, image on right
 */
const ContentLeftIcon = () => (
  <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect x="11.5" y="0.5" width="8" height="15" rx="1" stroke="currentColor" fill="none" />
    <rect x="0" y="3" width="9" height="2" fill="currentColor" />
    <rect x="0" y="7" width="9" height="2" fill="currentColor" />
    <rect x="0" y="11" width="6" height="2" fill="currentColor" />
  </svg>
);

/**
 * Layout icon - Content on right, image on left
 */
const ContentRightIcon = () => (
  <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect x="0.5" y="0.5" width="8" height="15" rx="1" stroke="currentColor" fill="none" />
    <rect x="11" y="3" width="9" height="2" fill="currentColor" />
    <rect x="11" y="7" width="9" height="2" fill="currentColor" />
    <rect x="11" y="11" width="6" height="2" fill="currentColor" />
  </svg>
);

/**
 * Mobile icon - Image on top
 */
const ImageTopIcon = () => (
  <svg width="14" height="20" viewBox="0 0 14 20" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect x="0.5" y="0.5" width="13" height="8" rx="1" stroke="currentColor" fill="none" />
    <rect x="2" y="12" width="10" height="1.5" fill="currentColor" />
    <rect x="2" y="15" width="10" height="1.5" fill="currentColor" />
    <rect x="2" y="18" width="6" height="1.5" fill="currentColor" />
  </svg>
);

/**
 * Mobile icon - Image on bottom
 */
const ImageBottomIcon = () => (
  <svg width="14" height="20" viewBox="0 0 14 20" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect x="0.5" y="11.5" width="13" height="8" rx="1" stroke="currentColor" fill="none" />
    <rect x="2" y="0" width="10" height="1.5" fill="currentColor" />
    <rect x="2" y="3" width="10" height="1.5" fill="currentColor" />
    <rect x="2" y="6" width="6" height="1.5" fill="currentColor" />
  </svg>
);

export const breakpoints = createHigherOrderComponent(
  (BlockEdit) => (props) => {
    const {
      name,
      attributes: {copySide, mobileReverse, selectedVariationName, selectedVariationTitle, spacing},
      setAttributes,
    } = props;

    if (!allowlist.includes(name)) {
      return <BlockEdit {...props} />;
    }

    const handleVariationChange = useCallback(
      (newVariationName) => {
        const variations = getVariations();
        const variation = variations.find((v) => v.name === newVariationName);
        if (!variation) return;

        setAttributes({
          selectedVariationName: variation.name,
          selectedVariationTitle: variation.title,
        });
      },
      [setAttributes]
    );

    return (
      <>
        <BlockEdit {...props} />

        <BlockControls>
          <ToolbarGroup>
            <ToolbarDropdownMenu
              icon={copySide === 'split-copy-right' ? <ContentRightIcon /> : <ContentLeftIcon />}
              label={__('Content Side')}
              controls={[
                {
                  icon: <ContentLeftIcon />,
                  title: __('Content Left'),
                  isActive: copySide === 'split-copy-left',
                  onClick: () => setAttributes({copySide: 'split-copy-left'}),
                },
                {
                  icon: <ContentRightIcon />,
                  title: __('Content Right'),
                  isActive: copySide === 'split-copy-right',
                  onClick: () => setAttributes({copySide: 'split-copy-right'}),
                },
              ]}
            />
          </ToolbarGroup>

          <ToolbarGroup>
            <ToolbarDropdownMenu
              icon={mobileReverse === 'image-btm-mobile' ? <ImageBottomIcon /> : <ImageTopIcon />}
              label={__('Mobile Order')}
              controls={[
                {
                  icon: <ImageTopIcon />,
                  title: __('Image Top'),
                  isActive: mobileReverse === 'image-top-mobile',
                  onClick: () => setAttributes({mobileReverse: 'image-top-mobile'}),
                },
                {
                  icon: <ImageBottomIcon />,
                  title: __('Image Bottom'),
                  isActive: mobileReverse === 'image-btm-mobile',
                  onClick: () => setAttributes({mobileReverse: 'image-btm-mobile'}),
                },
              ]}
            />
          </ToolbarGroup>
        </BlockControls>

        <InspectorControls>
          <PanelBody
            title={selectedVariationTitle ? `Layout: ${selectedVariationTitle}` : __('Layout Settings')}
            initialOpen={true}
          >
            <SelectControl
              label={__('Layout Style')}
              value={selectedVariationName}
              options={getVariations().map((v) => ({
                label: v.title,
                value: v.name,
              }))}
              onChange={handleVariationChange}
              help={__('Select the column layout style.')}
            />

            <BaseControl
              label={__('Content Side')}
              help={__('Which side the text content appears on desktop.')}
              style={{marginTop: '16px'}}
            >
              <ButtonGroup style={{display: 'flex', marginTop: '8px'}}>
                <Button
                  icon={<ContentLeftIcon />}
                  label={__('Content Left')}
                  isPressed={copySide === 'split-copy-left'}
                  onClick={() => setAttributes({copySide: 'split-copy-left'})}
                  style={{flex: 1, justifyContent: 'center'}}
                />
                <Button
                  icon={<ContentRightIcon />}
                  label={__('Content Right')}
                  isPressed={copySide === 'split-copy-right'}
                  onClick={() => setAttributes({copySide: 'split-copy-right'})}
                  style={{flex: 1, justifyContent: 'center'}}
                />
              </ButtonGroup>
            </BaseControl>

            <div style={{marginTop: '24px', paddingTop: '16px', borderTop: '1px solid #e0e0e0'}}>
              <p
                style={{
                  fontSize: '11px',
                  fontWeight: 500,
                  textTransform: 'uppercase',
                  letterSpacing: '0.02em',
                  marginBottom: '16px',
                  color: '#757575',
                }}
              >
                {__('Mobile Settings')}
              </p>

              <BaseControl
                label={__('Mobile Order')}
                help={__('Whether the image appears above or below the content on mobile.')}
              >
                <ButtonGroup style={{display: 'flex', marginTop: '8px'}}>
                  <Button
                    icon={<ImageTopIcon />}
                    label={__('Image Top')}
                    isPressed={mobileReverse === 'image-top-mobile'}
                    onClick={() => setAttributes({mobileReverse: 'image-top-mobile'})}
                    style={{flex: 1, justifyContent: 'center'}}
                  />
                  <Button
                    icon={<ImageBottomIcon />}
                    label={__('Image Bottom')}
                    isPressed={mobileReverse === 'image-btm-mobile'}
                    onClick={() => setAttributes({mobileReverse: 'image-btm-mobile'})}
                    style={{flex: 1, justifyContent: 'center'}}
                  />
                </ButtonGroup>
              </BaseControl>
            </div>
          </PanelBody>
        </InspectorControls>

        <InspectorControls group="styles">
          <PanelBody title={__('Spacing')}>
            <p style={{marginBottom: '1rem', fontSize: '12px'}}>
              {__('Adjust top/bottom padding for each breakpoint.')}
            </p>

            {screenKeys.map((screenKey, idx) => {
              const current = spacing?.[screenKey] || {};
              const active = current.active ?? false;

              const defaultValues = defaultSpacingValues[screenKey] || {
                paddingTop: 16,
                paddingBottom: 16,
              };

              const settings = current.settings ?? defaultValues;

              const updateSpacing = (newData) => {
                const next = {...spacing};
                next[screenKey] = {
                  ...spacing?.[screenKey],
                  active: true,
                  settings: {
                    ...(spacing?.[screenKey]?.settings || {}),
                    ...newData,
                  },
                };
                setAttributes({spacing: next});
              };

              const breakpoint = createBreakpointFromScreen(
                screenKey,
                breakpointLabels[screenKey],
                active,
                {},
                settings,
              );
              breakpoint.name = screenKey === 'Default' ? 'Mobile' : screenKey;

              return (
                <Breakpoint
                  key={idx}
                  breakpoint={breakpoint}
                  onToggleChange={() =>
                    setAttributes({
                      spacing: {
                        ...spacing,
                        [screenKey]: {
                          ...current,
                          active: !active,
                        },
                      },
                    })
                  }>
                  <RangeControl
                    label={__('Top Padding')}
                    min={0}
                    max={50}
                    step={2}
                    value={settings.paddingTop}
                    help={getSpacingDescription(settings.paddingTop)}
                    onChange={(val) =>
                      updateSpacing({
                        ...settings,
                        paddingTop: val,
                      })
                    }
                  />
                  <RangeControl
                    label={__('Bottom Padding')}
                    min={0}
                    max={50}
                    step={2}
                    value={settings.paddingBottom}
                    help={getSpacingDescription(settings.paddingBottom)}
                    onChange={(val) =>
                      updateSpacing({
                        ...settings,
                        paddingBottom: val,
                      })
                    }
                  />
                </Breakpoint>
              );
            })}
          </PanelBody>
        </InspectorControls>
      </>
    );
  }
);
