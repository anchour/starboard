/**
 * External dependencies.
 */
import classNames from 'classnames';

/* Base block class name */
export function blockClasses() {
  const className = classNames('wp-block-stats-item');

  return className;
}

/**
 * Builds class names for the `gallery-item` block for width/offsets.
 *
 * @param {object} breakpoints
 *
 * @returns string
 */
export const galleryItemClasses = (breakpoints) =>
  Object.keys(breakpoints).map((screen) => {
    const {
      active,
      settings: {columns = false, offset = false} = {columns: 12, offset: 0},
    } = breakpoints[screen];

    const prefix = screen === 'Default' ? '' : `${screen}:`;

    if (!active) {
      return '';
    }

    let className = [];

    if (columns !== false) {
      className.push(`${prefix}w-${columns}/12`);
    }

    if (offset !== false) {
      className.push(`${prefix}offset-${offset}/12`);
    }

    return className.join(' ');
  });
