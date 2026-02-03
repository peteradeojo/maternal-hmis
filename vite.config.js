import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    base: '/build/',
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/app.scss",
                "public/css/auth.css",
                "resources/js/app.js",
                "resources/js/util.js",
            ],
            refresh: true,
        }),
    ],
});
