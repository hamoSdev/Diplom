<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Check, Rocket, Sprout, TrendingUp } from '@lucide/vue';
import type { Component } from 'vue';
import GradeBadge from '@/components/GradeBadge.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import type { ExamLastResult, ExamNav } from '@/types';
import { route } from 'ziggy-js';

defineOptions({
    layout: (props: { nav?: ExamNav }) => ({
        breadcrumbs: props.nav?.breadcrumbs ?? [
            { title: 'Self-check', href: route('exam.show') },
        ],
    }),
});

const props = defineProps<{
    lastResult: ExamLastResult | null;
    nav?: ExamNav;
}>();

const levels: {
    value: string;
    label: string;
    description: string;
    icon: Component;
}[] = [
    {
        value: 'beginner',
        label: 'Beginner',
        description: 'Just starting out — begin with easier questions.',
        icon: Sprout,
    },
    {
        value: 'intermediate',
        label: 'Intermediate',
        description: 'Some solid ground — start around the middle.',
        icon: TrendingUp,
    },
    {
        value: 'advanced',
        label: 'Advanced',
        description: 'Confident already — begin with harder questions.',
        icon: Rocket,
    },
];

const form = useForm({ start_level: 'intermediate' as string });

function begin(): void {
    form.post(props.nav?.startUrl ?? route('exam.start'));
}
</script>

<template>
    <Head :title="props.nav?.title ?? 'Self-check'" />

    <div class="mx-auto flex w-full max-w-2xl flex-col gap-6 p-4">
        <Heading
            :title="props.nav?.title ?? 'Knowledge self-check'"
            description="An AI-powered adaptive check of your knowledge. Pick where you think you stand — it adapts to each answer and reveals your real level."
        />

        <Card v-if="lastResult">
            <CardContent
                class="flex flex-wrap items-center justify-between gap-3 pt-6"
            >
                <div class="flex items-center gap-3">
                    <span class="text-sm text-muted-foreground">
                        Last result
                    </span>
                    <GradeBadge
                        :grade="lastResult.grade"
                        :label="lastResult.grade_label"
                    />
                    <span class="text-sm font-medium">
                        {{ lastResult.score }}/20
                    </span>
                </div>
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
                class="flex items-center gap-4 rounded-xl border p-4 text-left transition-colors"
                :class="
                    form.start_level === level.value
                        ? 'border-primary bg-primary/5 ring-1 ring-primary'
                        : 'hover:border-primary/40 hover:bg-muted/40'
                "
                @click="form.start_level = level.value"
            >
                <div
                    class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                >
                    <component :is="level.icon" class="size-5" />
                </div>
                <div class="flex-1">
                    <p class="font-medium">{{ level.label }}</p>
                    <p class="text-sm text-muted-foreground">
                        {{ level.description }}
                    </p>
                </div>
                <div
                    class="flex size-5 shrink-0 items-center justify-center rounded-full border"
                    :class="
                        form.start_level === level.value
                            ? 'border-primary bg-primary text-primary-foreground'
                            : 'border-muted-foreground/30'
                    "
                >
                    <Check
                        v-if="form.start_level === level.value"
                        class="size-3"
                    />
                </div>
            </button>
        </div>

        <div>
            <Button :disabled="form.processing" @click="begin">
                {{ lastResult ? 'Retake exam' : 'Start exam' }}
            </Button>
        </div>
    </div>
</template>
