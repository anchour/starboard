#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const srcDir = path.resolve('src/styles/components');
const distDir = path.resolve('dist/styles/components');

// Ensure dist directories exist
fs.mkdirSync(distDir, { recursive: true });
fs.mkdirSync(path.resolve('dist/styles'), { recursive: true });

// Get all component directories
const components = fs.readdirSync(srcDir, { withFileTypes: true })
  .filter(dirent => dirent.isDirectory())
  .map(dirent => dirent.name);

console.log('Building components:', components);

for (const component of components) {
  const componentSrcDir = path.join(srcDir, component);
  const componentDistDir = path.join(distDir, component);
  
  // Create component dist directory
  fs.mkdirSync(componentDistDir, { recursive: true });
  
  // Get all CSS files in the component directory
  const cssFiles = fs.readdirSync(componentSrcDir)
    .filter(file => file.endsWith('.css'));
  
  for (const cssFile of cssFiles) {
    const input = path.join(componentSrcDir, cssFile);
    const output = path.join(componentDistDir, cssFile);
    
    console.log(`Building: ${component}/${cssFile}`);
    
    // Build each CSS file with Tailwind
    try {
      execSync(`npx tailwindcss -i ${input} -o ${output} --minify`, { 
        stdio: 'inherit' 
      });
    } catch (error) {
      console.error(`Error building ${component}/${cssFile}:`, error.message);
    }
  }
}

console.log('Component build complete!');