const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');

/** @type {import('tailwindcss').Config} */
module.exports = {

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Using CSS variables for colors with HSL
                background: 'hsl(var(--background) / <alpha-value>)',
                'card-bg': 'hsl(var(--card-bg) / <alpha-value>)',
                'primary-text': 'hsl(var(--primary-text) / <alpha-value>)',
                'secondary-text': 'hsl(var(--secondary-text) / <alpha-value>)',
                'border-color': 'hsl(var(--border-color) / <alpha-value>)',
                accent: 'hsl(var(--accent) / <alpha-value>)',
                'accent-hover': 'hsl(var(--accent-hover) / <alpha-value>)',
                danger: 'hsl(var(--danger) / <alpha-value>)',
                success: 'hsl(var(--success) / <alpha-value>)',
                warning: 'hsl(var(--warning) / <alpha-value>)',
                info: 'hsl(var(--info) / <alpha-value>)',

                // Status Colors
                'status-pending': colors.amber[400],
                'status-confirmed': colors.emerald[400],
                'status-completed': colors.blue[400],
                'status-cancelled': colors.red[400],
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};
