import { route } from 'ziggy-js';

/**
 * The courses list a user returns to from a course sub-page: admins manage all
 * courses, lecturers only their own.
 */
export function coursesIndexUrl(role: string): string {
    return role === 'admin'
        ? route('admin.courses.index')
        : route('courses.index');
}
