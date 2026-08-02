module.exports = {
  darkMode: 'class',
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        brand: {
          DEFAULT: '#9EBF3B',
          50: '#f6f9ec',
          100: '#eaf1d8', 
          200: '#d6e4b5',
          300: '#b9d088',
          400: '#9ebf3b',
          500: '#9EBF3B',
          600: '#6b8722',
          700: '#52671e',
          800: '#43531d',
          900: '#3a471d',
        },
        accent: {
          DEFAULT: '#D6A29A',
          50: '#fbf6f5',
          100: '#f7ecea',
          200: '#f0d9d6',
          300: '#e5bcb6', 
          400: '#d6a29a',
          500: '#D6A29A',
          600: '#a66960',
          700: '#8b554e',
          800: '#724843',
          900: '#5f3f3b',
        },
        subscribe: {
          DEFAULT: '#3B82F6',
          50: '#EFF6FF',
          100: '#DBEAFE',
          200: '#BFDBFE',
          300: '#93C5FD',
          400: '#60A5FA',
          500: '#3B82F6',
          600: '#2563EB',
          700: '#1D4ED8',
          800: '#1E40AF',
          900: '#1E3A8A',
        }
      },
      fontFamily: {
        sans: ['Tajawal', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif']
      }
    }
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('tailwindcss-rtl')
  ]
}