<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight } from '@lucide/vue';
import { computed } from 'vue';
import StatRing from '@/components/StatRing.vue';
import { Button } from '@/components/ui/button';
import { route } from 'ziggy-js';

const props = defineProps<{
    subjects: number;
    courses: number;
    coursesExamined: number;
    examsTaken: number;
    avgScore: number | null;
}>();

const progressPercent = computed(() =>
    props.courses === 0
        ? 0
        : Math.round((props.coursesExamined / props.courses) * 100),
);

const scorePercent = computed(() =>
    props.avgScore === null ? 0 : Math.round((props.avgScore / 20) * 100),
);

const scoreColor = computed(() => {
    if (scorePercent.value >= 80) {
        return 'stroke-emerald-500';
    }

    if (scorePercent.value >= 60) {
        return 'stroke-amber-500';
    }

    return 'stroke-destructive';
});
</script>

<template>
    <div
        class="grid gap-6 rounded-xl border bg-card p-6 sm:grid-cols-2 sm:gap-0"
    >
        <div class="flex items-center gap-5 sm:pr-8">
            <StatRing
                :percent="progressPercent"
                :center-top="`${coursesExamined}/${courses}`"
                center-bottom="courses"
                color-class="stroke-primary"
            />
            <div class="flex flex-col gap-1">
                <span
                    class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                >
                    Learning
                </span>
                <p class="text-sm">
                    <span class="font-semibold">{{ subjects }}</span> subjects ·
                    <span class="font-semibold">{{ courses }}</span> courses
                </p>
                <Button as-child variant="outline" size="sm" class="mt-2 w-fit">
                    <Link :href="route('learning.index')">
                        Browse courses
                        <ArrowRight class="size-4" />
                    </Link>
                </Button>
            </div>
        </div>

        <div
            class="flex items-center gap-5 border-t pt-6 sm:border-t-0 sm:border-l sm:pt-0 sm:pl-8"
        >
            <StatRing
                :percent="scorePercent"
                :center-top="avgScore ?? '—'"
                center-bottom="/ 20"
                :color-class="scoreColor"
            />
            <div class="flex flex-col gap-1">
                <span
                    class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                >
                    Performance
                </span>
                <p class="text-sm">
                    <span class="font-semibold">{{ examsTaken }}</span> exams
                    taken
                </p>
                <p class="text-sm text-muted-foreground">
                    Average across course exams
                </p>
            </div>
        </div>
    </div>
</template>
