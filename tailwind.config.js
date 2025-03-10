/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors : {
        primary: '#c8dbdf',
      }
    },
  },
  plugins: [],
}
