/// <reference types="cypress" />
// ***********************************************
// This example commands.ts shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })
//
// declare global {
//   namespace Cypress {
//     interface Chainable {
//       login(email: string, password: string): Chainable<void>
//       drag(subject: string, options?: Partial<TypeOptions>): Chainable<Element>
//       dismiss(subject: string, options?: Partial<TypeOptions>): Chainable<Element>
//       visit(originalFn: CommandOriginalFn, url: string, options: Partial<VisitOptions>): Chainable<Element>
//     }
//   }
// }

Cypress.Commands.add("cas_login", (username: string, password: string) => {
    cy.session(
        { username, password },
        () => {
            cy.visit("/");
            cy.get("a[test-data-id='login']").click();

            cy.origin(
                "https://cas:8443",
                // @ts-ignore
                { args: { username, password } },
                ({ username, password }): void => {
                    cy.get("input[name=username]").type(username);
                    cy.get("input[name=password]").type(password);
                    cy.get("button[type=submit]").click();
                }
            );
        },
        {
            validate() {
                cy.request({
                    url: "/calendar",
                    followRedirect: false,
                })
                    .its("status")
                    .should("eq", 200);
            },
            cacheAcrossSpecs: true,
        }
    );
});

Cypress.Commands.add("admin_login", (username?: string, password?: string) => {
    const admin_username = username ?? "admin";
    const admin_password = password ?? "password";
    cy.session(
        { admin_username, admin_password },
        () => {
            cy.visit("/administrator/login");
            cy.get("#username").type(admin_username);
            cy.get("#password").type(admin_password);
            cy.get("[test-data-id='login-submit']").click().wait(2000);
            cy.location("pathname").should("equal", "/administrator");
        },
        {
            validate() {
                cy.request({
                    url: "/administrator",
                    followRedirect: false,
                })
                    .its("status")
                    .should("eq", 200);
            },
            cacheAcrossSpecs: true,
        }
    );
});
