describe("Admin", () => {
    beforeEach(() => {
        cy.refreshDatabase();
        cy.seed();
        cy.seed("TestDataSeeder");
    });

    it("can login", () => {
        cy.admin_login();
        cy.visit("/administrator");
        cy.contains("Αρχική σελίδα");
    });

    it("can logout", () => {
        cy.admin_login();
        cy.visit("/administrator");
        cy.get("[test-data-id='admin-menu']").click();
        cy.get("[test-data-id='admin-logout']").click();
        cy.location("pathname").should("equal", "/");
    });

    it("can create a new admin user", () => {
        cy.admin_login();
        cy.visit("/administrator");
        cy.get("[test-data-id='user-link']").click();
        cy.contains("Διαχείριση Χρηστών Εφαρμογής");
        cy.get("[test-data-id='create-user-button']").click();
        cy.location("pathname").should("equal", "/administrator/user/create");
        cy.get("#type").select("admin");
        cy.get("#name").type("Test Admin User");
        cy.get("#username").type("test_admin_user");
        cy.get("#password").type("<PASSWORD>");
        cy.get("#password_confirmation").type("<PASSWORD>");
        cy.get("[test-data-id='submit-button']").click();
        cy.location("pathname").should("equal", "/administrator/user");
        cy.contains("Test Admin User");

        // Κάνε αποσύνδεση και σύνδεση ως ο νέος χρήστης
        cy.get("[test-data-id='admin-menu']").click();
        cy.get("[test-data-id='admin-logout']").click();
        cy.admin_login("test_admin_user", "<PASSWORD>");
        cy.visit("/administrator");
        cy.contains("Αρχική σελίδα");
    });

    it("can create a new cas user", () => {
        cy.admin_login();
        cy.visit("/administrator");
        cy.get("[test-data-id='user-link']").click();
        cy.contains("Διαχείριση Χρηστών Εφαρμογής");
        cy.get("[test-data-id='create-user-button']").click();
        cy.location("pathname").should("equal", "/administrator/user/create");
        cy.get("#type").select("cas");
        cy.get("#name").type("Test Cas User");
        cy.get("#username").type("test_cas_user");
        cy.get("#employee_number").type("222222");
        cy.get("#role").select("User");
        cy.get("[test-data-id='submit-button']").click();
        cy.location("pathname").should("equal", "/administrator/user");
        cy.contains("Test Cas User");

        // Τώρα δοκίμασε να συνδεθείς
        cy.get("[test-data-id='admin-menu']").click();
        cy.get("[test-data-id='admin-logout']").click();
        cy.cas_login("tstteacher3", "password");
        cy.visit("/calendar");
        cy.contains("Ημερολόγιο");
    });

    it("can create a new cas supervisor user", () => {
        cy.admin_login();
        cy.visit("/administrator");
        cy.get("[test-data-id='user-link']").click();
        cy.contains("Διαχείριση Χρηστών Εφαρμογής");
        cy.get("[test-data-id='create-user-button']").click();
        cy.location("pathname").should("equal", "/administrator/user/create");
        cy.get("#type").select("cas");
        cy.get("#name").type("Test Cas User");
        cy.get("#username").type("test_cas_user");
        cy.get("#employee_number").type("222222");
        cy.get("#role").select("Supervisor");
        cy.get("[test-data-id='submit-button']").click();
        cy.location("pathname").should("equal", "/administrator/user");
        cy.contains("Test Cas User");

        // Τώρα δοκίμασε να συνδεθείς
        cy.get("[test-data-id='admin-menu']").click();
        cy.get("[test-data-id='admin-logout']").click();
        cy.cas_login("tstteacher3", "password");
        cy.visit("/calendar");
        cy.contains("Ημερολόγιο");
        cy.contains("Φίλτρα");
    });

    it("can edit an existing admin user", () => {
        cy.admin_login();
        cy.php("App\\Models\\User::factory()->create()").then(
            (user: App.Models.User) => {
                cy.visit("/administrator/user");
                cy.contains(`${user.name}`);
                cy.get(
                    `[test-data-id='edit-user-admin-${user.id}-button']`
                ).click();

                // Δοκίμασε πρώτα να αλλάξεις μόνο όνομα χρήστη χωρίς να αλλάξεις
                // τον κωδικό του
                cy.get("#name").clear().type("Test User");
                cy.get("#username").clear().type("test_user");
                cy.get("[test-data-id='submit-button']").click();
                cy.location("pathname").should("equal", "/administrator/user");
                cy.contains("Test User");

                // Άλλαξε μόνο τον κωδικό του χρήστη τώρα
                cy.get(
                    `[test-data-id='edit-user-admin-${user.id}-button']`
                ).click();
                cy.get("#password").type("<PASSWORD>");
                cy.get("#password_confirmation").type("<PASSWORD>");
                cy.get("[test-data-id='submit-button']").click();
                cy.location("pathname").should("equal", "/administrator/user");

                //Δοκίμασε τώρα να συνδεθείς ως ο νέος χρήστης
                cy.get("[test-data-id='admin-menu']").click();
                cy.get("[test-data-id='admin-logout']").click();
                cy.admin_login("test_user", "<PASSWORD>");
                cy.visit("/administrator");
                cy.contains("Αρχική σελίδα");
            }
        );
    });

    it("can create a new calendar", () => {
        cy.admin_login();
        cy.visit("/administrator");
        cy.get("[test-data-id='calendar-link']").click();
        cy.get("[test-data-id='create-calendar-button']").click();
        cy.get("#name").type("Test Calendar");
        // TODO: επέλεξε χρώμα ημερολογίου
        cy.get("#active").check();
        cy.get("[test-data-id='submit-button']").click();
        cy.location("pathname").should("equal", "/administrator/calendar");
        cy.contains("Test Calendar");
    });

    it("can edit a calendar", () => {
        cy.admin_login();
        cy.php("App\\Models\\Calendar::factory()->create()").then(
            (calendar: App.Models.Calendar) => {
                cy.visit("/administrator/calendar");
                cy.get(
                    `[test-data-id='edit-calendar-${calendar.id}-button']`
                ).click();
                cy.get("#name").clear().type("Test Calendar");
                // TODO: επέλεξε χρώμα ημερολογίου
                cy.get("#active").uncheck();
                cy.get("[test-data-id='submit-button']").click();
                cy.location("pathname").should(
                    "equal",
                    "/administrator/calendar"
                );
                cy.contains("Test Calendar");
                cy.php(
                    `App\\Models\\Calendar::where("name", "Test Calendar")
                    ->where("active", false)
                    ->count()`
                ).then((count: number) => {
                    expect(count).to.be.equal(1);
                });
            }
        );
    });

    it("can toggle the active status of a calendar", () => {
        cy.admin_login();
        cy.php(
            "App\\Models\\Calendar::factory()->create(['active' => true])"
        ).then((calendar: App.Models.Calendar) => {
            cy.php(
                `App\\Models\\Calendar::where("name", '${calendar.name}')
                    ->where("active", true)
                    ->count()`
            ).then((count: number) => {
                expect(count).to.be.equal(1);
            });

            cy.visit("/administrator/calendar");
            cy.get(
                `[test-data-id='toggle-calendar-${calendar.id}-button']`
            ).click();
            cy.location("pathname").should("equal", "/administrator/calendar");

            cy.php(
                `App\\Models\\Calendar::where("name", '${calendar.name}')
                    ->where("active", false)
                    ->count()`
            ).then((count: number) => {
                expect(count).to.be.equal(1);
            });

            cy.get(
                `[test-data-id='toggle-calendar-${calendar.id}-button']`
            ).click();
            cy.location("pathname").should("equal", "/administrator/calendar");

            cy.php(
                `App\\Models\\Calendar::where("name", '${calendar.name}')
                    ->where("active", true)
                    ->count()`
            ).then((count: number) => {
                expect(count).to.be.equal(1);
            });
        });
    });

    it("can create a new trimester report", () => {
        const testReport = {
            name: "Test Report",
            type: 1,
            options: {
                from: "2021-01-01",
                to: "2021-04-01",
            },
        };

        cy.admin_login();
        cy.visit("/administrator");
        cy.get("[test-data-id='report-link']").click();
        cy.get("[test-data-id='create-report-button']").click();
        cy.contains("Δημιουργία νέας αναφοράς");
        cy.get("#name").type(testReport.name);
        cy.get("#from").type(testReport.options.from);
        cy.get("#to").type(testReport.options.to);
        cy.get("[test-data-id='report-save-button']").click();
        cy.location("pathname").should("equal", "/administrator/report");
        cy.php(
            `App\\Models\\Report::where('name', '${testReport.name}')
            ->where('type', '${testReport.type}')
            ->where('options', '${JSON.stringify(testReport.options)}')
            ->count()`
        ).should("equal", 1);
    });

    it("can edit an existing trimester report", () => {
        const newReport = {
            name: "Test Report",
            type: 1,
            options: {
                from: "2021-01-01",
                to: "2021-04-01",
            },
        };

        cy.php("App\\Models\\Report::factory()->create()")
            .then((report: App.Models.Report) => {
                return cy
                    .php(
                        "App\\Models\\CasUser::where('employee_number', '999999')->first()"
                    )
                    .then((cas_user: App.Models.CasUser) => {
                        return [report, cas_user];
                    });
            })
            .then(([report, cas_user]) => {
                cy.php(`App\\Models\\ReportData::factory()->create([
                    'cas_user_id' => ${cas_user.id},
                    'report_id' => ${report.id},
                    'data' => '{"filename":"abcd.txt", "real_filename":"abcd.txt"}',
                ])`);
                cy.admin_login();
                cy.visit("/administrator/report");
                cy.get(
                    `[test-data-id='report-${report.id}'] [test-data-id='edit-report-button']`
                ).click();
                cy.get("#name").clear().type(newReport.name);
                cy.get("#from").clear().type(newReport.options.from);
                cy.get("#to").clear().type(newReport.options.to);
                cy.get("[test-data-id='report-save-button']").click();
                cy.php(
                    `App\\Models\\Report::where('name', '${newReport.name}')
                    ->where('type', '${newReport.type}')
                    ->where('options', '${JSON.stringify(newReport.options)}')
                    ->count()`
                ).should("equal", 1);
            });
    });

    it("can toggle the visibility of a trimester report", () => {
        cy.php(
            "App\\Models\\Report::factory()->create(['active' => true])"
        ).then((report: App.Models.Report) => {
            cy.admin_login();
            cy.visit("/administrator/report");
            cy.get(
                `[test-data-id='report-${report.id}'] [test-data-id='toggle-active-button']`
            ).click();
            cy.location("pathname").should("equal", "/administrator/report");
            cy.php(
                `App\\Models\\Report::where('name', '${report.name}')
                ->where('type', '${report.type}')
                ->where('active', false)
                ->count()`
            ).should("equal", 1);
            cy.get(
                `[test-data-id='report-${report.id}'] [test-data-id='toggle-active-button']`
            ).click();
            cy.location("pathname").should("equal", "/administrator/report");
            cy.php(
                `App\\Models\\Report::where('name', '${report.name}')
                ->where('type', '${report.type}')
                ->where('active', true)
                ->count()`
            ).should("equal", 1);
        });
    });

    it.only("can see trimester report uploads", () => {
        cy.php("App\\Models\\Report::factory()->create()")
            .then((report: App.Models.Report) => {
                return cy
                    .php(
                        "App\\Models\\CasUser::where('employee_number', '999999')->first()"
                    )
                    .then((cas_user: App.Models.CasUser) => {
                        return [report, cas_user];
                    });
            })
            .then(([report, cas_user]) => {
                cy.php(`App\\Models\\ReportData::factory()->create([
                    'cas_user_id' => ${cas_user.id},
                    'report_id' => ${report.id},
                    'data' => '{"filename":"abcd.txt", "real_filename":"abcd.txt"}',
                ])`);
                cy.admin_login();
                cy.visit("/administrator/report");
                cy.get(
                    `[test-data-id='report-${report.id}'] [test-data-id='show-report-button']`
                ).click();
                cy.contains(cas_user.name);
                cy.contains("abcd.txt");
            });
    });
});
