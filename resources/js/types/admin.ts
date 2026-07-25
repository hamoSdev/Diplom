import type { UserRole } from '@/types/auth';

/** Одна строка в таблице пользователей админки (см. Admin\UserController::index). */
export type AdminUserListItem = {
    id: number;
    name: string;
    email: string;
    role: UserRole;
    role_label: string;
    faculty_id: number | null;
    faculty_name: string | null;
    faculty_subjects_count: number | null;
    taught_subjects: string[];
    courses_count: number;
};

/** Одна строка в таблице факультетов (см. Admin\FacultyController::index). */
export type FacultyListItem = {
    id: number;
    name: string;
    subjects_count: number;
};

/** Вариант для выпадающих списков (факультет или преподаватель). */
export type SelectOption = {
    id: number;
    name: string;
};

/** Одна строка в таблице предметов (см. Admin\SubjectController::index). */
export type SubjectListItem = {
    id: number;
    name: string;
    description: string | null;
    faculty_id: number;
    faculty_name: string;
    lecturer_ids: number[];
    lecturer_names: string[];
};

/** Одна строка в таблице курсов админки (см. Admin\CourseController::index). */
export type AdminCourseListItem = {
    id: number;
    title: string;
    subject_id: number;
    subject_name: string | null;
    lecturer_id: number;
    lecturer_name: string;
    status: string;
    status_label: string;
};
