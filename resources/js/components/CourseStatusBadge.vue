<script setup lang="ts">
import { Badge } from '@/components/ui/badge';

// Course status as a labelled status chip (state, never colour alone).
defineProps<{
    status: string;
    label: string;
}>();

function tone(status: string): string {
    switch (status) {
        case 'ready':
            return 'bg-emerald-500/10 text-emerald-500';
        case 'generating':
            return 'bg-blue-500/10 text-blue-500';
        case 'failed':
            return 'bg-destructive/10 text-destructive';
        default:
            return 'bg-muted text-muted-foreground';
    }
}
</script>

<template>
    <Badge variant="secondary" :class="tone(status)">{{ label }}</Badge>
</template>
