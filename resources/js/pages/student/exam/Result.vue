<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import GradeBadge from '@/components/GradeBadge.vue';
import Heading from '@/components/Heading.vue';
import StatRing from '@/components/StatRing.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { ExamNav, ExamResult } from '@/types';
import { route } from 'ziggy-js';

defineOptions({
    layout: (props: { nav?: ExamNav }) => ({
        breadcrumbs: props.nav?.breadcrumbs ?? [
            { title: 'Self-check', href: route('exam.show') },
        ],
    }),
});

const props = defineProps<{
    result: ExamResult;
    nav?: ExamNav;
}>();

const scorePercent = computed(() =>
    props.result.score === null
        ? 0
        : Math.round((props.result.score / props.result.max_score) * 100),
);

function barClass(percent: number): string {
    if (percent >= 80) {
        return 'bg-emerald-500';
    }

    if (percent >= 60) {
        return 'bg-amber-500';
    }

    return 'bg-destructive';
}

const ringColor = computed(() =>
    barClass(scorePercent.value).replace('bg-', 'stroke-'),
);
</script>

<template>
    <Head title="Exam result" />

    <div class="mx-auto flex w-full max-w-2xl flex-col gap-6 p-4">
        <Heading
            title="Your result"
            description="Based on how you handled easier and harder questions."
        />

        <Card>
            <CardContent class="flex items-center gap-6 pt-6">
                <StatRing
                    :percent="scorePercent"
                    :center-top="result.score ?? '—'"
                    :center-bottom="`/ ${result.max_score}`"
                    :color-class="ringColor"
                />
                <div class="flex flex-col items-start gap-2">
                    <GradeBadge
                        :grade="result.grade"
                        :label="result.grade_label"
                    />
                    <p class="text-sm">
                        {{ result.correct }} of {{ result.total }} correct
                    </p>
                    <p class="text-sm text-muted-foreground">
                        started as {{ result.level_label }}
                    </p>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Question breakdown</CardTitle>
            </CardHeader>
            <CardContent>
                <ul class="flex flex-col divide-y">
                    <li
                        v-for="answer in result.answers"
                        :key="answer.position"
                        class="flex items-center justify-between py-2 text-sm"
                    >
                        <span class="text-muted-foreground">
                            {{ answer.position }}. {{ answer.difficulty_label }}
                        </span>
                        <span
                            :class="
                                answer.is_correct
                                    ? 'text-emerald-600 dark:text-emerald-400'
                                    : 'text-destructive'
                            "
                        >
                            {{ answer.is_correct ? 'Correct' : 'Wrong' }}
                        </span>
                    </li>
                </ul>
            </CardContent>
        </Card>

        <Card v-if="result.topics.length > 0">
            <CardHeader>
                <CardTitle>By topic</CardTitle>
            </CardHeader>
            <CardContent class="flex flex-col gap-4">
                <div
                    v-for="topic in result.topics"
                    :key="topic.title"
                    class="flex flex-col gap-1"
                >
                    <div class="flex justify-between text-sm">
                        <span class="font-medium">{{ topic.title }}</span>
                        <span class="text-muted-foreground">
                            {{ topic.correct }}/{{ topic.total }} ·
                            {{ topic.percent }}%
                        </span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-muted">
                        <div
                            class="h-full rounded-full transition-all"
                            :class="barClass(topic.percent)"
                            :style="{ width: `${topic.percent}%` }"
                        />
                    </div>
                </div>

                <p
                    v-if="result.revise.length > 0"
                    class="rounded-lg border border-dashed p-3 text-sm text-muted-foreground"
                >
                    Suggested to revise:
                    <span class="font-medium text-foreground">
                        {{ result.revise.join(', ') }}
                    </span>
                    — then retake the exam.
                </p>
            </CardContent>
        </Card>

        <div class="flex gap-3">
            <Button as-child>
                <Link :href="props.nav?.retakeUrl ?? route('exam.show')">
                    Retake exam
                </Link>
            </Button>
            <Button as-child variant="outline">
                <Link :href="props.nav?.backUrl ?? route('dashboard')">
                    {{ props.nav?.backLabel ?? 'Back to dashboard' }}
                </Link>
            </Button>
        </div>
    </div>
</template>
