import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/util.js",
                "resources/js/records/patients.js",
                "resources/css/app.scss",
                "public/css/auth.css",
            ],
            refresh: true,
        }),
    ],
});
