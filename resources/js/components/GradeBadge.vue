<script setup lang="ts">
import { Badge } from '@/components/ui/badge';

// Exam grade as a labelled status chip (state, never colour alone).
defineProps<{
    grade: string | null;
    label: string | null;
}>();

function tone(grade: string | null): string {
    switch (grade) {
        case 'excellent':
            return 'bg-emerald-500/10 text-emerald-500';
        case 'good':
            return 'bg-blue-500/10 text-blue-500';
        case 'satisfactory':
            return 'bg-amber-500/10 text-amber-500';
        case 'unsatisfactory':
            return 'bg-destructive/10 text-destructive';
        default:
            return 'bg-muted text-muted-foreground';
    }
}
</script>

<template>
    <Badge variant="secondary" :class="tone(grade)">{{ label }}</Badge>
</template>
