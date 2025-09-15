#!/usr/bin/env node

/**
 * Build script for block style variants
 * Processes CSS files in each block's src/styles directory
 * Outputs compiled CSS to build/styles directory
 */

import { execSync } from 'child_process';
import { existsSync, mkdirSync, readdirSync, statSync } from 'fs';
import { join, resolve } from 'path';
import { fileURLToPath } from 'url';
import { dirname } from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const rootDir = resolve(__dirname, '..');
const blocksDir = resolve(rootDir, 'packages/blocks');

// Get all block directories
const getBlockDirectories = () => {
  const blocks = [];
  const items = readdirSync(blocksDir);
  
  for (const item of items) {
    const itemPath = join(blocksDir, item);
    if (statSync(itemPath).isDirectory()) {
      blocks.push({
        name: item,
        path: itemPath
      });
    }
  }
  
  return blocks;
};

// Process styles for a single block
const processBlockStyles = (block) => {
  const stylesDir = join(block.path, 'src/styles');
  const buildStylesDir = join(block.path, 'build/styles');
  
  // Check if styles directory exists
  if (!existsSync(stylesDir)) {
    console.log(`  ⚠️  No styles directory found for ${block.name}`);
    return;
  }
  
  // Create build/styles directory
  mkdirSync(buildStylesDir, { recursive: true });
  
  // Get all CSS files in styles directory
  const cssFiles = readdirSync(stylesDir).filter(file => file.endsWith('.css'));
  
  if (cssFiles.length === 0) {
    console.log(`  ⚠️  No CSS files found in ${block.name}/src/styles`);
    return;
  }
  
  // Process each CSS file
  for (const cssFile of cssFiles) {
    const inputFile = join(stylesDir, cssFile);
    const outputFile = join(buildStylesDir, cssFile);
    
    console.log(`  📝 Building ${block.name}/styles/${cssFile}`);
    
    try {
      // Use Tailwind CSS to process the file
      // This will use the root tailwind.config.js
      execSync(
        `npx tailwindcss -i "${inputFile}" -o "${outputFile}" --minify`,
        {
          stdio: 'pipe',
          cwd: rootDir
        }
      );
      console.log(`  ✅ Built ${cssFile}`);
    } catch (error) {
      console.error(`  ❌ Error building ${cssFile}:`, error.message);
    }
  }
};

// Main build function
const buildBlockStyles = () => {
  console.log('🚀 Building block style variants...\n');
  
  const blocks = getBlockDirectories();
  
  if (blocks.length === 0) {
    console.log('No blocks found in packages/blocks');
    return;
  }
  
  console.log(`Found ${blocks.length} blocks to process:\n`);
  
  for (const block of blocks) {
    console.log(`📦 Processing ${block.name}...`);
    processBlockStyles(block);
    console.log('');
  }
  
  console.log('✨ Block styles build complete!\n');
  
  // Also process the main style.css for each block (WordPress compatibility)
  console.log('🔧 Processing main style.css files for WordPress...\n');
  
  for (const block of blocks) {
    const mainStyleFile = join(block.path, 'src/style.css');
    const outputStyleFile = join(block.path, 'build/style-index.css');
    
    if (existsSync(mainStyleFile)) {
      console.log(`  📝 Building ${block.name}/style.css`);
      
      try {
        execSync(
          `npx tailwindcss -i "${mainStyleFile}" -o "${outputStyleFile}" --minify`,
          {
            stdio: 'pipe',
            cwd: rootDir
          }
        );
        console.log(`  ✅ Built style-index.css`);
      } catch (error) {
        console.error(`  ❌ Error building style-index.css:`, error.message);
      }
    }
  }
  
  console.log('\n🎉 All styles processed successfully!');
};

// Run the build
buildBlockStyles();