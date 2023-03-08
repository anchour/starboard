const plugin = require("tailwindcss/plugin");

module.exports = plugin(function ({ addUtilities }) {
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
