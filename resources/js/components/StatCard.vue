<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight } from '@lucide/vue';
import type { Component } from 'vue';

defineProps<{
    label: string;
    value: number | string;
    icon: Component;
    accent?: string;
    href?: string;
}>();
</script>

<template>
    <component
        :is="href ? Link : 'div'"
        :href="href"
        class="group flex items-center gap-4 rounded-xl border bg-card p-4 transition-colors"
        :class="href ? 'hover:border-primary/40 hover:bg-muted/40' : ''"
    >
        <div
            class="flex size-10 shrink-0 items-center justify-center rounded-lg"
            :class="accent ?? 'bg-primary/10 text-primary'"
        >
            <component :is="icon" class="size-5" />
        </div>
        <div class="flex flex-col">
            <span class="text-sm text-muted-foreground">{{ label }}</span>
            <span class="text-2xl leading-tight font-semibold">{{
                value
            }}</span>
        </div>
        <ArrowUpRight
            v-if="href"
            class="ml-auto size-4 text-muted-foreground opacity-0 transition-opacity group-hover:opacity-100"
        />
    </component>
</template>
