/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "*/assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        amarillo: '#FFD600',
        gris: '#4A4A45'
      }
    },
  },
  plugins: [],
}

