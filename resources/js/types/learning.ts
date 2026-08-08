/** Курс, доступный студенту (см. LearningController::index). */
export type StudentCourse = {
    id: number;
    title: string;
    description: string | null;
    lecturer_name: string;
    status: string;
    status_label: string;
};

/** Предмет факультета студента со списком его курсов. */
export type StudentSubject = {
    id: number;
    name: string;
    courses: StudentCourse[];
};

/** Факультет, к которому привязан студент. */
export type StudentFaculty = {
    id: number;
    name: string;
};
