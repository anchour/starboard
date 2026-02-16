/**
 * Starboard Shared Utilities
 *
 * This module exposes shared components and utilities as globals
 * so they can be used by both plugin blocks and theme overrides.
 *
 * Exposed on window.starboard:
 * - starboard.components.Breakpoint
 * - starboard.components.createBreakpointFromScreen
 * - starboard.utils.screens
 * - starboard.utils.screenKeys
 * - starboard.utils.breakpointLabels
 * - starboard.utils.defaultSpacingValues
 * - starboard.utils.getSpacingDescription
 * - starboard.utils.getTagNameFromBlockName
 * - starboard.config (lazy reference to starboardConfig)
 */

import Breakpoint, { createBreakpointFromScreen } from '../components/breakpoint.js';
import {
  screens,
  screenKeys,
  breakpointLabels,
  defaultSpacingValues,
  getSpacingDescription,
} from '../utils/screens.js';
import { getTagNameFromBlockName } from '../utils/helpers.js';

// Initialize global namespace (preserve existing properties)
if (typeof window.starboard === 'undefined') {
  window.starboard = {};
}

// Expose components (with null checks for safety)
window.starboard.components = Object.freeze({
  Breakpoint: Breakpoint || null,
  createBreakpointFromScreen: createBreakpointFromScreen || null,
});

// Expose utilities (with null checks for safety)
window.starboard.utils = Object.freeze({
  screens: screens || {},
  screenKeys: screenKeys || [],
  breakpointLabels: breakpointLabels || {},
  defaultSpacingValues: defaultSpacingValues || {},
  getSpacingDescription: getSpacingDescription || (() => ''),
  getTagNameFromBlockName: getTagNameFromBlockName || ((name) => name),
});

// Expose config as getter for lazy access (starboardConfig may load after this)
Object.defineProperty(window.starboard, 'config', {
  get() {
    return window.starboardConfig || {};
  },
  enumerable: true,
  configurable: false,
});
