/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./public/**/*.{css,scss}",
        "./resources/**/*.blade.php",
        "./resources/**/*.{css,scss}",
    ],
    theme: {
        extend: {
            colors: {
                'primary': '#3fbbc0',
            },
        },
    },
    plugins: [require("@tailwindcss/forms")],
};
