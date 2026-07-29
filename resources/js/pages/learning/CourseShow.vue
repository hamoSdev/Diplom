<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Download } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { formatFileSize } from '@/lib/formatFileSize';
import type { CourseMaterialItem } from '@/types';
import { route } from 'ziggy-js';

type CourseDetail = {
    id: number;
    title: string;
    subject_name: string;
    lecturer_name: string;
    status_label: string;
    content: string | null;
    has_exam: boolean;
    materials: CourseMaterialItem[];
};

// Функциональная форма layout получает пропсы страницы, поэтому в breadcrumbs можно
// подставить название текущего курса. «My learning» — родитель-ссылка (кнопка «назад»),
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
</script>

<template>
    <Head :title="course.title" />

    <article class="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                :title="course.title"
                :description="`${course.subject_name} · ${course.lecturer_name} · ${course.status_label}`"
            />
            <Button v-if="course.has_exam" as-child>
                <Link :href="route('course-exam.show', course.id)">
                    Take exam
                </Link>
            </Button>
        </div>

        <div
            v-if="course.content"
            class="tiptap rounded-xl border p-6"
            v-html="course.content"
        />
        <p
            v-else-if="course.materials.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            This course has no content yet.
        </p>

        <section v-if="course.materials.length > 0" class="flex flex-col gap-2">
            <h2 class="text-sm font-medium text-muted-foreground">Materials</h2>
            <a
                v-for="material in course.materials"
                :key="material.id"
                :href="route('materials.download', material.id)"
                class="flex items-center justify-between rounded-lg border p-3 text-sm transition-colors hover:bg-muted"
            >
                <span class="flex items-center gap-2 font-medium">
                    <Download class="size-4 text-muted-foreground" />
                    {{ material.original_name }}
                </span>
                <span class="text-muted-foreground">
                    {{ formatFileSize(material.size) }}
                </span>
            </a>
        </section>
    </article>
</template>
