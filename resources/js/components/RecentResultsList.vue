<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import GradeBadge from '@/components/GradeBadge.vue';

type ResultRow = {
    id: number;
    label: string;
    grade: string | null;
    grade_label: string | null;
    score: number | null;
    date: string | null;
    url: string;
};

defineProps<{
    results: ResultRow[];
}>();
</script>

<template>
    <ul class="flex flex-col divide-y">
        <li v-for="result in results" :key="result.id">
            <Link
                :href="result.url"
                class="-mx-2 flex items-center justify-between gap-3 rounded-md px-2 py-3 transition-colors hover:bg-muted"
            >
                <div class="flex flex-col">
                    <span class="font-medium">{{ result.label }}</span>
                    <span class="text-xs text-muted-foreground">
                        {{ result.date }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <GradeBadge
                        :grade="result.grade"
                        :label="result.grade_label"
                    />
                    <span class="text-sm text-muted-foreground">
                        {{ result.score }}/20
                    </span>
                </div>
            </Link>
        </li>
    </ul>
</template>
