<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

// A titled card section. `flush` removes content padding so a table can sit
// edge to edge inside the card.
defineProps<{
    title?: string;
    flush?: boolean;
}>();
</script>

<template>
    <Card class="overflow-hidden">
        <CardHeader v-if="title">
            <CardTitle class="text-base ">{{ title }}</CardTitle>
        </CardHeader>
        <CardContent :class="flush ? 'p-0' : ''">
            <slot />
        </CardContent>
    </Card>
</template>
