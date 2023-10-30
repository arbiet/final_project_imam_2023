/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./public/**/*.{html,js,php}"],
  theme: {
    extend: {
      fontFamily: {
        inter: ['Inter'],
      },
      colors: {
        customOrange: '#CD5C08',
        customBeige: '#F5E8B7',
        customGreen: '#C1D8C3',
        customTeal: '#6A9C89',
      },
    },
    plugins: [],
  }
}