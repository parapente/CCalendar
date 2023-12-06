import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: "resources/ts/app.ts",
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/ts/"),
            "ziggy-js": path.resolve(
                __dirname,
                "vendor/tightenco/ziggy/dist/index.es"
            ),
        },
    },
});
