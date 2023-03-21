import copy from 'rollup-plugin-copy';
import insert from 'rollup-plugin-insert';
import path from 'path';
import { fileURLToPath } from 'url';


const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);


export default {
	input: '',
	output: {
     type: 'module',
  },
  plugins: [
    copy({
      targets: [
        { src: path.resolve(__dirname, './css/components/button/button.css'), dest: path.resolve(process.cwd(), './src/css/components') },
        { src: path.resolve(__dirname, './liquid/components/button/button.liquid'), dest: path.resolve(process.cwd(), './shopify/snippets') }
        // { src: ['assets/fonts/arial.woff', 'assets/fonts/arial.woff2'], dest: 'dist/public/fonts' },
        // { src: 'assets/images/**/*', dest: 'dist/public/images' }
      ]
    }),
    insert.append(
      'import "@anchour/starboard-components/css/components/button/index";\nimport "components/button";',
      {
        include: "path.resolve(process.cwd(), './src/css/main.css')",
      },
    )
    
  ]
};