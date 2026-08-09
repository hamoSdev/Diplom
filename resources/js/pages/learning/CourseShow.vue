<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    BookOpen,
    Download,
    Dumbbell,
    FileText,
    GraduationCap,
    User,
} from '@lucide/vue';
import { ref } from 'vue';
import CourseStatusBadge from '@/components/CourseStatusBadge.vue';
import { Button } from '@/components/ui/button';
import { formatFileSize } from '@/lib/formatFileSize';
import type { CourseMaterialItem } from '@/types';
import { route } from 'ziggy-js';

type CourseTopic = {
    id: number;
    title: string;
    content: string | null;
    has_questions: boolean;
};

type CourseDetail = {
    id: number;
    title: string;
    subject_name: string;
    lecturer_name: string;
    status: string;
    status_label: string;
    content: string | null;
    has_exam: boolean;
    materials: CourseMaterialItem[];
    topics: CourseTopic[];
};

// Функциональная форма layout: «My learning» — родитель-ссылка (кнопка «назад»),
// последний элемент (название курса) рендерится как текущая страница.
defineOptions({
    layout: (props: { course: CourseDetail }) => ({
        breadcrumbs: [
            { title: 'My learning', href: route('learning.index') },
            {
                title: props.course.title,
                href: route('learning.courses.show', props.course.id),
            },
        ],
    }),
});

defineProps<{
    course: CourseDetail;
}>();

const openTopic = ref<number | null>(null);

function toggleTopic(id: number): void {
    openTopic.value = openTopic.value === id ? null : id;
}
</script>

<template>
    <Head :title="course.title" />

    <article class="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-semibold">{{ course.title }}</h1>
                <div
                    class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground"
                >
                    <span
                        class="rounded-full bg-muted px-2 py-0.5 text-xs font-medium"
                    >
                        {{ course.subject_name }}
                    </span>
                    <span class="flex items-center gap-1">
                        <User class="size-3.5" />
                        {{ course.lecturer_name }}
                    </span>
                    <CourseStatusBadge
                        :status="course.status"
                        :label="course.status_label"
                    />
                </div>
            </div>
            <Button v-if="course.has_exam" as-child>
                <Link :href="route('course-exam.show', course.id)">
                    <GraduationCap class="size-4" />
                    Take exam
                </Link>
            </Button>
        </div>

        <div
            v-if="course.content"
            class="tiptap rounded-xl border bg-card p-6"
            v-html="course.content"
        />
        <p
            v-else-if="course.materials.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            This course has no content yet.
        </p>

        <section v-if="course.topics.length > 0" class="flex flex-col gap-2">
            <h2 class="text-sm font-medium text-muted-foreground">Topics</h2>
            <div
                v-for="topic in course.topics"
                :key="topic.id"
                class="flex flex-col gap-3 rounded-lg border bg-card p-3"
            >
                <div class="flex items-center gap-3">
                    <span class="flex-1 text-sm font-medium">
                        {{ topic.title }}
                    </span>
                    <Button
                        v-if="topic.content"
                        variant="ghost"
                        size="sm"
                        @click="toggleTopic(topic.id)"
                    >
                        <BookOpen class="size-4" />
                        Material
                    </Button>
                    <Button
                        v-if="topic.has_questions"
                        variant="outline"
                        size="sm"
                        as-child
                    >
                        <Link :href="route('practice.topic', topic.id)">
                            <Dumbbell class="size-4" />
                            Practice
                        </Link>
                    </Button>
                </div>
                <div
                    v-if="topic.content && openTopic === topic.id"
                    class="tiptap border-t pt-3"
                    v-html="topic.content"
                />
            </div>
        </section>

        <section v-if="course.materials.length > 0" class="flex flex-col gap-2">
            <h2 class="text-sm font-medium text-muted-foreground">Materials</h2>
            <a
                v-for="material in course.materials"
                :key="material.id"
                :href="route('materials.download', material.id)"
                class="group flex items-center justify-between gap-3 rounded-lg border bg-card p-3 text-sm transition-colors hover:border-primary/40 hover:bg-muted/40"
            >
                <span class="flex min-w-0 items-center gap-3">
                    <span
                        class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                    >
                        <FileText class="size-4" />
                    </span>
                    <span class="flex min-w-0 flex-col">
                        <span class="truncate font-medium">
                            {{ material.original_name }}
                        </span>
                        <span class="text-xs text-muted-foreground">
                            {{ formatFileSize(material.size) }}
                        </span>
                    </span>
                </span>
                <Download
                    class="size-4 shrink-0 text-muted-foreground transition-colors group-hover:text-foreground"
                />
            </a>
        </section>
    </article>
</template>
