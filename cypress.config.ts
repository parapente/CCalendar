import { defineConfig } from "cypress";
import plugins from "./tests/cypress/plugins";

export default defineConfig({
    component: {
        devServer: {
            framework: "vue",
            bundler: "vite",
        },
        supportFolder: "tests/cypress/support",
        supportFile: "tests/cypress/support/index.ts",
        videosFolder: "tests/cypress/videos",
        screenshotsFolder: "tests/cypress/screenshots",
        fixturesFolder: "tests/cypress/fixture",
    },

    e2e: {
        setupNodeEvents(on, config) {
            // implement node event listeners here
            plugins(on, config);
        },
        specPattern: "tests/cypress/integration/**/*.cy.{js,jsx,ts,tsx}",
        supportFile: "tests/cypress/support/index.ts",
        chromeWebSecurity: false,
        responseTimeout: 100000,
    },
});
