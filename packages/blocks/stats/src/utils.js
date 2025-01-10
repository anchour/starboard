/* Base block class name */
export const blockClasses = () => 'wp-block-stats';

/**
 * Builds class names for the `stats` block for width/offsets.
 *
 * @param {object} breakpoints
 *
 * @returns (string|string)[]
 */
export const statClasses = (breakpoints) =>
  Object.keys(breakpoints).map((screen) => {
    const {active, settings: {perRow = false} = {perRow: 4}} =
      breakpoints[screen];

    const prefix = screen === 'Default' ? '' : `${screen}:`;

    if (!active) {
      return '';
    }

    let className = [];

    if (perRow !== false) {
      className.push(`${prefix}grid-cols-${perRow}`);
      className.push(`${prefix}stats-per-row-${perRow}`);
    }

    return className.join(' ');
  });
