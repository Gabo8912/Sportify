export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;

    role: string;
    profile_photo_url?: string;
    profile?: Profile | null;


    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
    
};
export interface Profile {
    id: number;
    user_id: number;
    current_club?: string;
    position?: string;
    height?: number;
    weight?: number;
    dominant_foot?: string;
    birth_date?: string;
}

export type Auth = {
    user: User;
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
