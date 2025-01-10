/**
 * WordPress dependencies.
 */
const {
  i18n: {__},
  blockEditor: {useSetting},
  components: {SelectControl},
} = wp;

export function HeadingFontSize({
  attributes: {headingFontSize},
  setAttributes,
}) {
  const fontSizes = useSetting('blocks.core/heading.typography.fontSizes');

  const options = fontSizes?.theme?.map((size) => ({
    label: size?.name || '',
    value: size?.slug || '',
  }));

  return (
    <SelectControl
      label={__('Heading Font Size')}
      value={headingFontSize}
      options={options}
      onChange={(headingFontSize) => {
        setAttributes({headingFontSize: headingFontSize});
      }}
    />
  );
}
