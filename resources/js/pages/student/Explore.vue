<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Library } from '@lucide/vue';
import CourseStatusBadge from '@/components/CourseStatusBadge.vue';
import Heading from '@/components/Heading.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { route } from 'ziggy-js';

type ExploreCourse = {
    id: number;
    title: string;
    lecturer_name: string;
    status: string;
    status_label: string;
};

type ExploreSubject = {
    id: number;
    name: string;
    faculty_name: string | null;
    is_mine: boolean;
    courses: ExploreCourse[];
};

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Explore', href: route('explore.index') }],
    },
});

defineProps<{
    subjects: ExploreSubject[];
}>();
</script>

<template>
    <Head title="Explore" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="Explore"
            description="Browse every subject and course on the platform. Full access to reading and exams stays within your faculty."
        />

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <Card v-for="subject in subjects" :key="subject.id">
                <CardHeader
                    class="flex-row items-start justify-between gap-3 space-y-0"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex size-9 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        >
                            <Library class="size-4" />
                        </div>
                        <div class="flex flex-col">
                            <CardTitle class="text-base">
                                {{ subject.name }}
                            </CardTitle>
                            <span class="text-xs text-muted-foreground">
                                {{ subject.faculty_name }}
                            </span>
                        </div>
                    </div>
                    <span
                        v-if="subject.is_mine"
                        class="shrink-0 rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary"
                    >
                        Your faculty
                    </span>
                </CardHeader>
                <CardContent>
                    <p
                        v-if="subject.courses.length === 0"
                        class="text-sm text-muted-foreground"
                    >
                        No courses yet.
                    </p>
                    <ul v-else class="flex flex-col gap-2">
                        <li v-for="course in subject.courses" :key="course.id">
                            <component
                                :is="subject.is_mine ? Link : 'div'"
                                :href="
                                    subject.is_mine
                                        ? route(
                                              'learning.courses.show',
                                              course.id,
                                          )
                                        : undefined
                                "
                                class="flex items-center justify-between gap-3 rounded-lg border p-3 text-sm"
                                :class="
                                    subject.is_mine
                                        ? 'transition-colors hover:border-primary/40 hover:bg-muted/40'
                                        : ''
                                "
                            >
                                <div class="flex min-w-0 flex-col">
                                    <span class="truncate font-medium">
                                        {{ course.title }}
                                    </span>
                                    <span class="text-xs text-muted-foreground">
                                        {{ course.lecturer_name }}
                                    </span>
                                </div>
                                <CourseStatusBadge
                                    :status="course.status"
                                    :label="course.status_label"
                                />
                            </component>
                        </li>
                    </ul>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
