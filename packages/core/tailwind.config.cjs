/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './src/**/*.{css,js,jsx,ts,tsx,html}'
  ],
  theme: {
    extend: {
      container: {
        center: true,
        padding: 'var(--sb-gutter)',
        screens: {
          sm: '640px',
          md: '768px',
          lg: '1024px',
          xl: '1280px',
        },
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        mono: ['DM Mono', 'monospace'],
      },
      spacing: {
        'sb-xs': 'var(--sb-spacing-xs)',
        'sb-sm': 'var(--sb-spacing-sm)',
        'sb-md': 'var(--sb-spacing-md)',
        'sb-lg': 'var(--sb-spacing-lg)',
        'sb-xl': 'var(--sb-spacing-xl)',
        'sb-2xl': 'var(--sb-spacing-2xl)',
        'sb-3xl': 'var(--sb-spacing-3xl)',
        'sb-4xl': 'var(--sb-spacing-4xl)',
        'sb-5xl': 'var(--sb-spacing-5xl)',
        'sb-6xl': 'var(--sb-spacing-6xl)',
      },
      gap: {
        'sb-xs': 'var(--sb-gap-xs)',
        'sb-sm': 'var(--sb-gap-sm)',
        'sb-md': 'var(--sb-gap-md)',
        'sb-lg': 'var(--sb-gap-lg)',
        'sb-xl': 'var(--sb-gap-xl)',
        'sb-2xl': 'var(--sb-gap-2xl)',
      },
    },
  },
  plugins: [],
}