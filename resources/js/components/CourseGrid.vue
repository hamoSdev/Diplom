<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight, BookOpen } from '@lucide/vue';

type CourseCard = {
    id: number;
    title: string;
    subject_name: string;
    url: string;
};

defineProps<{
    courses: CourseCard[];
}>();
</script>

<template>
    <div
        class="grid [grid-template-columns:repeat(auto-fill,minmax(240px,1fr))] gap-4"
    >
        <Link
            v-for="course in courses"
            :key="course.id"
            :href="course.url"
            class="group flex flex-col gap-3 rounded-xl border bg-card p-4 transition-colors hover:border-primary/40 hover:bg-muted/40"
        >
            <div class="flex items-start justify-between">
                <div
                    class="flex size-9 items-center justify-center rounded-lg bg-primary/10 text-primary"
                >
                    <BookOpen class="size-4" />
                </div>
                <ArrowUpRight
                    class="size-4 text-muted-foreground opacity-0 transition-opacity group-hover:opacity-100"
                />
            </div>
            <div class="flex flex-col gap-2">
                <span class="leading-snug font-medium">{{ course.title }}</span>
                <span
                    class="w-fit rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground"
                >
                    {{ course.subject_name }}
                </span>
            </div>
        </Link>
    </div>
</template>
