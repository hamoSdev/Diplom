/** Держать в синхроне с App\Enums\CourseStatus. */
export type CourseStatus = 'draft' | 'generating' | 'ready' | 'failed';

/** Тема курса (см. TopicController). */
export type TopicItem = {
    id: number;
    title: string;
};

/** Вариант value/label для нативного select. */
export type ValueLabelOption = {
    value: string;
    label: string;
};

/** Вопрос курса в списке (см. QuestionController::index). */
export type QuestionListItem = {
    id: number;
    body: string;
    topic_title: string | null;
    type_label: string;
    difficulty_label: string;
};

/** Файл-материал курса (см. CourseMaterialController). */
export type CourseMaterialItem = {
    id: number;
    original_name: string;
    size: number;
};

/** Один курс в списке преподавателя (см. CourseController::index). */
export type CourseListItem = {
    id: number;
    title: string;
    subject_id: number | null;
    subject_name: string | null;
    status: CourseStatus;
    status_label: string;
};
