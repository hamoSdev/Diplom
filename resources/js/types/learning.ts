/** Курс, доступный студенту (см. LearningController::index). */
export type StudentCourse = {
    id: number;
    title: string;
    lecturer_name: string;
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
