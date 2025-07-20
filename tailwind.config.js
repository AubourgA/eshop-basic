/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors : {
        primary100: '#BE185D',
        primary80: '#F9A8D4',
        primary20: '#FCE7F3',
      }
    },
  },
  safelist: [
    'bg-yellow-100',
    'bg-green-100',
    'bg-blue-100',
    'bg-red-100',
    'text-yellow-600',
    'text-green-600',
    'text-blue-600',
    'text-red-600',
  ],
  plugins: [],
}
