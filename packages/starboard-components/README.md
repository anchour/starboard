## Installation

```bash
pnpm add @anchour/starboard-components
```

## Usage

This library includes common compnents to quickly initiallally set up commonly used components.

### Button
* add import to main.css to bring in base button styling
* button.css will be in css/components/ for further styling. Add this to main.css under the @anchour import
```
/** Anchour Components */
import '@anchour/starboard/css/components/button/index';
import 'components/button';
```

Shopify Only:
* button.liquid will be added to /shopify/snippets. This include code examples and potential variations.


### Known Issues
* a way to bring in one component at a time
* a way to read from project package.json to determine shopify or wordpress
* a better way to