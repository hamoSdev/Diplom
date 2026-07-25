<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import LecturerCourseFields from '@/components/LecturerCourseFields.vue';
import LecturerCourseRowActions from '@/components/LecturerCourseRowActions.vue';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { CourseListItem, SelectOption } from '@/types';
import { route } from 'ziggy-js';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Courses', href: route('courses.index') }],
    },
});

defineProps<{
    courses: CourseListItem[];
    subjects: SelectOption[];
}>();
</script>

<template>
    <Head title="Courses" />

    <div class="flex flex-col gap-8 p-4">
        <Heading
            title="Courses"
            description="Create and manage the courses you teach"
        />

        <p
            v-if="subjects.length === 0"
            class="rounded-xl border border-dashed p-4 text-sm text-muted-foreground"
        >
            You are not attached to any subject yet. An administrator assigns
            subjects to you before you can create courses.
        </p>

        <Form
            v-else
            :action="route('courses.store')"
            method="post"
            :reset-on-success="['title']"
            class="grid max-w-xl gap-4"
            v-slot="{ errors, processing }"
        >
            <LecturerCourseFields :subjects="subjects" :errors="errors" />
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
                    <TableCell>{{ course.status_label }}</TableCell>
                    <TableCell>
                        <LecturerCourseRowActions
                            :course="course"
                            :subjects="subjects"
                        />
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
