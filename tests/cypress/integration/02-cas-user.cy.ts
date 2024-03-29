import { DateTime } from "luxon";
import * as fs from "fs";

describe("CAS User", () => {
    beforeEach(() => {
        cy.refreshDatabase();
        cy.seed();
        cy.seed("TestDataSeeder");
    });

    it("can login through cas", () => {
        cy.cas_login("tstteacher2", "password");
        cy.visit("/calendar");

        // Κάνε έλεγχο ότι υπάρχουν όλα τα στοιχεία
        cy.get("[test-data-id='calendar-filters']").should("not.exist");
        cy.contains("Εκτύπωση");
        cy.contains("Ημερολόγια");
        cy.get("[test-data-id='calendar-legend']").should("exist");
        cy.get("[test-data-id='add-event-button']").should("exist");
        // Δεν είναι απαραίτητο ότι θα υπάρχει αν δεν έχουμε καμία εκδήλωση
        // μέσα στο μήνα.
        // cy.get("[test-data-id='calendar-event-list']").should("exist");
    });

    it("can logout through cas", () => {
        cy.cas_login("tstteacher2", "password");
        cy.visit("/calendar");
        cy.get("[test-data-id='user-menu']").click();
        cy.get("[test-data-id='user-logout']").click();
        cy.origin("https://cas:8443", () => {
            cy.contains("Logout successful");
        });
    });

    it("can create a new event", () => {
        let testEvent = {
            title: "Test Event",
            description: "This is a test event.",
            start_date: "2021-01-01T10:00",
            end_date: "2021-01-02T10:00",
            location: "Thessaloniki, Greece",
            url: "https://www.example.com",
        };

        cy.cas_login("tstteacher2", "password");
        cy.visit("/calendar");
        cy.get("[test-data-id='add-event-button']").click();
        cy.get("[test-data-id='calendar-event-form'] > div").should(
            "include.text",
            "Νέα εκδήλωση"
        );
        cy.get("[test-data-id='calendar-event-form'] [name='title']").type(
            testEvent.title
        );
        cy.get(
            "[test-data-id='calendar-event-form'] [name='description']"
        ).type(testEvent.description);
        cy.get("[test-data-id='calendar-event-form'] [name='start_date']").type(
            testEvent.start_date
        );
        cy.get("[test-data-id='calendar-event-form'] [name='end_date']").type(
            testEvent.end_date
        );
        cy.get("[test-data-id='calendar-event-form'] [name='location']").type(
            testEvent.location
        );
        cy.get("[test-data-id='calendar-event-form'] [name='url']").type(
            testEvent.url
        );
        cy.get(
            "[test-data-id='calendar-event-form'] [test-data-id='button-save']"
        ).click();
        cy.php(
            `App\\Models\\CalendarEvent::where('title', '${testEvent.title}')
            ->where('description', '${testEvent.description}')
            ->where('start_date', '${testEvent.start_date}')
            ->where('end_date', '${testEvent.end_date}')
            ->where('location', '${testEvent.location}')
            ->where('url', '${testEvent.url}')
            ->count()`
        ).should("equal", 1);
    });

    it("can edit an event", () => {
        const testEvent = {
            title: "Test Event",
            description: "This is a test event.",
            start_date: DateTime.local().startOf("minute").toISO({
                includeOffset: false,
            }),
            end_date: DateTime.local()
                .plus({ days: 1 })
                .startOf("minute")
                .toISO({
                    includeOffset: false,
                }),
            location: "Thessaloniki, Greece",
            url: "https://www.example.com",
        };

        const editTestEvent = {
            title: "Test Event edited",
            description: "This is an edited test event.",
            start_date: DateTime.local()
                .startOf("minute")
                .plus({ days: 2 })
                .toISO({
                    includeOffset: false,
                }),
            end_date: DateTime.local()
                .startOf("minute")
                .plus({ days: 3 })
                .toISO({
                    includeOffset: false,
                }),
            location: "Athens, Greece",
            url: "https://www.example.gr",
        };

        cy.php(
            "App\\Models\\CasUser::where('employee_number', '999999')->first()"
        )
            .then((cas_user) => {
                return cy
                    .create("App\\Models\\Calendar", {
                        active: true,
                    })
                    .then((calendar) => {
                        return cy.create("App\\Models\\CalendarEvent", {
                            ...testEvent,
                            cas_user_id: cas_user.id,
                            calendar_id: calendar.id,
                        });
                    });
            })
            .then((calendar_event) => {
                cy.cas_login("tstteacher2", "password");
                cy.visit("/calendar");
                cy.get(
                    `[test-data-id='event-card-${calendar_event.id}'] [test-data-id='event-card-edit-button']`
                ).click();
                cy.get("[test-data-id='calendar-event-form'] > div").should(
                    "include.text",
                    "Επεξεργασία εκδήλωσης"
                );
                cy.get("[test-data-id='calendar-event-form'] [name='title']")
                    .clear()
                    .type(editTestEvent.title);
                cy.get(
                    "[test-data-id='calendar-event-form'] [name='description']"
                )
                    .clear()
                    .type(editTestEvent.description);
                cy.get(
                    "[test-data-id='calendar-event-form'] [name='start_date']"
                )
                    .clear()
                    .type(editTestEvent.start_date);
                cy.get("[test-data-id='calendar-event-form'] [name='end_date']")
                    .clear()
                    .type(editTestEvent.end_date);
                cy.get("[test-data-id='calendar-event-form'] [name='location']")
                    .clear()
                    .type(editTestEvent.location);
                cy.get("[test-data-id='calendar-event-form'] [name='url']")
                    .clear()
                    .type(editTestEvent.url);
                cy.get(
                    "[test-data-id='calendar-event-form'] [test-data-id='button-save']"
                ).click();
                cy.php(
                    `App\\Models\\CalendarEvent::where('title', '${editTestEvent.title}')
                    ->where('description', '${editTestEvent.description}')
                    ->where('start_date', '${editTestEvent.start_date}')
                    ->where('end_date', '${editTestEvent.end_date}')
                    ->where('location', '${editTestEvent.location}')
                    ->where('url', '${editTestEvent.url}')
                    ->count()`
                ).should("equal", 1);
            });
    });

    it("can cancel editing an event", () => {
        const testEvent = {
            title: "Test Event",
            description: "This is a test event.",
            start_date: DateTime.local().startOf("minute").toISO({
                includeOffset: false,
            }),
            end_date: DateTime.local()
                .plus({ days: 1 })
                .startOf("minute")
                .toISO({
                    includeOffset: false,
                }),
            location: "Thessaloniki, Greece",
            url: "https://www.example.com",
        };

        const editTestEvent = {
            title: "Test Event edited",
            description: "This is an edited test event.",
            start_date: DateTime.local()
                .startOf("minute")
                .plus({ days: 2 })
                .toISO({
                    includeOffset: false,
                }),
            end_date: DateTime.local()
                .startOf("minute")
                .plus({ days: 3 })
                .toISO({
                    includeOffset: false,
                }),
            location: "Athens, Greece",
            url: "https://www.example.gr",
        };

        cy.php(
            "App\\Models\\CasUser::where('employee_number', '999999')->first()"
        )
            .then((cas_user) => {
                return cy
                    .create("App\\Models\\Calendar", {
                        active: true,
                    })
                    .then((calendar) => {
                        return cy.create("App\\Models\\CalendarEvent", {
                            ...testEvent,
                            cas_user_id: cas_user.id,
                            calendar_id: calendar.id,
                        });
                    });
            })
            .then((calendar_event) => {
                cy.cas_login("tstteacher2", "password");
                cy.visit("/calendar");
                cy.get(
                    `[test-data-id='event-card-${calendar_event.id}'] [test-data-id='event-card-edit-button']`
                ).click();
                cy.get("[test-data-id='calendar-event-form'] > div").should(
                    "include.text",
                    "Επεξεργασία εκδήλωσης"
                );
                cy.get("[test-data-id='calendar-event-form'] [name='title']")
                    .clear()
                    .type(editTestEvent.title);
                cy.get(
                    "[test-data-id='calendar-event-form'] [name='description']"
                )
                    .clear()
                    .type(editTestEvent.description);
                cy.get(
                    "[test-data-id='calendar-event-form'] [name='start_date']"
                )
                    .clear()
                    .type(editTestEvent.start_date);
                cy.get("[test-data-id='calendar-event-form'] [name='end_date']")
                    .clear()
                    .type(editTestEvent.end_date);
                cy.get("[test-data-id='calendar-event-form'] [name='location']")
                    .clear()
                    .type(editTestEvent.location);
                cy.get("[test-data-id='calendar-event-form'] [name='url']")
                    .clear()
                    .type(editTestEvent.url);
                cy.get(
                    "[test-data-id='calendar-event-form'] [test-data-id='button-cancel']"
                ).click();
                cy.php(
                    `App\\Models\\CalendarEvent::where('title', '${testEvent.title}')
                    ->where('description', '${testEvent.description}')
                    ->where('start_date', '${testEvent.start_date}')
                    ->where('end_date', '${testEvent.end_date}')
                    ->where('location', '${testEvent.location}')
                    ->where('url', '${testEvent.url}')
                    ->count()`
                ).should("equal", 1);
                cy.php(
                    `App\\Models\\CalendarEvent::where('title', '${editTestEvent.title}')
                    ->where('description', '${editTestEvent.description}')
                    ->where('start_date', '${editTestEvent.start_date}')
                    ->where('end_date', '${editTestEvent.end_date}')
                    ->where('location', '${editTestEvent.location}')
                    ->where('url', '${editTestEvent.url}')
                    ->count()`
                ).should("equal", 0);
            });
    });

    it("can delete an event", () => {
        const testEvent = {
            title: "Test Event",
            description: "This is a test event.",
            start_date: DateTime.local().startOf("minute").toISO({
                includeOffset: false,
            }),
            end_date: DateTime.local()
                .plus({ days: 1 })
                .startOf("minute")
                .toISO({
                    includeOffset: false,
                }),
            location: "Thessaloniki, Greece",
            url: "https://www.example.com",
        };

        cy.php(
            "App\\Models\\CasUser::where('employee_number', '999999')->first()"
        )
            .then((cas_user: App.Models.CasUser) => {
                return cy
                    .create("App\\Models\\Calendar", {
                        active: true,
                    })
                    .then((calendar: App.Models.Calendar) => {
                        return cy.create("App\\Models\\CalendarEvent", {
                            ...testEvent,
                            cas_user_id: cas_user.id,
                            calendar_id: calendar.id,
                        });
                    });
            })
            .then((calendar_event: App.Models.CalendarEvent) => {
                cy.cas_login("tstteacher2", "password");
                cy.visit("/calendar");
                cy.get(
                    `[test-data-id='event-card-${calendar_event.id}'] [test-data-id='event-card-delete-button']`
                ).click();
                cy.get("[test-data-id='confirm-delete-yes-button']").click();
                cy.php(
                    `App\\Models\\CalendarEvent::where('title', '${testEvent.title}')
                    ->where('description', '${testEvent.description}')
                    ->where('start_date', '${testEvent.start_date}')
                    ->where('end_date', '${testEvent.end_date}')
                    ->where('location', '${testEvent.location}')
                    ->where('url', '${testEvent.url}')
                    ->count()`
                ).should("equal", 0);
            });
    });

    it("can cancel an event deletion", () => {
        const testEvent = {
            title: "Test Event",
            description: "This is a test event.",
            start_date: DateTime.local().startOf("minute").toISO({
                includeOffset: false,
            }),
            end_date: DateTime.local()
                .plus({ days: 1 })
                .startOf("minute")
                .toISO({
                    includeOffset: false,
                }),
            location: "Thessaloniki, Greece",
            url: "https://www.example.com",
        };

        cy.php(
            "App\\Models\\CasUser::where('employee_number', '999999')->first()"
        )
            .then((cas_user: App.Models.CasUser) => {
                return cy
                    .create("App\\Models\\Calendar", {
                        active: true,
                    })
                    .then((calendar) => {
                        return cy.create("App\\Models\\CalendarEvent", {
                            ...testEvent,
                            cas_user_id: cas_user.id,
                            calendar_id: calendar.id,
                        });
                    });
            })
            .then((calendar_event: App.Models.CalendarEvent) => {
                cy.cas_login("tstteacher2", "password");
                cy.visit("/calendar");
                cy.get(
                    `[test-data-id='event-card-${calendar_event.id}'] [test-data-id='event-card-delete-button']`
                ).click();
                cy.get("[test-data-id='confirm-delete-no-button']").click();
                cy.php(
                    `App\\Models\\CalendarEvent::where('title', '${testEvent.title}')
                    ->where('description', '${testEvent.description}')
                    ->where('start_date', '${testEvent.start_date}')
                    ->where('end_date', '${testEvent.end_date}')
                    ->where('location', '${testEvent.location}')
                    ->where('url', '${testEvent.url}')
                    ->count()`
                ).should("equal", 1);
            });
    });

    it("can fill a trimester report", () => {
        cy.php("App\\Models\\Report::factory()->create()").then(
            (report: App.Models.Report) => {
                cy.cas_login("tstteacher2", "password");
                cy.visit("/report");
                cy.get(
                    `[test-data-id='report-${report.id}'] [test-data-id='show-report-button']`
                ).click();
                cy.contains(report.name);

                // Έλεγξε αν μπορείς να κατεβάσεις το ημερολόγιο του τριμήνου σε docx
                cy.deleteDownloadsFolder();
                cy.get("[test-data-id='report-calendar-to-word']").click();
                cy.verifyDownload(".docx", { contains: true });

                // Ανέβασε την αναφορά
                cy.get("input[name='file']").selectFile({
                    contents: Cypress.Buffer.from("file contents"),
                    fileName: "file.txt",
                    mimeType: "text/plain",
                    lastModified: Date.now(),
                });
                cy.get("[test-data-id='report-upload-button']").click();
                cy.location("pathname").should("eq", "/report");
                cy.php("App\\Models\\ReportData::all()->count()").should(
                    "equal",
                    1
                );

                // Έλεγξε αν μπορείς να κατεβάσεις το ήδη ανεβασμένο αρχείο της αναφοράς
                cy.get(
                    `[test-data-id='report-${report.id}'] [test-data-id='show-report-button']`
                ).click();
                cy.contains(report.name);
                cy.deleteDownloadsFolder();
                cy.get("[test-data-id='report-uploaded-file-link']").click();
                cy.verifyDownload("file.txt");

                // Έλεγξε αν μπορείς να αλλάξεις το αρχείο της αναφοράς
                cy.get("input[name='file']").selectFile({
                    contents: Cypress.Buffer.from("new file contents"),
                    fileName: "file2.txt",
                    mimeType: "text/plain",
                    lastModified: Date.now(),
                });
                cy.get("[test-data-id='report-upload-button']").click();
                cy.location("pathname").should("eq", "/report");
                cy.php("App\\Models\\ReportData::all()->count()").should(
                    "equal",
                    1
                );
                cy.get(
                    `[test-data-id='report-${report.id}'] [test-data-id='show-report-button']`
                ).click();
                cy.contains(report.name);
                cy.deleteDownloadsFolder();
                cy.get("[test-data-id='report-uploaded-file-link']").click();
                cy.verifyDownload("file2.txt");
            }
        );
    });
});
