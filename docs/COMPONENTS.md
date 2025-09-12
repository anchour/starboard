# Component Specifications

## Overview

This document provides detailed specifications for all Starboard components, including HTML structure, data attributes, variants, and usage examples.

---

## 1. Columns Component

### Purpose
Flexible grid-based layout system using CSS Grid, supporting responsive breakpoints and various column configurations.

### HTML Structure
```html
<div class="sb-columns" 
     data-columns="4" 
     data-columns-tablet="2" 
     data-columns-mobile="1"
     data-gap="default"
     data-spacing-top="lg"
     data-spacing-bottom="lg">
  <div>Column 1 content</div>
  <div>Column 2 content</div>
  <div>Column 3 content</div>
  <div>Column 4 content</div>
</div>
```

### Data Attributes
| Attribute | Values | Default | Description |
|-----------|--------|---------|-------------|
| `data-columns` | 1-6 | 2 | Desktop column count |
| `data-columns-tablet` | 1-4 | 2 | Tablet column count |
| `data-columns-mobile` | 1-2 | 1 | Mobile column count |
| `data-gap` | none, sm, default, lg, xl | default | Gap between columns |
| `data-layout` | grid, sidebar-left, sidebar-right, split | grid | Special layouts |

### Variants

#### Standard Grid
```html
<div class="sb-columns" data-columns="3">
  <!-- 3 equal columns on desktop -->
</div>
```

#### Sidebar Layout
```html
<div class="sb-columns" data-layout="sidebar-left">
  <aside>Sidebar content</aside>
  <main>Main content</main>
</div>
```

#### Split Layout
```html
<div class="sb-columns" data-layout="split">
  <div>Left 50%</div>
  <div>Right 50%</div>
</div>
```

### Use Cases
- Page layouts
- Card grids
- Feature lists
- Gallery layouts
- Form layouts

---

## 2. Button Component

### Purpose
Versatile button component with multiple variants, sizes, and states.

### HTML Structure
```html
<a class="sb-button" 
   href="/action"
   data-variant="primary"
   data-size="default"
   data-icon="arrow-right"
   data-icon-position="right">
  Click Me
</a>

<!-- Button element -->
<button class="sb-button" 
        type="submit"
        data-variant="secondary"
        data-size="lg">
  Submit Form
</button>
```

### Data Attributes
| Attribute | Values | Default | Description |
|-----------|--------|---------|-------------|
| `data-variant` | primary, secondary, outline, ghost, link | primary | Button style |
| `data-size` | sm, default, lg, xl | default | Button size |
| `data-icon` | Icon name | - | Icon identifier |
| `data-icon-position` | left, right | left | Icon position |
| `data-full-width` | true, false | false | Full width button |
| `data-loading` | true, false | false | Loading state |
| `data-disabled` | true, false | false | Disabled state |

### Variants

#### Primary Button
```html
<a class="sb-button" data-variant="primary" href="#">
  Get Started
</a>
```

#### Secondary Button
```html
<button class="sb-button" data-variant="secondary">
  Learn More
</button>
```

#### Outline Button
```html
<button class="sb-button" data-variant="outline">
  Download
</button>
```

#### Ghost Button
```html
<button class="sb-button" data-variant="ghost">
  Cancel
</button>
```

#### Link Button
```html
<a class="sb-button" data-variant="link" href="#">
  View Details →
</a>
```

### Button Groups
```html
<div class="sb-button-group">
  <button class="sb-button" data-variant="primary">Save</button>
  <button class="sb-button" data-variant="outline">Cancel</button>
</div>
```

---

## 3. Hero Component

### Purpose
Large, prominent sections typically used at the top of pages for headlines and calls-to-action.

### HTML Structure
```html
<section class="sb-hero"
         data-height="tall"
         data-alignment="center"
         data-overlay="dark"
         data-spacing-top="none"
         data-spacing-bottom="xl">
  <div class="sb-hero__background">
    <img src="hero-bg.jpg" alt="" />
  </div>
  <div class="sb-hero__content">
    <h1 class="sb-hero__title">Welcome to Our Platform</h1>
    <p class="sb-hero__subtitle">Build amazing experiences with our tools</p>
    <div class="sb-hero__actions">
      <a class="sb-button" data-variant="primary" data-size="lg" href="#">
        Get Started
      </a>
      <a class="sb-button" data-variant="outline" data-size="lg" href="#">
        Learn More
      </a>
    </div>
  </div>
</section>
```

### Data Attributes
| Attribute | Values | Default | Description |
|-----------|--------|---------|-------------|
| `data-height` | short, medium, tall, full | medium | Hero height |
| `data-alignment` | left, center, right | center | Content alignment |
| `data-overlay` | none, light, dark, gradient | none | Background overlay |
| `data-background` | image, video, color | image | Background type |

### Variants

#### Centered Hero
```html
<section class="sb-hero" data-alignment="center" data-height="tall">
  <!-- Centered content, full viewport height -->
</section>
```

#### Left-Aligned Hero
```html
<section class="sb-hero" data-alignment="left" data-height="medium">
  <!-- Left-aligned content, 75vh height -->
</section>
```

#### Video Background Hero
```html
<section class="sb-hero" data-background="video">
  <div class="sb-hero__background">
    <video autoplay muted loop>
      <source src="hero-video.mp4" type="video/mp4">
    </video>
  </div>
  <!-- Content -->
</section>
```

---

## 4. Card Component

### Purpose
Flexible content containers for displaying grouped information.

### HTML Structure
```html
<article class="sb-card"
         data-layout="vertical"
         data-spacing="default"
         data-shadow="default">
  <div class="sb-card__media">
    <img src="card-image.jpg" alt="Card image" />
  </div>
  <div class="sb-card__content">
    <h3 class="sb-card__title">Card Title</h3>
    <p class="sb-card__text">Card description text goes here.</p>
    <div class="sb-card__meta">
      <span class="sb-card__date">Jan 1, 2024</span>
      <span class="sb-card__category">Category</span>
    </div>
    <div class="sb-card__actions">
      <a class="sb-button" data-variant="link" href="#">Read More</a>
    </div>
  </div>
</article>
```

### Data Attributes
| Attribute | Values | Default | Description |
|-----------|--------|---------|-------------|
| `data-layout` | vertical, horizontal, overlay | vertical | Card layout |
| `data-spacing` | none, sm, default, lg | default | Internal spacing |
| `data-shadow` | none, sm, default, lg | default | Shadow depth |
| `data-border` | true, false | false | Show border |
| `data-hover` | lift, glow, none | lift | Hover effect |

### Variants

#### Vertical Card
```html
<article class="sb-card" data-layout="vertical">
  <!-- Image on top, content below -->
</article>
```

#### Horizontal Card
```html
<article class="sb-card" data-layout="horizontal">
  <!-- Image on left, content on right -->
</article>
```

#### Overlay Card
```html
<article class="sb-card" data-layout="overlay">
  <!-- Content overlays image -->
</article>
```

---

## 5. Carousel Component

### Purpose
Interactive slideshow for images, content, or cards.

### HTML Structure
```html
<div class="sb-carousel"
     data-carousel
     data-autoplay="true"
     data-duration="5000"
     data-items="1"
     data-items-tablet="1"
     data-items-mobile="1">
  <div class="sb-carousel__track">
    <div class="sb-carousel__slide" data-slide>
      <!-- Slide 1 content -->
    </div>
    <div class="sb-carousel__slide" data-slide>
      <!-- Slide 2 content -->
    </div>
    <div class="sb-carousel__slide" data-slide>
      <!-- Slide 3 content -->
    </div>
  </div>
  <button class="sb-carousel__prev" data-carousel-prev>Previous</button>
  <button class="sb-carousel__next" data-carousel-next>Next</button>
  <div class="sb-carousel__indicators" data-carousel-indicators></div>
</div>
```

### Data Attributes
| Attribute | Values | Default | Description |
|-----------|--------|---------|-------------|
| `data-carousel` | - | - | Initialize carousel |
| `data-autoplay` | true, false | false | Auto-advance slides |
| `data-duration` | Number (ms) | 5000 | Autoplay duration |
| `data-items` | 1-6 | 1 | Items visible (desktop) |
| `data-items-tablet` | 1-4 | 1 | Items visible (tablet) |
| `data-items-mobile` | 1-2 | 1 | Items visible (mobile) |
| `data-loop` | true, false | true | Infinite loop |
| `data-gap` | none, sm, default, lg | default | Gap between items |

### JavaScript API
```javascript
// Manual initialization
const carousel = new Carousel(element, {
  autoplay: true,
  duration: 3000,
  onSlideChange: (index) => console.log(`Slide ${index}`)
});

// Methods
carousel.next();
carousel.prev();
carousel.goTo(2);
carousel.play();
carousel.pause();
```

---

## 6. Table Component

### Purpose
Display tabular data with optional sorting and responsive behavior.

### HTML Structure
```html
<div class="sb-table-wrapper">
  <table class="sb-table"
         data-sortable="true"
         data-responsive="stack">
    <thead>
      <tr>
        <th data-sort="string">Name</th>
        <th data-sort="number">Price</th>
        <th data-sort="date">Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td data-label="Name">Product A</td>
        <td data-label="Price">$99.99</td>
        <td data-label="Date">2024-01-01</td>
        <td data-label="Actions">
          <button class="sb-button" data-variant="link">Edit</button>
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

### Data Attributes
| Attribute | Values | Default | Description |
|-----------|--------|---------|-------------|
| `data-sortable` | true, false | false | Enable column sorting |
| `data-responsive` | stack, scroll, hide | stack | Mobile behavior |
| `data-striped` | true, false | false | Striped rows |
| `data-hover` | true, false | true | Hover state |
| `data-bordered` | true, false | true | Table borders |

### Responsive Modes

#### Stack Mode (Mobile)
```css
/* Stacks cells vertically on mobile */
@media (max-width: 768px) {
  .sb-table[data-responsive="stack"] td {
    display: block;
  }
}
```

#### Scroll Mode
```html
<div class="sb-table-wrapper" data-responsive="scroll">
  <!-- Table scrolls horizontally on mobile -->
</div>
```

---

## 7. Testimonial Component

### Purpose
Display customer testimonials and reviews.

### HTML Structure
```html
<blockquote class="sb-testimonial"
            data-layout="card"
            data-rating="5">
  <div class="sb-testimonial__content">
    <div class="sb-testimonial__rating" aria-label="5 out of 5 stars">
      ★★★★★
    </div>
    <p class="sb-testimonial__text">
      "This product has transformed our business. Highly recommended!"
    </p>
  </div>
  <footer class="sb-testimonial__footer">
    <img class="sb-testimonial__avatar" src="avatar.jpg" alt="John Doe">
    <div class="sb-testimonial__author">
      <cite class="sb-testimonial__name">John Doe</cite>
      <p class="sb-testimonial__role">CEO, Company Inc.</p>
    </div>
  </footer>
</blockquote>
```

### Data Attributes
| Attribute | Values | Default | Description |
|-----------|--------|---------|-------------|
| `data-layout` | card, quote, minimal | card | Visual style |
| `data-rating` | 0-5 | - | Star rating |
| `data-size` | sm, default, lg | default | Text size |

### Layout Variants

#### Card Layout
```html
<blockquote class="sb-testimonial" data-layout="card">
  <!-- Card with shadow and padding -->
</blockquote>
```

#### Quote Layout
```html
<blockquote class="sb-testimonial" data-layout="quote">
  <!-- Large quote marks, centered -->
</blockquote>
```

#### Minimal Layout
```html
<blockquote class="sb-testimonial" data-layout="minimal">
  <!-- Simple, no decoration -->
</blockquote>
```

---

## 8. Card Carousel Component

### Purpose
Combines cards with carousel functionality for showcasing multiple cards with navigation.

### HTML Structure
```html
<div class="sb-card-carousel"
     data-carousel
     data-items="3"
     data-items-tablet="2"
     data-items-mobile="1"
     data-gap="default">
  <div class="sb-carousel__track">
    <article class="sb-card" data-slide>
      <!-- Card 1 -->
    </article>
    <article class="sb-card" data-slide>
      <!-- Card 2 -->
    </article>
    <article class="sb-card" data-slide>
      <!-- Card 3 -->
    </article>
    <article class="sb-card" data-slide>
      <!-- Card 4 -->
    </article>
  </div>
  <button class="sb-carousel__prev" data-carousel-prev>Previous</button>
  <button class="sb-carousel__next" data-carousel-next>Next</button>
</div>
```

### Features
- Inherits all carousel functionality
- Cards maintain consistent height
- Responsive items per view
- Touch/swipe support
- Peek next/previous cards option

---

## 9. Rates Component

### Purpose
Display financial rates, pricing, or numerical data with trends.

### HTML Structure
```html
<div class="sb-rates"
     data-layout="table"
     data-highlight="best">
  <div class="sb-rates__item" data-trend="up">
    <h4 class="sb-rates__title">30-Year Fixed</h4>
    <div class="sb-rates__value">
      <span class="sb-rates__number">6.95</span>
      <span class="sb-rates__unit">%</span>
    </div>
    <div class="sb-rates__change">
      <span class="sb-rates__indicator">↑</span>
      <span class="sb-rates__delta">+0.15</span>
    </div>
    <div class="sb-rates__meta">
      <span class="sb-rates__apr">7.12% APR</span>
      <time class="sb-rates__updated">Updated 1 hour ago</time>
    </div>
  </div>
</div>
```

### Data Attributes
| Attribute | Values | Default | Description |
|-----------|--------|---------|-------------|
| `data-layout` | table, cards, list | cards | Display layout |
| `data-highlight` | best, none | none | Highlight best rate |
| `data-trend` | up, down, stable | stable | Rate trend |
| `data-comparison` | true, false | false | Show comparison |

### Use Cases
- Mortgage rates
- Interest rates
- Currency exchange
- Stock prices
- Pricing tables

---

## 10. Icon Component

### Purpose
SVG icon system with consistent sizing and styling.

### HTML Structure
```html
<!-- Standalone icon -->
<span class="sb-icon" data-icon="check" data-size="default"></span>

<!-- Icon with text -->
<div class="sb-icon-text" data-layout="horizontal">
  <span class="sb-icon" data-icon="phone" data-size="lg"></span>
  <div class="sb-icon-text__content">
    <h4 class="sb-icon-text__title">Call Us</h4>
    <p class="sb-icon-text__description">Available 24/7</p>
  </div>
</div>
```

### Data Attributes
| Attribute | Values | Default | Description |
|-----------|--------|---------|-------------|
| `data-icon` | Icon name | - | Icon identifier |
| `data-size` | xs, sm, default, lg, xl | default | Icon size |
| `data-color` | current, primary, secondary | current | Icon color |
| `data-layout` | horizontal, vertical, inline | inline | Layout with text |

### Icon Library
Icons are loaded via SVG sprite:
```html
<svg class="sb-icon__svg">
  <use href="#icon-check"></use>
</svg>
```

---

## 11. Text-Media Split Component

### Purpose
Content sections with text on one side and media on the other.

### HTML Structure
```html
<section class="sb-text-media"
         data-layout="media-right"
         data-alignment="center"
         data-spacing="lg">
  <div class="sb-text-media__content">
    <h2 class="sb-text-media__title">Feature Headline</h2>
    <p class="sb-text-media__text">
      Detailed description of the feature or content.
    </p>
    <div class="sb-text-media__actions">
      <a class="sb-button" data-variant="primary" href="#">Learn More</a>
    </div>
  </div>
  <div class="sb-text-media__media">
    <img src="feature.jpg" alt="Feature image">
  </div>
</section>
```

### Data Attributes
| Attribute | Values | Default | Description |
|-----------|--------|---------|-------------|
| `data-layout` | media-left, media-right | media-right | Media position |
| `data-alignment` | top, center, bottom | center | Vertical alignment |
| `data-ratio` | 50-50, 60-40, 40-60 | 50-50 | Content ratio |
| `data-reverse-mobile` | true, false | true | Stack order on mobile |

### Layout Options

#### Media Left
```html
<section class="sb-text-media" data-layout="media-left">
  <!-- Media on left, text on right -->
</section>
```

#### 60-40 Split
```html
<section class="sb-text-media" data-ratio="60-40">
  <!-- 60% text, 40% media -->
</section>
```

---

## 12. Photo Feature Component

### Purpose
Featured image sections with optional text overlay, similar to hero but contained.

### HTML Structure
```html
<div class="sb-photo-feature"
     data-height="medium"
     data-overlay="gradient"
     data-text-position="bottom-left">
  <div class="sb-photo-feature__media">
    <img src="feature.jpg" alt="Feature photo">
  </div>
  <div class="sb-photo-feature__content">
    <h3 class="sb-photo-feature__title">Feature Title</h3>
    <p class="sb-photo-feature__caption">
      Optional caption or description text.
    </p>
  </div>
</div>
```

### Data Attributes
| Attribute | Values | Default | Description |
|-----------|--------|---------|-------------|
| `data-height` | short, medium, tall | medium | Feature height |
| `data-overlay` | none, light, dark, gradient | none | Overlay type |
| `data-text-position` | top-left, top-right, bottom-left, bottom-right, center | bottom-left | Text position |
| `data-aspect-ratio` | 16-9, 4-3, 1-1, 3-2 | 16-9 | Aspect ratio |

### Use Cases
- Article headers
- Section dividers
- Portfolio pieces
- Product showcases
- Gallery items

---

## Component Composition

### Combining Components
Components can be nested and combined:

```html
<!-- Columns containing cards -->
<div class="sb-columns" data-columns="3">
  <article class="sb-card">...</article>
  <article class="sb-card">...</article>
  <article class="sb-card">...</article>
</div>

<!-- Hero with columns -->
<section class="sb-hero">
  <div class="sb-hero__content">
    <div class="sb-columns" data-columns="2">
      <div>Left content</div>
      <div>Right content</div>
    </div>
  </div>
</section>

<!-- Card carousel with testimonials -->
<div class="sb-card-carousel">
  <blockquote class="sb-testimonial sb-card">...</blockquote>
  <blockquote class="sb-testimonial sb-card">...</blockquote>
</div>
```

## Accessibility

All components follow WCAG 2.1 AA guidelines:

- Semantic HTML elements
- ARIA labels where needed
- Keyboard navigation support
- Focus indicators
- Screen reader friendly
- Color contrast compliance
- Responsive text sizing

## Browser Support

- Chrome (last 2 versions)
- Firefox (last 2 versions)
- Safari (last 2 versions)
- Edge (last 2 versions)
- Mobile browsers (iOS Safari, Chrome)

## Performance

- CSS Grid/Flexbox for layouts (no JavaScript)
- Progressive enhancement
- Lazy loading for images
- Minimal JavaScript (only where essential)
- Tree-shakeable styles
- PurgeCSS compatible