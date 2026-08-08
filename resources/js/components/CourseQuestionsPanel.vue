<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Trash2 } from '@lucide/vue';
import LecturerQuestionForm from '@/components/LecturerQuestionForm.vue';
import { Button } from '@/components/ui/button';
import type {
    QuestionListItem,
    TopicItem,
    ValueLabelOption,
} from '@/types';
import { route } from 'ziggy-js';

const props = defineProps<{
    courseId: number;
    topics: TopicItem[];
    questions: QuestionListItem[];
    typeOptions: ValueLabelOption[];
    difficultyOptions: ValueLabelOption[];
}>();

function remove(question: QuestionListItem): void {
    router.delete(route('courses.questions.destroy', [props.courseId, question.id]), {
        preserveScroll: true,
        preserveState: true,
    });
}
</script>

<template>
    <div class="flex flex-col gap-4">
        <p
            v-if="topics.length === 0"
            class="rounded-xl border border-dashed p-6 text-center text-sm text-muted-foreground"
        >
            Add topics first — questions attach to a topic.
        </p>

        <LecturerQuestionForm
            v-else
            :course-id="courseId"
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
