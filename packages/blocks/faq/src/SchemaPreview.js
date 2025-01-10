export default function SchemaPreview(props) {
	const {schema} = props;

	if (!schema) {
		return null;
	}

	return (
		<div className={'schema-preview'}>
			<h3>Schema Preview</h3>
			<div style={{
				overflow: 'auto',
				border: '1px solid',
				borderRadius: '0.5rem 0.5rem 0 0',
				paddingLeft: '1rem',
				paddingRight: '1rem',
			}}>
				<pre style={{
					width: '100%',
					marginBottom: '1rem',
					paddingTop: '1rem',
					paddingBottom: '1rem',
					marginTop: 0
				}}>
					{JSON.stringify(schema, null, 2)}
				</pre>
			</div>
		</div>
	);
}
