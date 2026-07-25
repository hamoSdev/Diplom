<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import CourseFormFields from '@/components/CourseFormFields.vue';
import CourseRowActions from '@/components/CourseRowActions.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { AdminCourseListItem, SelectOption } from '@/types';
import { route } from 'ziggy-js';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Courses', href: route('admin.courses.index') }],
    },
});

defineProps<{
    courses: AdminCourseListItem[];
    subjects: SelectOption[];
    lecturers: SelectOption[];
}>();
</script>

<template>
    <Head title="Courses" />

    <div class="flex flex-col gap-8 p-4">
        <Heading
            title="Courses"
            description="Create courses and assign them to a subject and lecturer"
        />

        <Form
            :action="route('admin.courses.store')"
            method="post"
            :reset-on-success="['title']"
            class="grid max-w-xl gap-4"
            v-slot="{ errors, processing }"
        >
            <CourseFormFields
                :subjects="subjects"
                :lecturers="lecturers"
                :errors="errors"
            />
            <div>
                <Button :disabled="processing" data-test="create-course-button">
                    Create course
                </Button>
            </div>
        </Form>

        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Title</TableHead>
                    <TableHead>Subject</TableHead>
                    <TableHead>Lecturer</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead class="text-right">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="course in courses" :key="course.id">
                    <TableCell class="font-medium">{{
                        course.title
                    }}</TableCell>
                    <TableCell>{{ course.subject_name ?? '—' }}</TableCell>
                    <TableCell>{{ course.lecturer_name }}</TableCell>
                    <TableCell>{{ course.status_label }}</TableCell>
                    <TableCell>
                        <CourseRowActions
                            :course="course"
                            :subjects="subjects"
                            :lecturers="lecturers"
                        />
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
