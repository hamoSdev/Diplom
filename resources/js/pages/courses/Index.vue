<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { CourseListItem, CourseStatus } from '@/types';
import { route } from 'ziggy-js';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Courses', href: route('courses.index') }],
    },
});

defineProps<{
    courses: CourseListItem[];
}>();

const statusVariant: Record<
    CourseStatus,
    'default' | 'secondary' | 'destructive' | 'outline'
> = {
    draft: 'secondary',
    generating: 'outline',
    ready: 'default',
    failed: 'destructive',
};
</script>

<template>
    <Head title="Courses" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="Courses"
            description="Courses you teach and their generation status"
        />

        <p
            v-if="courses.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            You don't have any courses yet.
        </p>

        <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="course in courses" :key="course.id">
                <CardHeader>
                    <div class="flex items-start justify-between gap-2">
                        <CardTitle class="text-base">{{
                            course.title
                        }}</CardTitle>
                        <Badge :variant="statusVariant[course.status]">
                            {{ course.status_label }}
                        </Badge>
                    </div>
                </CardHeader>
                <CardContent class="text-sm text-muted-foreground">
                    Created {{ course.created_at ?? '—' }}
                </CardContent>
            </Card>
        </div>
    </div>
</template>
