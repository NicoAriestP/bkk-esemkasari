import type { User } from './index';

export interface Announcement {
    id: number;
    title: string;
    content: string;
    file?: string;
    file_url?: string;
    created_at: string;
    updated_at: string;
    created_by: User;
    updated_by: User;
}

export interface AnnouncementForm {
    title: string;
    content: string;
    file?: File;
}
