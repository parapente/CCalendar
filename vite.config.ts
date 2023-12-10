import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path";
import { exit } from "process";

export default defineConfig(({ mode }) => {
    process.env = { ...process.env, ...loadEnv(mode, process.cwd(), "") };

    // Εύρεση resource path από το APP_URL
    let resources_dir;
    let index = 0;
    let app_url = process.env.APP_URL;

    if (typeof app_url === "undefined") {
        console.error("APP_URL is not defined! Aborting...");
        exit(2);
    }

    index = app_url.indexOf("//");
    if (index === -1) {
        console.log("Σφάλμα με τον ορισμό του APP_URL στο .env!");
        process.exit(1);
    }
    index = app_url.indexOf("/", index + 2);
    if (index === -1) {
        resources_dir = "/";
    } else {
        resources_dir = app_url.substring(index);
        if (!resources_dir.endsWith("/")) {
            resources_dir += "/";
        }
    }

    console.log("==", resources_dir);
    process.env.ASSET_URL = resources_dir;

    return {
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
                ziggy: path.resolve(__dirname, "vendor/tightenco/ziggy/src/js"),
            },
        },
    };
});
