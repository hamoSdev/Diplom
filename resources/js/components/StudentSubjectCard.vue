<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { StudentSubject } from '@/types';
import { route } from 'ziggy-js';

defineProps<{
    subject: StudentSubject;
}>();
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="text-base">{{ subject.name }}</CardTitle>
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
                    <Link
                        :href="route('learning.courses.show', course.id)"
                        class="flex items-center justify-between rounded-md border p-2 text-sm transition-colors hover:bg-muted"
                    >
                        <span class="font-medium">{{ course.title }}</span>
                        <span class="text-muted-foreground">
                            {{ course.lecturer_name }} ·
                            {{ course.status_label }}
                        </span>
                    </Link>
                </li>
            </ul>
        </CardContent>
    </Card>
</template>
