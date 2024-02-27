import type { PageProps } from "@inertiajs/core";

export interface PageWithSharedProps extends PageProps {
    jetstream: {
        canCreateTeams: boolean;
        canManageTwoFactorAuthentication: boolean;
        canUpdatePassword: boolean;
        canUpdateProfileInformation: boolean;
        hasEmailVerification: boolean;
        flash: {
            bannerStyle?: "success" | "danger";
            banner?: string;
            token?: string;
        };
        hasAccountDeletionFeatures: boolean;
        hasApiFeatures: boolean;
        hasTeamFeatures: boolean;
        hasTermsAndPrivacyPolicyFeature: boolean;
        managesProfilePhotos: boolean;
    };

    user: App.Models.User & {
        two_factor_enabled: boolean;
    };

    cas_user: App.Models.CasUser | null;
    cas_user_role: string;

    auth: {
        user: {
            id: number;
            name: string;
            username: string;
        };
    };

    flash: {
        message: string;
        success: string;
        danger: string;
        warning: string;
    };

    organization: string;
}
