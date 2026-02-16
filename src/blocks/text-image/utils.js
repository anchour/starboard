/**
 * Built-in layout configurations for each variant.
 * Defines copy and image column positions for a 12-column grid.
 */
const BUILT_IN_LAYOUTS = {
  // Narrow Content (4/5)
  'text-image__narrow-content': {
    lg: {
      copyLeft: {copy: {start: 1, span: 5}, img: {start: 7, span: 6}},
      copyRight: {copy: {start: 8, span: 5}, img: {start: 1, span: 6}},
    },
    xl: {
      copyLeft: {copy: {start: 2, span: 4}, img: {start: 7, span: 5}},
      copyRight: {copy: {start: 8, span: 4}, img: {start: 2, span: 5}},
    },
  },
  // Wide Image (4/7)
  'text-image__wide-image': {
    lg: {
      copyLeft: {copy: {start: 1, span: 5}, img: {start: 7, span: 6}},
      copyRight: {copy: {start: 8, span: 5}, img: {start: 1, span: 6}},
    },
    xl: {
      copyLeft: {copy: {start: 1, span: 4}, img: {start: 6, span: 7}},
      copyRight: {copy: {start: 9, span: 4}, img: {start: 1, span: 7}},
    },
  },
  // Balanced (6/5)
  'text-image__balanced': {
    lg: {
      copyLeft: {copy: {start: 1, span: 6}, img: {start: 7, span: 6}},
      copyRight: {copy: {start: 7, span: 6}, img: {start: 1, span: 6}},
    },
    xl: {
      copyLeft: {copy: {start: 1, span: 6}, img: {start: 8, span: 5}},
      copyRight: {copy: {start: 7, span: 6}, img: {start: 1, span: 5}},
    },
  },
};

/**
 * Get custom layouts from starboardConfig
 */
const getCustomLayouts = () => {
  return window.starboardConfig?.customVariants?.layouts || {};
};

/**
 * Layout configurations for each variant - SINGLE SOURCE OF TRUTH for grid positioning.
 * Merges built-in layouts with custom layouts from settings.
 * These values are output as CSS variables on the parent block.
 */
export const LAYOUTS = {
  ...BUILT_IN_LAYOUTS,
  ...getCustomLayouts(),
};

// Default layout when no variant selected
const DEFAULT_LAYOUT = {
  lg: {
    copyLeft: {copy: {start: 1, span: 6}, img: {start: 7, span: 6}},
    copyRight: {copy: {start: 7, span: 6}, img: {start: 1, span: 6}},
  },
};

/**
 * Generate CSS variables for grid positioning based on variant and copy side.
 *
 * @param {string} variantName - The selected variation name
 * @param {string} copySide - 'split-copy-left' or 'split-copy-right'
 * @returns {object} CSS style object with grid position vars
 */
export const generateGridVars = (variantName, copySide = 'split-copy-left') => {
  const layout = LAYOUTS[variantName] || DEFAULT_LAYOUT;
  const side = copySide === 'split-copy-right' ? 'copyRight' : 'copyLeft';
  const vars = {};

  // Generate vars for each breakpoint
  Object.entries(layout).forEach(([breakpoint, positions]) => {
    const pos = positions[side];
    const prefix = breakpoint === 'Default' ? '' : `--${breakpoint}-`;

    vars[`${prefix}copy-start`] = pos.copy.start;
    vars[`${prefix}copy-span`] = pos.copy.span;
    vars[`${prefix}img-start`] = pos.img.start;
    vars[`${prefix}img-span`] = pos.img.span;
  });

  return vars;
};

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

    const prefix = key === 'Default' ? '' : `${key.toLowerCase()}-`;
    const {paddingTop, paddingBottom} = val.settings || {};

    const topValue = paddingTop !== undefined ? paddingTop : 9;
    const bottomValue = paddingBottom !== undefined ? paddingBottom : 9;

    vars[`--${prefix}padding-top`] = `${topValue * 0.25}rem`;
    vars[`--${prefix}padding-bottom`] = `${bottomValue * 0.25}rem`;
  });

  return vars;
};
