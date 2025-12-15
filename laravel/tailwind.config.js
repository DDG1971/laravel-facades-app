import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './modules/**/*.blade.php',        // если есть кастомные модули
        './resources/views/**/**/*.blade.php', // если есть вложенные подпапки
    ],
    safelist: [
        'text-red-500',
        'grid',
        'grid-cols-1',
        'grid-cols-4',
        'gap-4',
        'mb-4',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
