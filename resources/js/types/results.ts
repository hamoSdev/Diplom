/** Строка курса в списке результатов преподавателя (см. LecturerResultsController::index). */
export type ResultsCourseRow = {
    id: number;
    title: string;
    students: number;
    attempts: number;
    avg_score: number | null;
};

/** Количество попыток в одной оценочной полосе. */
export type ResultsGradeCount = {
    grade: string;
    label: string;
    count: number;
};

/** Успеваемость группы по одной теме. */
export type ResultsTopicScore = {
    title: string;
    correct: number;
    total: number;
    percent: number;
};

/** Общий результат группы по курсу (см. LecturerResultsController::show). */
export type ResultsGroupSummary = {
    students: number;
    attempts: number;
    avg_score: number | null;
    max_score: number;
    grades: ResultsGradeCount[];
    topics: ResultsTopicScore[];
    revise: string[];
};

/** Строка студента в результатах курса. */
export type ResultsStudentRow = {
    attempt_id: number;
    name: string;
    score: number | null;
    grade: string | null;
    grade_label: string | null;
    level_label: string;
    finished_at: string | null;
    attempts_count: number;
};
