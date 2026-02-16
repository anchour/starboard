/**
 * Screen Breakpoints Configuration
 *
 * Reads from starboardConfig (localized via PHP) with fallback to defaults.
 * Themes can customize via the `starboard_screens` PHP filter.
 */

const defaultScreens = {
  md: '768px',
  lg: '1024px',
  xl: '1280px',
};

export const screens = window.starboardConfig?.screens || defaultScreens;

/**
 * Default spacing values per breakpoint.
 *
 * Themes can customize via the `starboard_default_spacing` PHP filter.
 */
const defaultSpacing = {
  Default: {paddingTop: 16, paddingBottom: 16},
  md: {paddingTop: 18, paddingBottom: 18},
  lg: {paddingTop: 20, paddingBottom: 20},
  xl: {paddingTop: 24, paddingBottom: 24},
};

export const defaultSpacingValues = window.starboardConfig?.defaultSpacing || defaultSpacing;

/**
 * Screen keys for iteration (includes 'Default' for mobile-first).
 */
export const screenKeys = ['Default', ...Object.keys(screens)];

/**
 * Breakpoint labels mapping screen keys to display values.
 */
export const breakpointLabels = {
  Default: 'Mobile',
  ...screens,
};

/**
 * Helper to format spacing value description.
 *
 * @param {number} val - The spacing value (multiplied by 0.25rem)
 * @returns {string} Formatted description (e.g., "4rem (64px)")
 */
export const getSpacingDescription = (val) =>
  `${val * 0.25}rem (${val * 0.25 * 16}px)`;
