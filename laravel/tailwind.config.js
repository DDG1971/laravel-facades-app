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
        // Цвета текста
        'text-gray-500', 'text-gray-700', 'text-black', 'text-white',
        'text-red-500', 'text-green-500', 'text-blue-500',

        // Фоны
        'bg-white', 'bg-gray-50', 'bg-gray-100', 'bg-gray-200',
        'bg-red-200', 'bg-green-200', 'bg-blue-200', 'bg-yellow-200',
        'bg-green-500', 'bg-green-600',
        'bg-red-500', 'bg-red-600',
        'bg-blue-500', 'bg-blue-600',
        'bg-yellow-500', 'bg-yellow-600',
        'hover:bg-green-600', 'hover:bg-red-600',

        // Сетка
        'grid', 'grid-cols-1', 'grid-cols-2', 'grid-cols-3', 'grid-cols-4',
        'gap-0', 'gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-6',

        // Flex helpers
        'flex', 'items-center', 'items-start', 'items-end',
        'justify-center', 'justify-between', 'justify-start', 'justify-end',

        // Отступы
        'p-2', 'p-4', 'px-2', 'px-3', 'px-4', 'py-2', 'py-3', 'py-4',
        'mb-0', 'mb-1', 'mb-2', 'mb-3', 'mb-4', 'mb-6',
        'mt-0', 'mt-2', 'mt-4',

        // Width / Height
        'w-auto', 'w-fit', 'w-full', 'w-16', 'w-32', 'w-48', 'w-64',
        'h-10', 'h-12', 'h-16', 'h-32',

        // Бордеры и закругления
        'border', 'border-gray-300', 'border-gray-400',
        'rounded', 'rounded-md', 'rounded-lg',

        // Состояния
        'hover:bg-gray-200', 'hover:bg-indigo-500',
        'focus:ring', 'focus:ring-indigo-500',

        'fixed', 'bottom-0', 'left-0', 'right-0',
        'bg-gray-100', 'border-t',
        'px-6',
        'justify-between', 'items-center',
        'pb-24', 'pb-32'
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
