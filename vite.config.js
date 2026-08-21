import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import inertia from "@inertiajs/vite";
import react from '@vitejs/plugin-react';
import path from "path";

export default defineConfig({
    base: "/build/",
    plugins: [
        laravel({
            input: [
                // "public/css/auth.css",
                "resources/css/app.css",
                "resources/css/app.scss",
                "resources/js/app.js",
                "resources/js/util.js",
                "resources/js/inertia.jsx",
            ],
            refresh: true,
        }),
        react(),
    ],
    resolve: {
        alias: {
            "@": path.resolve(import.meta.dirname, "resources/js"),
        },
    },
});
