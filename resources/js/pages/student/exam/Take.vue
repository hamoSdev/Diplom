<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Check } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import type { ExamNav, ExamProgress, ExamQuestion } from '@/types';
import { route } from 'ziggy-js';

defineOptions({
    layout: (props: { nav?: ExamNav }) => ({
        breadcrumbs: props.nav?.breadcrumbs ?? [
            { title: 'Level exam', href: route('exam.show') },
        ],
    }),
});

const props = defineProps<{
    question: ExamQuestion;
    progress: ExamProgress;
    nav?: ExamNav;
}>();

const form = useForm({ selected_option_id: null as number | null });

const percent = computed(() =>
    Math.round((props.progress.answered / props.progress.total) * 100),
);

function submit(): void {
    if (form.selected_option_id === null) {
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
    <Head title="Level exam" />

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

        <div class="grid gap-3">
            <button
                v-for="option in question.options"
                :key="option.id"
                type="button"
                class="flex items-center gap-3 rounded-xl border p-4 text-left transition-colors"
                :class="
                    form.selected_option_id === option.id
                        ? 'border-primary bg-primary/5 ring-1 ring-primary'
                        : 'hover:border-primary/40 hover:bg-muted/40'
                "
                @click="form.selected_option_id = option.id"
            >
                <div
                    class="flex size-5 shrink-0 items-center justify-center rounded-full border"
                    :class="
                        form.selected_option_id === option.id
                            ? 'border-primary bg-primary text-primary-foreground'
                            : 'border-muted-foreground/30'
                    "
                >
                    <Check
                        v-if="form.selected_option_id === option.id"
                        class="size-3"
                    />
                </div>
                <span>{{ option.body }}</span>
            </button>
        </div>

        <div class="flex items-center justify-between">
            <Button
                :disabled="form.selected_option_id === null || form.processing"
                @click="submit"
            >
                Submit answer
            </Button>
            <Button variant="ghost" size="sm" @click="skip">
                Skip exam (test)
            </Button>
        </div>
    </div>
</template>
