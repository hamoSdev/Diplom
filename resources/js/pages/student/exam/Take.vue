<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Check } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import type { ExamNav, ExamProgress, ExamQuestion } from '@/types';
import { route } from 'ziggy-js';

defineOptions({
    layout: (props: { nav?: ExamNav }) => ({
        breadcrumbs: props.nav?.breadcrumbs ?? [
            { title: 'Self-check', href: route('exam.show') },
        ],
    }),
});

const props = defineProps<{
    question: ExamQuestion;
    progress: ExamProgress;
    nav?: ExamNav;
}>();

const form = useForm<{ selected_option_ids: number[]; answer_text: string }>({
    selected_option_ids: [],
    answer_text: '',
});

const isOpen = computed(() => props.question.type === 'open');
const isMultiple = computed(() => props.question.type === 'multiple');

const percent = computed(() =>
    Math.round((props.progress.answered / props.progress.total) * 100),
);

const canSubmit = computed(() =>
    isOpen.value
        ? form.answer_text.trim().length > 0
        : form.selected_option_ids.length > 0,
);

function isSelected(id: number): boolean {
    return form.selected_option_ids.includes(id);
}

function choose(id: number): void {
    if (isMultiple.value) {
        form.selected_option_ids = isSelected(id)
            ? form.selected_option_ids.filter((x) => x !== id)
            : [...form.selected_option_ids, id];

        return;
    }

    form.selected_option_ids = [id];
}

function submit(): void {
    if (!canSubmit.value) {
        return;
    }

    form.post(props.nav?.answerUrl ?? route('exam.answer'), {
        onSuccess: () => form.reset(),
    });
}

function skip(): void {
    router.post(props.nav?.skipUrl ?? route('exam.skip'));
}
</script>

<template>
    <Head title="Self-check" />

    <div class="mx-auto flex w-full max-w-2xl flex-col gap-6 p-4">
        <div class="flex flex-col gap-2">
            <div class="flex justify-between text-sm text-muted-foreground">
                <span
                    >Question {{ progress.current }} of
                    {{ progress.total }}</span
                >
                <span>{{ percent }}%</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-muted">
                <div
                    class="h-full rounded-full bg-primary transition-all"
                    :style="{ width: `${percent}%` }"
                />
            </div>
        </div>

        <h1 class="text-xl font-medium">{{ question.body }}</h1>

        <p v-if="isMultiple" class="-mt-2 text-sm text-muted-foreground">
            Select all that apply.
        </p>

        <!-- Open answer -->
        <Textarea
            v-if="isOpen"
            v-model="form.answer_text"
            rows="6"
            placeholder="Type your answer…"
        />

        <!-- Option-based answer -->
        <div v-else class="grid gap-3">
            <button
                v-for="option in question.options"
                :key="option.id"
                type="button"
                class="flex items-center gap-3 rounded-xl border p-4 text-left transition-colors"
                :class="
                    isSelected(option.id)
                        ? 'border-primary bg-primary/5 ring-1 ring-primary'
                        : 'hover:border-primary/40 hover:bg-muted/40'
                "
                @click="choose(option.id)"
            >
                <div
                    class="flex size-5 shrink-0 items-center justify-center border"
                    :class="[
                        isMultiple ? 'rounded-md' : 'rounded-full',
                        isSelected(option.id)
                            ? 'border-primary bg-primary text-primary-foreground'
                            : 'border-muted-foreground/30',
                    ]"
                >
                    <Check v-if="isSelected(option.id)" class="size-3" />
                </div>
                <span>{{ option.body }}</span>
            </button>
        </div>

        <div class="flex items-center justify-between">
            <Button :disabled="!canSubmit || form.processing" @click="submit">
                Submit answer
            </Button>
            <Button variant="ghost" size="sm" @click="skip">
                Skip exam (test)
            </Button>
        </div>
    </div>
</template>
