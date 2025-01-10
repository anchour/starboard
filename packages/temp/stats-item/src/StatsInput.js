const {
  blockEditor: {RichText},
  i18n: {__},
} = wp;

export function StatsInput(props) {
  const {
    attributes: {
      statsHeading,
      statsDescription,
      statsFootnote,
      headingFontSize,
      paragraphFontSize,
    },
    setAttributes,
  } = props

  return (
    <>
      <RichText
        value={statsHeading}
        help={__('Ex. 50%')}
        onChange={(statsHeading) => setAttributes({statsHeading})}
        tagName={'h4'}
        className={`anchour-stats-item__stat wp-block-heading has-${headingFontSize}-font-size my-0`}
        placeholder={__('Stat')}
      />

      <RichText
        value={statsDescription}
        onChange={(statsDescription) => setAttributes({statsDescription})}
        tagName={'p'}
        className={`anchour-stats-item__description wp-block-paragraph has-${paragraphFontSize}-font-size`}
        placeholder={__('Description')}
      />

      <RichText
        value={statsFootnote}
        onChange={(statsFootnote) => setAttributes({statsFootnote})}
        className={'stats-footnote'}
        tagName={'span'}
        placeholder={__('Footnote')}
      />
    </>
  );
}
