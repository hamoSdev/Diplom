<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { ref } from 'vue';
import CourseStatusBadge from '@/components/CourseStatusBadge.vue';
import Heading from '@/components/Heading.vue';
import LecturerCourseFields from '@/components/LecturerCourseFields.vue';
import LecturerCourseRowActions from '@/components/LecturerCourseRowActions.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardAction,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
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

const createOpen = ref(false);
</script>

<template>
    <Head title="Courses" />

    <div class="flex flex-col gap-8 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Courses"
                description="Create and manage the courses you teach"
            />

            <Dialog v-if="subjects.length > 0" v-model:open="createOpen">
                <DialogTrigger as-child>
                    <Button data-test="create-course-button">
                        <Plus class="size-4" />
                        New course
                    </Button>
                </DialogTrigger>
                <DialogContent>
                    <Form
                        :action="route('courses.store')"
                        method="post"
                        class="grid gap-4"
                        v-slot="{ errors, processing }"
                        @success="createOpen = false"
                    >
                        <DialogHeader>
                            <DialogTitle>New course</DialogTitle>
                        </DialogHeader>
                        <LecturerCourseFields
                            :subjects="subjects"
                            :errors="errors"
                        />
                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button type="button" variant="secondary">
                                    Cancel
                                </Button>
                            </DialogClose>
                            <Button type="submit" :disabled="processing">
                                Create course
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </div>

        <p
            v-if="subjects.length === 0"
            class="rounded-xl border border-dashed p-4 text-sm text-muted-foreground"
        >
            You are not attached to any subject yet. An administrator assigns
            subjects to you before you can create courses.
        </p>

        <p
            v-else-if="courses.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            No courses yet. Create your first course to get started.
        </p>

        <div v-else class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <Card
                v-for="course in courses"
                :key="course.id"
                class="flex flex-col"
            >
                <CardHeader>
                    <CardTitle class="text-base leading-snug">
                        {{ course.title }}
                    </CardTitle>
                    <CardAction>
                        <CourseStatusBadge
                            :status="course.status"
                            :label="course.status_label"
                        />
                    </CardAction>
                </CardHeader>
                <CardContent class="flex-1">
                    <span
                        v-if="course.subject_name"
                        class="rounded-full bg-muted px-2 py-0.5 text-xs font-medium text-muted-foreground"
                    >
                        {{ course.subject_name }}
                    </span>
                    <span v-else class="text-xs text-muted-foreground">
                        No subject
                    </span>
                </CardContent>
                <CardFooter>
                    <LecturerCourseRowActions
                        :course="course"
                        :subjects="subjects"
                        class="w-full"
                    />
                </CardFooter>
            </Card>
        </div>
    </div>
</template>
