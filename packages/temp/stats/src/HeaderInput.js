const {
  blockEditor: {RichText},
  i18n: {__},
} = wp;

export function HeaderInput(props) {
  const {
    attributes: {
      statsHeader,
      headingFontSize,
    },
    setAttributes,
  } = props;

  return (
    <>
      <RichText
        value={statsHeader}
        onChange={(statsHeader) => setAttributes({statsHeader})}
        className={`wp-block-heading has-${headingFontSize}-font-size`}
        tagName={'h3'}
        placeholder={__('Stats Heading')}
      />
    </>
  );
}
