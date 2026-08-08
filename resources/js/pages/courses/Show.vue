<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Sparkles } from '@lucide/vue';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import CourseMaterialsPanel from '@/components/CourseMaterialsPanel.vue';
import CourseQuestionsPanel from '@/components/CourseQuestionsPanel.vue';
import CourseStatusBadge from '@/components/CourseStatusBadge.vue';
import CourseTopicsPanel from '@/components/CourseTopicsPanel.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { coursesIndexUrl } from '@/lib/coursesIndexUrl';
import type {
    CourseMaterialItem,
    CourseStatus,
    QuestionListItem,
    TopicItem,
    ValueLabelOption,
} from '@/types';
import { route } from 'ziggy-js';

type Course = {
    id: number;
    title: string;
    subject_name: string | null;
    status: CourseStatus;
    status_label: string;
    content: string | null;
};

type LayoutProps = { course: Course; auth: { user: { role: string } } };

defineOptions({
    layout: (props: LayoutProps) => ({
        breadcrumbs: [
            { title: 'Courses', href: coursesIndexUrl(props.auth.user.role) },
            {
                title: props.course.title,
                href: route('courses.show', props.course.id),
            },
        ],
    }),
});

const props = defineProps<{
    course: Course;
    topics: TopicItem[];
    questions: QuestionListItem[];
    materials: CourseMaterialItem[];
    typeOptions: ValueLabelOption[];
    difficultyOptions: ValueLabelOption[];
}>();

const tabs = [
    { key: 'content', label: 'Content' },
    { key: 'topics', label: 'Topics' },
    { key: 'questions', label: 'Questions' },
    { key: 'materials', label: 'Materials' },
];

const active = ref('content');

const isGenerating = computed(() => props.course.status === 'generating');

function generate(): void {
    router.post(
        route('courses.generate', props.course.id),
        {},
        { preserveScroll: true },
    );
}

// While the AI job runs, refresh the page props every few seconds so topics,
// material and questions appear as they land and the status flips to Ready.
let poll: ReturnType<typeof setInterval> | null = null;

function stopPoll(): void {
    if (poll !== null) {
        clearInterval(poll);
        poll = null;
    }
}

watch(
    isGenerating,
    (generating) => {
        if (generating && poll === null) {
            poll = setInterval(() => router.reload(), 4000);
        } else if (!generating) {
            stopPoll();
        }
    },
    { immediate: true },
);

onBeforeUnmount(stopPoll);
</script>

<template>
    <Head :title="course.title" />

    <div class="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-semibold">{{ course.title }}</h1>
                <div
                    class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground"
                >
                    <span
                        v-if="course.subject_name"
                        class="rounded-full bg-muted px-2 py-0.5 text-xs font-medium"
                    >
                        {{ course.subject_name }}
                    </span>
                    <CourseStatusBadge
                        :status="course.status"
                        :label="course.status_label"
                    />
                </div>
            </div>

            <Button :disabled="isGenerating" @click="generate">
                <Sparkles class="size-4" />
                {{ isGenerating ? 'Generating…' : 'Generate with AI' }}
            </Button>
        </div>

        <div class="flex gap-1 rounded-lg border bg-muted/40 p-1 text-sm">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                type="button"
                class="flex-1 rounded-md px-3 py-1.5 font-medium transition-colors"
                :class="
                    active === tab.key
                        ? 'bg-background text-foreground shadow-sm'
                        : 'text-muted-foreground hover:text-foreground'
                "
                @click="active = tab.key"
            >
                {{ tab.label }}
            </button>
        </div>

        <div v-show="active === 'content'">
            <Card>
                <CardContent class="pt-6">
                    <div
                        v-if="course.content"
                        class="tiptap"
                        v-html="course.content"
                    />
                    <p v-else class="text-sm text-muted-foreground">
                        No written content yet. Use the Editor to write an
                        article and save it into a course.
                    </p>
                </CardContent>
            </Card>
        </div>

        <div v-show="active === 'topics'">
            <CourseTopicsPanel :course-id="course.id" :topics="topics" />
        </div>

        <div v-show="active === 'questions'">
            <CourseQuestionsPanel
                :course-id="course.id"
                :topics="topics"
                :questions="questions"
                :type-options="typeOptions"
                :difficulty-options="difficultyOptions"
            />
        </div>

        <div v-show="active === 'materials'">
            <CourseMaterialsPanel
                :course-id="course.id"
                :materials="materials"
            />
        </div>
    </div>
</template>
