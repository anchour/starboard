import plugin from "tailwindcss/plugin";

export default plugin(function ({ addUtilities }) {
  addUtilities({
    ".hide-scrollbars": {
      overflowY: "scroll",
      scrollbarWidth: "none",
      "-ms-overflow-style": "none",
    },
    ".hide-scrollbars::-webkit-scrollbar": {
      width: 0,
      height: 0,
    },
  });
});
