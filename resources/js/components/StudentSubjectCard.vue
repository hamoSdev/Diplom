<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Library } from '@lucide/vue';
import { computed } from 'vue';
import CircularCarousel from '@/components/CircularCarousel.vue';
import { Card, CardContent } from '@/components/ui/card';
import type { StudentSubject } from '@/types';
import { route } from 'ziggy-js';

const props = defineProps<{
    subject: StudentSubject;
}>();

const items = computed(() =>
    props.subject.courses.map((course) => ({
        id: String(course.id),
        title: course.title,
        description: course.description ?? `By ${course.lecturer_name}`,
        tag: course.status_label,
    })),
);

function openCourse(index: number): void {
    const course = props.subject.courses[index];
    router.visit(route('learning.courses.show', course.id));
}
</script>

<template>
    <Card class="overflow-hidden py-0">
        <CardContent
            class="relative flex flex-col items-center gap-6 overflow-hidden px-6 pt-6"
        >
            <div
                class="absolute top-6 left-6 flex size-9 items-center justify-center rounded-lg bg-primary/10 text-primary"
            >
                <Library class="size-4" />
            </div>

            <h3 class="text-base font-semibold">{{ subject.name }}</h3>

            <p
                v-if="subject.courses.length === 0"
                class="text-sm text-muted-foreground"
            >
                No courses yet.
            </p>
            <CircularCarousel v-else :items="items" @open="openCourse" />
        </CardContent>
    </Card>
</template>
