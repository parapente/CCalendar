import { DateTime } from "luxon";

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
            startDate: "2021-01-01T10:00",
            endDate: "2021-01-02T10:00",
            location: "Thessaloniki, Greece",
            url: "https://www.example.com",
        };

        cy.cas_login("tstteacher2", "password");
        cy.visit("/calendar");
        cy.get("[test-data-id='add-event-button']").click();
        cy.get("[test-data-id='calendar-event-form'] [name='title']").type(
            testEvent.title
        );
        cy.get(
            "[test-data-id='calendar-event-form'] [name='description']"
        ).type(testEvent.description);
        cy.get("[test-data-id='calendar-event-form'] [name='start_date']").type(
            testEvent.startDate
        );
        cy.get("[test-data-id='calendar-event-form'] [name='end_date']").type(
            testEvent.endDate
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
            ->where('start_date', '${testEvent.startDate}')
            ->where('end_date', '${testEvent.endDate}')
            ->where('location', '${testEvent.location}')
            ->where('url', '${testEvent.url}')
            ->count()`
        ).should("equal", 1);
    });

    it.only("can edit an event", () => {
        let testEvent = {
            title: "Test Event",
            description: "This is a test event.",
            start_date: DateTime.local().toISO({
                includeOffset: false,
            }),
            end_date: DateTime.local().plus({ days: 1 }).toISO({
                includeOffset: false,
            }),
            location: "Thessaloniki, Greece",
            url: "https://www.example.com",
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
                cy.log(calendar_event);
            });
        cy.cas_login("tstteacher2", "password");
        cy.visit("/calendar");
    });
});
