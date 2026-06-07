/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js}'],
  theme: {
    extend: {
      colors: {
        'odi-negro':    '#0A0A0A',
        'odi-amarillo': '#F5C518',
        'odi-gris':     '#1A1A1A',
        'odi-gris2':    '#2A2A2A',
        'odi-texto':    '#E8E8E8',
        'odi-muted':    '#888888',
      },
      fontFamily: {
        sans:    ['DM Sans', 'system-ui', 'sans-serif'],
        display: ['Syne', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
