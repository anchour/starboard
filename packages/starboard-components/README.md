## Installation

*Future Requirement* Project package.json must specify if it is a **shopify** or **wordpress** project.
```json
{
  "buildType": "shopify",
}
```

```bash
pnpm add @anchour/starboard-components
```

## Usage

This library includes common compnents to quickly initially set up commonly used components.

### Button
* will add import to main.css to bring in base button styling
* button.css will be in css/components/ for further styling. This will be added to main.css under the @anchour import
```css
/** Anchour Components */
import '@anchour/starboard/css/components/button/index';
import 'components/button';
```

Shopify Only:
-[ ] button.liquid will be added to /shopify/snippets. This include code examples and potential variations.


### Known Issues
-[x] rollup plugin modify may not actually find the file, need a way to specify file name.
-[ ] a way to bring in one component at a time
-[ ] a way to read from project package.json to determine shopify or wordpress
-[ ] a better way to traverse files to bring in components (based on which project it is)