import type { User } from './index';

export type QuestionType = 'dropdown' | 'checkbox' | 'fillable' | 'date';

export interface QuestionOption {
    id?: number;
    question_id?: number;
    option_label: string;
    created_at?: string;
    updated_at?: string;
}

export interface QuestionnaireQuestion {
    id?: number;
    questionnaire_id?: number;
    question_title: string;
    type: QuestionType;
    type_label?: string;
    notes?: string | null;
    questionOptions?: QuestionOption[];
    created_at?: string;
    updated_at?: string;
}

export interface Questionnaire {
    id?: number;
    title: string;
    description: string;
    due_at?: string | null;
    created_by?: number;
    updated_by?: number;
    createdBy?: User;
    updatedBy?: User;
    questions?: QuestionnaireQuestion[];
    created_at?: string;
    updated_at?: string;
}

export interface QuestionTypeOption {
    label: string;
    value: QuestionType;
    icon: string;
    requiresOptions: boolean;
}
