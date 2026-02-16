export default function Schema(props) {
	const {schema} = props;

	let schemaContents = JSON.stringify(schema);

	if (schemaContents === '{}') {
		return null;
	}

	// Replace any escaped HTML entities with their unescaped counterparts
	schemaContents = schemaContents
		.replace(/&quot;/g, '"')
		.replace(/&lt;/g, '<')
		.replace(/&gt;/g, '>')
		.replace(/&amp;/g, '&');

	return <script type="application/ld+json" dangerouslySetInnerHTML={{__html: schemaContents}} />
}
