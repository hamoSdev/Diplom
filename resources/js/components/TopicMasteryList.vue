<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

type TopicStat = {
    title: string;
    course: string;
    percent: number;
    url: string;
};

defineProps<{
    topics: TopicStat[];
}>();

// Status encoding (state, not identity); every bar carries a % label so meaning
// never rests on colour alone.
function barClass(percent: number): string {
    if (percent >= 80) {
        return 'bg-emerald-500';
    }

    if (percent >= 60) {
        return 'bg-amber-500';
    }

    return 'bg-destructive';
}
</script>

<template>
    <div class="flex flex-col gap-4">
        <Link
            v-for="(topic, index) in topics"
            :key="index"
            :href="topic.url"
            class="-mx-2 flex flex-col gap-1.5 rounded-md px-2 py-1 transition-colors hover:bg-muted"
        >
            <div class="flex items-baseline justify-between gap-3">
                <span class="truncate text-sm">
                    <span class="font-medium">{{ topic.title }}</span>
                    <span class="text-xs text-muted-foreground">
                        · {{ topic.course }}
                    </span>
                </span>
                <span class="text-sm text-muted-foreground">
                    {{ topic.percent }}%
                </span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-muted">
                <div
                    class="h-full rounded-full transition-all"
                    :class="barClass(topic.percent)"
                    :style="{ width: `${Math.max(topic.percent, 3)}%` }"
                />
            </div>
        </Link>
    </div>
</template>
