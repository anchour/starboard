export default function Schema(props) {
	const {schema} = props;

	return schema && (<script type="application/ld+json">{JSON.stringify(schema)}</script>);
}
