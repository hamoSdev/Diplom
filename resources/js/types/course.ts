/** Держать в синхроне с App\Enums\CourseStatus. */
export type CourseStatus = 'draft' | 'generating' | 'ready' | 'failed';

/** Один курс в списке преподавателя (см. CourseController::index). */
export type CourseListItem = {
    id: number;
    title: string;
    status: CourseStatus;
    status_label: string;
    created_at: string | null;
};
