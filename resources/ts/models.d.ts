declare namespace App.Models {
    export interface User {
        id: number;
        name: string;
    }

    export interface Calendar {
        id: number;
        name: string;
        color: string;
        active: boolean;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface CalendarEvent {
        id: number;
        title: string;
        description: string;
        start_date: string;
        end_date: string;
        location: string;
        url: string;
        calendar_id: number;
        created_at: string | null;
        updated_at: string | null;
    }
}
