import { only } from "node:test";

describe("Setup", () => {
    before(() => {
        cy.refreshDatabase();
        cy.seed();
    });

    only("shows the starting page", () => {
        cy.visit("/");

        cy.contains("Καλωσορίσατε");
    });

    it("shows the login page", () => {
        cy.session("login", () => {
            cy.visit("/calendar");
            cy.contains("cas");
        });
    });

    it("can login through cas", () => {
        cy.seed("TestDataSeeder");
        // cy.cas_login("tstteacher", "password");
        cy.contains("Ημερολόγιο");
        cy.contains("Φίλτρα");
        cy.contains("Εκτύπωση");
    });
});
