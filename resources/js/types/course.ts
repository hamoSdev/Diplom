/** Держать в синхроне с App\Enums\CourseStatus. */
export type CourseStatus = 'draft' | 'generating' | 'ready' | 'failed';

/** Один курс в списке преподавателя (см. CourseController::index). */
export type CourseListItem = {
    id: number;
    title: string;
    subject_id: number | null;
    subject_name: string | null;
    status: CourseStatus;
    status_label: string;
};
