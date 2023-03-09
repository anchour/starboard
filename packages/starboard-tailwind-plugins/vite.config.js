import path from "path";

/** @type {import('vite').UserConfig} */
export default {
  build: {
    lib: {
      entry: path.resolve(__dirname, "./src/index.js"),
      name: "tailwind-plugins.js",
      fileName: (format) => `tailwind-plugins.${format}.js`,
    },
  },
};
