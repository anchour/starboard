/**
 * WordPress dependencies
 */
import {__} from '@wordpress/i18n';

/**
 * Default template for the copy side column
 */
const COPY_TEMPLATE = [
  ['core/heading', {level: 2}],
  ['core/paragraph'],
  ['core/buttons'],
];

/**
 * Template option choices for predefined columns layouts.
 * lg grid: 12 columns
 * DOM order: copy first, then image (for accessibility)
 */
/**
 * Built-in variations
 */
const builtInVariations = [
  {
    name: 'text-image__narrow-content',
    title: __('Narrow Content (4/5)'),
    description: __('Narrow content column with medium image'),
    attributes: {innerColumnCount: 2, verticalAlignment: 'center', copySide: 'split-copy-left'},
    icon: (
      <svg width="48" height="32" viewBox="0 0 48 32" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="4" y="8" width="16" height="3" fill="currentColor" />
        <rect x="4" y="14" width="16" height="6" fill="currentColor" />
        <rect x="4" y="22" width="10" height="2" fill="currentColor" />
        <rect x="26" y="4" width="18" height="24" rx="1" fill="currentColor" fillOpacity="0.3" stroke="currentColor" />
      </svg>
    ),
    innerBlocks: [
      [
        'starboard/text-image-column',
        {
          columnType: 'copy',
          className: 'text-image__copy-side',
        },
        COPY_TEMPLATE,
      ],
      [
        'starboard/text-image-column',
        {
          columnType: 'image',
          className: 'text-image__img-side',
        },
        [['core/image']],
      ],
    ],
    scope: ['block'],
  },
  {
    name: 'text-image__wide-image',
    title: __('Wide Image (4/7)'),
    description: __('Narrow content with wide image'),
    attributes: {innerColumnCount: 2, verticalAlignment: 'center', copySide: 'split-copy-left'},
    icon: (
      <svg width="48" height="32" viewBox="0 0 48 32" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="0" y="8" width="14" height="3" fill="currentColor" />
        <rect x="0" y="14" width="14" height="6" fill="currentColor" />
        <rect x="0" y="22" width="8" height="2" fill="currentColor" />
        <rect x="18" y="2" width="28" height="28" rx="1" fill="currentColor" fillOpacity="0.3" stroke="currentColor" />
      </svg>
    ),
    innerBlocks: [
      [
        'starboard/text-image-column',
        {
          columnType: 'copy',
          className: 'text-image__copy-side',
        },
        COPY_TEMPLATE,
      ],
      [
        'starboard/text-image-column',
        {
          columnType: 'image',
          className: 'text-image__img-side',
        },
        [['core/image']],
      ],
    ],
    scope: ['block'],
  },
  {
    name: 'text-image__balanced',
    title: __('Balanced (6/5)'),
    description: __('Balanced content and image columns'),
    attributes: {innerColumnCount: 2, verticalAlignment: 'center', copySide: 'split-copy-left'},
    icon: (
      <svg width="48" height="32" viewBox="0 0 48 32" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="0" y="8" width="20" height="3" fill="currentColor" />
        <rect x="0" y="14" width="20" height="6" fill="currentColor" />
        <rect x="0" y="22" width="12" height="2" fill="currentColor" />
        <rect x="26" y="4" width="20" height="24" rx="1" fill="currentColor" fillOpacity="0.3" stroke="currentColor" />
      </svg>
    ),
    innerBlocks: [
      [
        'starboard/text-image-column',
        {
          columnType: 'copy',
          className: 'text-image__copy-side',
        },
        COPY_TEMPLATE,
      ],
      [
        'starboard/text-image-column',
        {
          columnType: 'image',
          className: 'text-image__img-side',
        },
        [['core/image']],
      ],
    ],
    scope: ['block'],
  },
];

/**
 * Get custom variations from starboardConfig
 */
const getCustomVariations = () => {
  const customVariants = window.starboardConfig?.customVariants?.variations || [];

  return customVariants.map((variant) => ({
    name: variant.name,
    title: variant.title,
    description: variant.description || '',
    attributes: {innerColumnCount: 2, verticalAlignment: 'center', copySide: 'split-copy-left'},
    // Generic icon for custom variants
    icon: (
      <svg width="48" height="32" viewBox="0 0 48 32" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="2" y="8" width="18" height="3" fill="currentColor" />
        <rect x="2" y="14" width="18" height="6" fill="currentColor" />
        <rect x="2" y="22" width="10" height="2" fill="currentColor" />
        <rect x="24" y="4" width="22" height="24" rx="1" fill="currentColor" fillOpacity="0.3" stroke="currentColor" />
        <circle cx="35" cy="16" r="4" fill="currentColor" fillOpacity="0.5" />
      </svg>
    ),
    innerBlocks: [
      [
        'starboard/text-image-column',
        {
          columnType: 'copy',
          className: 'text-image__copy-side',
        },
        COPY_TEMPLATE,
      ],
      [
        'starboard/text-image-column',
        {
          columnType: 'image',
          className: 'text-image__img-side',
        },
        [['core/image']],
      ],
    ],
    scope: ['block'],
  }));
};

/**
 * Merge built-in and custom variations
 */
const variations = [...builtInVariations, ...getCustomVariations()];

export default variations;
