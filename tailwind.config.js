import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#feee00',
                secondary: '#424141',
            },
            fontFamily: {
                sans:     ['Inter', ...defaultTheme.fontFamily.sans],
                inter:    ['Inter', ...defaultTheme.fontFamily.sans],
                playfair: ['"Playfair Display"', 'Georgia', 'serif'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
    ],
};
