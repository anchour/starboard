/**
 * Builds inline style vars from spacing attribute.
 *
 * @param {object} spacing
 * @returns {object} CSS style object
 */
export const generateSpacingVars = (spacing = {}) => {
	const vars = {};

	Object.entries(spacing).forEach(([key, val]) => {
		if (!val?.active) return;

		const prefix = key === "Default" ? "" : `${key.toLowerCase()}-`;
		const { paddingTop, paddingBottom } = val.settings || {};

		if (paddingTop != null) {
			vars[`--${prefix}padding-top`] = `${paddingTop * 0.25}rem`;
		}

		if (paddingBottom != null) {
			vars[`--${prefix}padding-bottom`] = `${paddingBottom * 0.25}rem`;
		}
	});

	return vars;
};
