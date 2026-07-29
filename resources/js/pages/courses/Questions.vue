<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Trash2 } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import LecturerQuestionForm from '@/components/LecturerQuestionForm.vue';
import { Button } from '@/components/ui/button';
import { coursesIndexUrl } from '@/lib/coursesIndexUrl';
import type { QuestionListItem, TopicItem, ValueLabelOption } from '@/types';
import { route } from 'ziggy-js';

type Course = { id: number; title: string };
type LayoutProps = { course: Course; auth: { user: { role: string } } };

// Функциональная форма layout — название курса и ролевой родитель в breadcrumbs.
defineOptions({
    layout: (props: LayoutProps) => ({
        breadcrumbs: [
            { title: 'Courses', href: coursesIndexUrl(props.auth.user.role) },
            {
                title: props.course.title,
                href: route('courses.questions.index', props.course.id),
            },
        ],
    }),
});

const props = defineProps<{
    course: Course;
    topics: TopicItem[];
    questions: QuestionListItem[];
    typeOptions: ValueLabelOption[];
    difficultyOptions: ValueLabelOption[];
}>();

function remove(question: QuestionListItem): void {
    router.delete(
        route('courses.questions.destroy', [props.course.id, question.id]),
    );
}
</script>

<template>
    <Head :title="`${course.title} — questions`" />

    <div class="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4">
        <Heading
            :title="course.title"
            description="Author the question bank for this course's exam. Each question belongs to a topic."
        />

        <p
            v-if="topics.length === 0"
            class="rounded-xl border border-dashed p-6 text-center text-sm text-muted-foreground"
        >
            Add topics first —
            <Link
                :href="route('courses.topics.index', course.id)"
                class="underline"
            >
                manage topics
            </Link>
            .
        </p>

        <LecturerQuestionForm
            v-else
            :course-id="course.id"
            :topics="topics"
            :type-options="typeOptions"
            :difficulty-options="difficultyOptions"
        />

        <p
            v-if="questions.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            No questions yet.
        </p>
        <ul v-else class="flex flex-col gap-2">
            <li
                v-for="question in questions"
                :key="question.id"
                class="flex items-start justify-between gap-3 rounded-lg border p-3 text-sm"
            >
                <div class="flex flex-col gap-1">
                    <span class="font-medium">{{ question.body }}</span>
                    <span class="text-xs text-muted-foreground">
                        {{ question.topic_title ?? '—' }} ·
                        {{ question.type_label }} ·
                        {{ question.difficulty_label }}
                    </span>
                </div>
                <Button
                    variant="ghost"
                    size="icon"
                    class="size-8 shrink-0"
                    @click="remove(question)"
                >
                    <Trash2 class="size-4" />
                </Button>
            </li>
        </ul>
    </div>
</template>
