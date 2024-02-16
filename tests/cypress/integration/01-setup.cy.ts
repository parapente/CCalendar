describe("Setup", () => {
    before(() => {
        cy.refreshDatabase();
        cy.seed();
    });

    it("shows the starting page", () => {
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
        cy.cas_login("tstteacher", "password");
        cy.visit("/calendar");
        cy.contains("Ημερολόγιο");
        cy.contains("Φίλτρα");
        cy.contains("Εκτύπωση");
    });
});
