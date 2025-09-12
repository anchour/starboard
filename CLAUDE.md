# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Starboard is a WordPress Gutenberg blocks monorepo using pnpm workspaces. It contains custom block components for WordPress sites, primarily FAQ and Stats blocks with their nested sub-blocks.

## Development Commands

### Root Level Commands
```bash
# Start development server with hot reload
npm run start:hot

# Start development server
npm run start

# Build all blocks for production
npm run build
```

### Package Level Commands
For individual block packages (`packages/blocks/*/`):
```bash
# Start development server
npm run start

# Start with hot reload
npm run start:hot

# Build for production
npm run build
```

## Architecture

### Monorepo Structure
- **pnpm workspaces**: Manages multiple packages under `packages/` directory
- **Main workspace config**: `pnpm-workspace.yaml` defines package locations
- **WordPress Scripts**: Uses `@wordpress/scripts` for build tooling

### Package Organization
```
packages/
├── blocks/          # Active WordPress blocks
│   ├── faq/        # FAQ container block with schema.org support
│   └── faq-item/   # Individual FAQ item (child of FAQ block)
├── temp/           # Temporary/legacy blocks (stats components)
└── utils/          # Shared utilities across blocks
```

### Block Development Patterns

#### Block Registration
Blocks use two patterns:
1. **Standard pattern** (faq block): Import metadata, use `registerBlockType` with separate edit/save files
2. **Inline pattern** (faq-item): Single file with all logic, direct `registerBlockType` call

#### State Management
- FAQ blocks use custom Redux store (`starboard/faq-store`) for parent-child communication
- FAQ items register themselves with the parent FAQ block through the store

#### WordPress Dependencies
All blocks rely on WordPress packages:
- `@wordpress/blocks` - Block registration
- `@wordpress/block-editor` - Editor components (RichText, BlockControls, etc.)
- `@wordpress/components` - UI components
- `@wordpress/element` - React utilities
- `@wordpress/data` - State management

#### Schema.org Integration
FAQ blocks generate structured data for SEO, building FAQPage schema from child FAQ items.

### Key Technical Details

- **Block API Version**: 3
- **Module System**: ES6 modules (`"type": "module"` in package.json)
- **Build Output**: Each block builds to its own `build/` directory
- **Block Metadata**: Defined in `block.json` files following WordPress schema
- **Styling**: Mix of inline styles and CSS files (Tailwind configured but usage varies)