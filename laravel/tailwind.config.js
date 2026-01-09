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
        'text-black', 'text-white',
        'text-gray-500', 'text-gray-700','text-gray-800',
        'text-red-500', 'text-red-700','text-red-800',
        'text-green-500', 'text-green-700','text-green-800',
        'text-blue-500','text-blue-800', 'text-yellow-700',
        'text-indigo-800',
        'text-yellow-800',
        'text-purple-800',
        'text-teal-800',

        // Фоны
        'bg-white',
        'bg-gray-50', 'bg-gray-100', 'bg-gray-200', 'bg-gray-300', 'bg-gray-400', 'bg-gray-600', 'bg-gray-800',
        'bg-red-50','bg-red-100', 'bg-red-200', 'bg-red-300', 'bg-red-500', 'bg-red-600',
        'bg-green-50', 'bg-green-100', 'bg-green-200', 'bg-green-300', 'bg-green-500', 'bg-green-600',
        'bg-blue-50', 'bg-blue-100', 'bg-blue-200', 'bg-blue-300', 'bg-blue-500', 'bg-blue-600',
        'bg-yellow-50','bg-yellow-100', 'bg-yellow-200', 'bg-yellow-300', 'bg-yellow-500', 'bg-yellow-600',
        'bg-purple-100',
        'bg-indigo-100',
        'bg-teal-100',
        'bg-black',

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
        'w-auto', 'w-fit', 'w-full',

        'w-8', 'w-12', 'w-16', 'w-20', 'w-24', 'w-28',
        'w-32', 'w-36', 'w-40', 'w-48', 'w-56',
        'w-64', 'w-72', 'w-80', 'w-96',
        'h-8', 'h-10', 'h-12', 'h-16',
        'h-20', 'h-24', 'h-32',

        // Бордеры и закругления
        'border', 'border-gray-300', 'border-gray-400',
        'rounded', 'rounded-md', 'rounded-lg', 'border-green-400', 'border-yellow-400',
        'border-red-400',

        // Состояния
        'hover:bg-gray-200', 'hover:bg-gray-700', 'hover:bg-indigo-500',
        'focus:ring', 'focus:ring-indigo-500','hover:bg-green-600', 'hover:bg-red-600',

        'fixed', 'bottom-0', 'left-0', 'right-0',
        'bg-gray-100', 'border-t',
        'px-6',
        'justify-between', 'items-center',
        'pb-24', 'pb-32',
        // Sticky header + overflow
        'h-[70vh]', 'min-w-[60px]', 'min-w-[80px]', 'min-w-[100px]', 'min-w-[120px]', 'min-w-[200px]',
        'table-fixed', 'border-separate',
        'sticky', 'top-0', 'z-10', 'shadow-sm', 'shadow-md',
        'overflow-x-auto', 'overflow-y-auto',
        // Текстовые утилиты для обрезки
        'truncate', 'whitespace-nowrap', 'overflow-hidden',
        // Выравнивание текста
        'text-left', 'text-center', 'text-right'
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
