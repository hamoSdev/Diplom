/**
 * Контекст роутов/заголовков для переиспользуемых экранов экзамена. Общий
 * экзамен на уровень его не передаёт (фолбэк на роуты exam.*), курсовой — да.
 */
export type ExamNav = {
    title?: string;
    breadcrumbs?: { title: string; href: string }[];
    startUrl?: string;
    answerUrl?: string;
    skipUrl?: string;
    retakeUrl?: string;
    backUrl?: string;
    backLabel?: string;
};

/** Краткий итог последней завершённой попытки (см. ExamController::show). */
export type ExamLastResult = {
    id: number;
    score: number | null;
    grade_label: string | null;
    level_label: string;
};

/** Вариант ответа без пометки правильности. */
export type ExamOption = {
    id: number;
    body: string;
};

/** Текущий вопрос экзамена (см. ExamController::take). */
export type ExamQuestion = {
    id: number;
    body: string;
    type: string;
    options: ExamOption[];
};

/** Прогресс прохождения экзамена. */
export type ExamProgress = {
    answered: number;
    current: number;
    total: number;
};

/** Одна строка разбора ответа в результатах. */
export type ExamAnswerReview = {
    position: number;
    difficulty_label: string;
    is_correct: boolean;
};

/** Результат по одной теме экзамена. */
export type ExamTopicScore = {
    title: string;
    correct: number;
    total: number;
    percent: number;
};

/** Итог завершённого экзамена (см. AdaptiveExamService::summary). */
export type ExamResult = {
    score: number | null;
    max_score: number;
    grade_label: string | null;
    level_label: string;
    correct: number;
    total: number;
    answers: ExamAnswerReview[];
    topics: ExamTopicScore[];
    revise: string[];
};
