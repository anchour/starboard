/**
 * WordPress dependencies.
 */
const {
  i18n: {__},
  blockEditor: {useSetting},
  components: {SelectControl},
} = wp;

export function ParagraphFontSize({
  attributes: {paragraphFontSize},
  setAttributes,
}) {
  const fontSizes = useSetting('typography.fontSizes');

  const options = fontSizes?.map((size) => ({
    label: size?.name || '',
    value: size?.slug || '',
  }));

  return (
    <SelectControl
      label={__('Paragraph Font Size')}
      value={paragraphFontSize}
      options={options}
      onChange={(paragraphFontSize) => {
        setAttributes({paragraphFontSize: paragraphFontSize});
      }}
    />
  );
}
