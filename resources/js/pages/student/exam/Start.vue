<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { ExamLastResult, ExamNav } from '@/types';
import { route } from 'ziggy-js';

defineOptions({
    layout: (props: { nav?: ExamNav }) => ({
        breadcrumbs: props.nav?.breadcrumbs ?? [
            { title: 'Level exam', href: route('exam.show') },
        ],
    }),
});

const props = defineProps<{
    lastResult: ExamLastResult | null;
    nav?: ExamNav;
}>();

const levels = [
    {
        value: 'beginner',
        label: 'Beginner',
        description: 'Just starting out — begin with easier questions.',
    },
    {
        value: 'intermediate',
        label: 'Intermediate',
        description: 'Some solid ground — start around the middle.',
    },
    {
        value: 'advanced',
        label: 'Advanced',
        description: 'Confident already — begin with harder questions.',
    },
] as const;

const form = useForm({ start_level: 'intermediate' as string });

function begin(): void {
    form.post(props.nav?.startUrl ?? route('exam.start'));
}
</script>

<template>
    <Head :title="props.nav?.title ?? 'Level exam'" />

    <div class="mx-auto flex w-full max-w-2xl flex-col gap-6 p-4">
        <Heading
            :title="props.nav?.title ?? 'Adaptive level exam'"
            description="Pick where you think you stand. The exam adapts to each answer and reveals your real level."
        />

        <Card v-if="lastResult">
            <CardHeader>
                <CardTitle class="text-sm text-muted-foreground">
                    Your last result
                </CardTitle>
            </CardHeader>
            <CardContent class="flex items-center justify-between">
                <span class="text-lg font-medium">
                    {{ lastResult.grade_label }} · {{ lastResult.score }}/20
                </span>
                <span class="text-sm text-muted-foreground">
                    started as {{ lastResult.level_label }}
                </span>
            </CardContent>
        </Card>

        <div class="grid gap-3">
            <button
                v-for="level in levels"
                :key="level.value"
                type="button"
                class="rounded-xl border p-4 text-left transition-colors"
                :class="
                    form.start_level === level.value
                        ? 'border-primary bg-primary/5 ring-1 ring-primary'
                        : 'hover:bg-muted/50'
                "
                @click="form.start_level = level.value"
            >
                <p class="font-medium">{{ level.label }}</p>
                <p class="text-sm text-muted-foreground">
                    {{ level.description }}
                </p>
            </button>
        </div>

        <div>
            <Button :disabled="form.processing" @click="begin">
                {{ lastResult ? 'Retake exam' : 'Start exam' }}
            </Button>
        </div>
    </div>
</template>
