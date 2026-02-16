/**
 * Breakpoint Component
 *
 * A reusable UI component for displaying breakpoint-specific controls
 * with a toggle to enable/disable settings per breakpoint.
 */

const {
  components: {ToggleControl, PanelRow},
  element: {Fragment},
} = wp;

const BreakpointComponent = ({
  breakpoint: {name, breakpoint, active},
  showToggle = true,
  onToggleChange,
  children,
}) => {
  let deviceType;
  switch (name.toLowerCase()) {
    case 'md':
      deviceType = 'Tablet';
      break;
    case 'lg':
      deviceType = 'Laptop';
      break;
    case 'xl':
      deviceType = 'Desktop';
      break;
    default:
      deviceType = name;
  }

  const screenSize =
    breakpoint === '390px and up' ? breakpoint : `${breakpoint} and up`;

  return (
    <div className="breakpoint-controls">
      <PanelRow>
        <div className="breakpoint-controls__header">
          <b>{deviceType}</b>
          <span className="breakpoint-controls__header-description">
            ({screenSize})
          </span>
        </div>

        {showToggle && (
          <ToggleControl
            checked={active}
            className="breakpoint-controls__toggle"
            onChange={onToggleChange}
            __nextHasNoMarginBottom={true}
          />
        )}
      </PanelRow>

      {active && <Fragment>{children}</Fragment>}
    </div>
  );
};

export default BreakpointComponent;

/**
 * Create a breakpoint configuration object.
 *
 * @param {string} name - Breakpoint key (e.g., 'Default', 'md', 'lg', 'xl')
 * @param {string} breakpoint - CSS breakpoint value (e.g., '768px')
 * @param {boolean} active - Whether this breakpoint is enabled
 * @param {object} settings - Additional settings
 * @param {object} values - Current values for this breakpoint
 * @returns {object} Breakpoint configuration object
 */
export const createBreakpointFromScreen = (
  name = 'Default',
  breakpoint,
  active,
  settings = {},
  values = {},
) =>
  Object.assign({
    name,
    breakpoint,
    active,
    settings: {
      ...settings,
      ...values,
    },
  });
