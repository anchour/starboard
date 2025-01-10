import Breakpoint, {createBreakpointFromScreen} from '../components/breakpoint';
import {screens} from '../block-utilities/tailwind-utilities';
import {merge} from 'lodash-es';

const {
  i18n: {__},
  blockEditor: {InspectorControls},
  components: {Panel, PanelBody, RangeControl},
  compose: {createHigherOrderComponent},
  element: {Fragment},
} = wp;

const allowlist = ['anchour/stats'];

export const breakpoints = createHigherOrderComponent(
  (BlockEdit) => (props) => {
    const {
      name,
      attributes: {breakpoints = {}, responsive = {}},
      setAttributes,
    } = props;

    if (!allowlist.includes(name)) {
      return <BlockEdit {...props} />;
    }

    const bpScreens = {Default: 'All screen sizes', ...screens};

    const updateBreakpoint = (screen, data) => {
      let bps = {...breakpoints};

      bps[screen] = bps[screen] || {};

      bps[screen] = merge(bps[screen], data);

      setAttributes({breakpoints: bps});
    };

    return (
      <Fragment>
        <BlockEdit {...props} />

        <InspectorControls>
          <Panel>
            <PanelBody title={__('Breakpoints')}>
              {Object.keys(bpScreens).map((screen, index) => {
                const current = breakpoints[screen] || {};
                const breakpoint = createBreakpointFromScreen(
                  screen,
                  bpScreens[screen],
                  current.active || false,
                  responsive,
                  current.settings || {},
                );

                const {settings} = breakpoint;

                return (
                  <Breakpoint
                    key={index}
                    breakpoint={breakpoint}
                    onToggleChange={() => {
                      let bps = {
                        ...breakpoints,
                      };

                      bps[screen] = bps[screen] || {};
                      bps[screen].active = !bps[screen].active;

                      setAttributes({breakpoints: bps});
                    }}>
                    <RangeControl
                      min={1}
                      max={6}
                      value={settings.perRow}
                      label={__('Per row')}
                      onChange={(perRow) =>
                        updateBreakpoint(screen, {
                          settings: {
                            perRow,
                          },
                        })
                      }
                    />
                  </Breakpoint>
                );
              })}
            </PanelBody>
          </Panel>
        </InspectorControls>
      </Fragment>
    );
  },
);
