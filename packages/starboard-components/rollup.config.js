import copy from 'rollup-plugin-copy'
import modify from 'rollup-plugin-modify'

export default {
	// input: 'css/components/button/button-config.css',
	// output: {
	// 	file: '../../src/css/components/button.css',
	// 	format: 'css'
	// },
  plugins: [
    copy({
      targets: [
        { src: 'css/components/button/button-config.css', dest: '../../src/css/components' },
        { src: 'liquid/components/button/button.liquid', dest: '../../shopify/snippets' }
        // { src: ['assets/fonts/arial.woff', 'assets/fonts/arial.woff2'], dest: 'dist/public/fonts' },
        // { src: 'assets/images/**/*', dest: 'dist/public/images' }
      ]
    }),
    modify({
      find: '@import "tailwindcss/components";',
      replace: '@import "tailwindcss/components";\n/** Anchour Components */\nimport "@anchour/starboard/css/components/button/index";\nimport "components/button";'
    })
  ]
};