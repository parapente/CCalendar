declare namespace App.Models {
    import type { JsonObjectExpression } from "typescript";

    export interface User {
        id: number;
        name: string;
        username: string;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface CasUser {
        id: number;
        name: string;
        username: string;
        active: boolean;
        role_id: number;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface Role {
        id: number;
        name: string;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface Calendar {
        id: number;
        name: string;
        color: string;
        active: boolean;
        created_at: string | null;
        updated_at: string | null;
        shared: boolean;
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
        cas_user_id: number;
        created_at: string | null;
        updated_at: string | null;
        cancelled: boolean;
    }

    export interface Report {
        id: number;
        name: string;
        type: number;
        active: boolean;
        options: string | null;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface ReportData {
        id: number;
        cas_user_id: number;
        report_id: number;
        data: string | null;
        created_at: string | null;
        updated_at: string | null;
    }
}
