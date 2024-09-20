/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./public/**/*.{css,scss}",
        "./resources/**/*.blade.php",
        "./resources/**/*.{css,scss}",
    ],
    theme: {
        extend: {},
    },
    plugins: [require("@tailwindcss/forms")],
};
