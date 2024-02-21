describe("Admin", () => {
    beforeEach(() => {
        cy.refreshDatabase();
        cy.seed();
        cy.seed("TestDataSeeder");
    });

    it("can login", () => {
        cy.admin_login();
    });

    it("can logout", () => {
        cy.admin_login();
        cy.visit("/administrator");
        cy.get("[test-data-id='admin-menu']").click();
        cy.get("[test-data-id='admin-logout']").click();
        cy.location("pathname").should("equal", "/");
    });
});
