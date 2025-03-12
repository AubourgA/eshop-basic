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
  plugins: [],
}
