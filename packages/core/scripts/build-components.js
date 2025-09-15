#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const coreComponentsDir = path.resolve('src/styles/components');
const blocksDir = path.resolve('../blocks');
const distDir = path.resolve('dist/styles/components');

// Ensure dist directories exist
fs.mkdirSync(distDir, { recursive: true });
fs.mkdirSync(path.resolve('dist/styles'), { recursive: true });

// Get all component directories from core
let components = [];
if (fs.existsSync(coreComponentsDir)) {
  const coreComponents = fs.readdirSync(coreComponentsDir, { withFileTypes: true })
    .filter(dirent => dirent.isDirectory())
    .map(dirent => ({ name: dirent.name, type: 'core' }));
  components.push(...coreComponents);
}

// Get all block directories that have styles
if (fs.existsSync(blocksDir)) {
  const blockDirs = fs.readdirSync(blocksDir, { withFileTypes: true })
    .filter(dirent => dirent.isDirectory() && dirent.name !== 'node_modules')
    .map(dirent => dirent.name);
  
  for (const blockName of blockDirs) {
    const blockStylesDir = path.join(blocksDir, blockName, 'src/styles');
    if (fs.existsSync(blockStylesDir)) {
      components.push({ name: blockName, type: 'block' });
    }
  }
}

console.log('Building components:', components.map(c => c.name));

for (const component of components) {
  let componentSrcDir;
  
  if (component.type === 'core') {
    componentSrcDir = path.join(coreComponentsDir, component.name);
  } else if (component.type === 'block') {
    componentSrcDir = path.join(blocksDir, component.name, 'src/styles');
  }
  
  const componentDistDir = path.join(distDir, component.name);
  
  // Create component dist directory
  fs.mkdirSync(componentDistDir, { recursive: true });
  
  // Get all CSS files in the component directory
  const cssFiles = fs.readdirSync(componentSrcDir)
    .filter(file => file.endsWith('.css'));
  
  for (const cssFile of cssFiles) {
    const input = path.join(componentSrcDir, cssFile);
    const output = path.join(componentDistDir, cssFile);
    
    console.log(`Building: ${component.name}/${cssFile}`);
    
    // Build each CSS file with Tailwind
    try {
      execSync(`npx tailwindcss -i ${input} -o ${output} --minify`, { 
        stdio: 'inherit' 
      });
    } catch (error) {
      console.error(`Error building ${component.name}/${cssFile}:`, error.message);
    }
  }
}

console.log('Component build complete!');