<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Check, RotateCcw, X } from '@lucide/vue';
import { computed, reactive, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { route } from 'ziggy-js';

type PracticeOption = { id: number; body: string; is_correct: boolean };
type PracticeQuestion = {
    id: number;
    body: string;
    type: string;
    explanation: string | null;
    options: PracticeOption[];
};

const props = defineProps<{
    topic: { id: number; title: string };
    course: { id: number; title: string };
    questions: PracticeQuestion[];
}>();

defineOptions({
    layout: (page: { course: { id: number; title: string } }) => ({
        breadcrumbs: [
            { title: 'My learning', href: route('learning.index') },
            {
                title: page.course.title,
                href: route('learning.courses.show', page.course.id),
            },
            { title: 'Practice', href: '#' },
        ],
    }),
});

const selections = reactive<Record<number, number[]>>({});
const checked = ref(false);

function isMultiple(q: PracticeQuestion): boolean {
    return q.type === 'multiple';
}

function isSelected(q: PracticeQuestion, optionId: number): boolean {
    return (selections[q.id] ?? []).includes(optionId);
}

function choose(q: PracticeQuestion, optionId: number): void {
    if (checked.value) {
        return;
    }

    if (isMultiple(q)) {
        const current = selections[q.id] ?? [];
        selections[q.id] = current.includes(optionId)
            ? current.filter((x) => x !== optionId)
            : [...current, optionId];

        return;
    }

    selections[q.id] = [optionId];
}

function isQuestionCorrect(q: PracticeQuestion): boolean {
    const correct = q.options
        .filter((o) => o.is_correct)
        .map((o) => o.id)
        .sort();
    const chosen = [...(selections[q.id] ?? [])].sort();

    return (
        correct.length > 0 &&
        correct.length === chosen.length &&
        correct.every((id, i) => id === chosen[i])
    );
}

const answeredCount = computed(
    () => Object.values(selections).filter((v) => v.length > 0).length,
);

const score = computed(
    () => props.questions.filter((q) => isQuestionCorrect(q)).length,
);

function check(): void {
    checked.value = true;
}

function reset(): void {
    checked.value = false;

    for (const key of Object.keys(selections)) {
        delete selections[Number(key)];
    }
}

// Styling for one option once answers are revealed.
function optionClass(q: PracticeQuestion, o: PracticeOption): string {
    if (!checked.value) {
        return isSelected(q, o.id)
            ? 'border-primary bg-primary/5 ring-1 ring-primary'
            : 'hover:border-primary/40 hover:bg-muted/40';
    }

    if (o.is_correct) {
        return 'border-emerald-500/50 bg-emerald-500/10';
    }

    if (isSelected(q, o.id)) {
        return 'border-destructive/50 bg-destructive/10';
    }

    return 'opacity-60';
}
</script>

<template>
    <Head :title="`${topic.title} — practice`" />

    <div class="mx-auto flex w-full max-w-2xl flex-col gap-6 p-4">
        <Heading :title="topic.title" description="Practice — self-check" />

        <p
            v-if="questions.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            No practice questions for this topic yet.
        </p>

        <template v-else>
            <div
                v-for="(q, index) in questions"
                :key="q.id"
                class="flex flex-col gap-3 rounded-xl border p-4"
            >
                <div class="flex items-start gap-2">
                    <span class="text-sm text-muted-foreground">
                        {{ index + 1 }}.
                    </span>
                    <p class="flex-1 font-medium">{{ q.body }}</p>
                    <span
                        v-if="checked"
                        class="shrink-0 text-sm font-medium"
                        :class="
                            isQuestionCorrect(q)
                                ? 'text-emerald-600 dark:text-emerald-400'
                                : 'text-destructive'
                        "
                    >
                        {{ isQuestionCorrect(q) ? 'Correct' : 'Wrong' }}
                    </span>
                </div>

                <p v-if="isMultiple(q)" class="-mt-1 text-xs text-muted-foreground">
                    Select all that apply.
                </p>

                <div class="grid gap-2">
                    <button
                        v-for="o in q.options"
                        :key="o.id"
                        type="button"
                        :disabled="checked"
                        class="flex items-center gap-3 rounded-lg border p-3 text-left text-sm transition-colors"
                        :class="optionClass(q, o)"
                        @click="choose(q, o.id)"
                    >
                        <div
                            class="flex size-5 shrink-0 items-center justify-center border"
                            :class="[
                                isMultiple(q) ? 'rounded-md' : 'rounded-full',
                                isSelected(q, o.id)
                                    ? 'border-primary bg-primary text-primary-foreground'
                                    : 'border-muted-foreground/30',
                            ]"
                        >
                            <Check v-if="isSelected(q, o.id)" class="size-3" />
                        </div>
                        <span class="flex-1">{{ o.body }}</span>
                        <Check
                            v-if="checked && o.is_correct"
                            class="size-4 shrink-0 text-emerald-600 dark:text-emerald-400"
                        />
                        <X
                            v-else-if="checked && isSelected(q, o.id)"
                            class="size-4 shrink-0 text-destructive"
                        />
                    </button>
                </div>

                <p
                    v-if="checked && q.explanation"
                    class="rounded-lg bg-muted/50 p-3 text-sm text-muted-foreground"
                >
                    {{ q.explanation }}
                </p>
            </div>

            <div
                v-if="checked"
                class="rounded-xl border bg-card p-4 text-center font-medium"
            >
                You got {{ score }} / {{ questions.length }} right
            </div>

            <div class="flex items-center gap-3">
                <Button
                    v-if="!checked"
                    :disabled="answeredCount === 0"
                    @click="check"
                >
                    Check answers
                </Button>
                <Button v-else variant="outline" @click="reset">
                    <RotateCcw class="size-4" />
                    Try again
                </Button>
                <Button variant="ghost" as-child>
                    <Link :href="route('learning.courses.show', course.id)">
                        Back to course
                    </Link>
                </Button>
            </div>
        </template>
    </div>
</template>
