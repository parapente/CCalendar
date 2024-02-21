/// <reference types="cypress" />

type RouteObject = {
    name: string;
    domain: string | null;
    action: string;
    uri: string;
    method: Array<"GET" | "HEAD" | "POST" | "PUT" | "PATCH" | "DELETE">;
};

type CreateModelType = {
    model: string;
    state: Array<string>;
    attributes: Array<string>;
    load: Array<string>;
    count: number;
};

declare namespace Cypress {
    interface Chainable<Subject> {
        /**
         * Log in the user with the given attributes, or create a new user and then log them in.
         *
         * @example
         * cy.login()
         * cy.login({ id: 1 })
         */
        login(attributes?: Record<string, unknown>): Chainable<any>;

        /**
         * Log out the current user.
         *
         * @example
         * cy.logout()
         */
        logout(): Chainable<any>;

        /**
         * Fetch the currently authenticated user.
         *
         * @example
         * cy.currentUser()
         */
        currentUser(): Chainable<any>;

        /**
         * Fetch a CSRF token from the server.
         *
         * @example
         * cy.logout()
         */
        csrfToken(): Chainable<any>;

        /**
         * Fetch a fresh list of URI routes from the server.
         *
         * @example
         * cy.logout()
         */
        refreshRoutes(): Chainable<any>;

        /**
         * Create and persist a new Eloquent record using Laravel model factories.
         *
         * @example
         * cy.create('App\\User');
         * cy.create('App\\User', 2);
         * cy.create('App\\User', 2, { active: false });
         * cy.create({ model: 'App\\User', state: ['guest'], relations: ['profile'], count: 2 }
         */
        create(
            model: string | CreateModelType,
            count?,
            attributes?,
            load?,
            state?
        ): Chainable<any>;

        /**
         * Refresh the database state using Laravel's migrate:fresh command.
         *
         * @example
         * cy.refreshDatabase()
         * cy.refreshDatabase({ '--drop-views': true }
         */
        refreshDatabase(options?: Record<string, unknown>): Chainable<any>;

        /**
         * Run Artisan's db:seed command.
         *
         * @example
         * cy.seed()
         * cy.seed('PlansTableSeeder')
         */
        seed(seederClass?: string): Chainable<any>;

        /**
         * Run an Artisan command.
         *
         * @example
         * cy.artisan()
         */
        artisan(
            command: string,
            parameters?: Record<string, unknown>,
            options?: Record<string, unknown>
        ): Chainable<any>;

        /**
         * Execute arbitrary PHP on the server.
         *
         * @example
         * cy.php('2 + 2')
         * cy.php('App\\User::count()')
         */
        php(command: string): Chainable<any>;

        cas_login(username: string, password: string): Chainable<void>;
        admin_login(username?: string, password?: string): Chainable<void>;
    }

    interface Cypress {
        Laravel: {
            currentUser?: Cypress.RequestBody | undefined;
            routes: Record<string, RouteObject>;
            route: (
                name: string,
                parameters: Record<string, unknown>
            ) => string;
        };
    }
}
