<script setup lang="ts">
// Radial gauge: a magnitude shown as an arc, with the value in the centre so
// meaning never rests on colour alone. `colorClass` is a Tailwind stroke-* class.
defineProps<{
    percent: number;
    centerTop: string | number;
    centerBottom: string;
    colorClass: string;
}>();
</script>

<template>
    <div class="relative shrink-0">
        <svg viewBox="0 0 36 36" class="size-24 -rotate-90">
            <circle
                cx="18"
                cy="18"
                r="15.5"
                fill="none"
                class="stroke-muted"
                stroke-width="3.5"
            />
            <circle
                cx="18"
                cy="18"
                r="15.5"
                fill="none"
                stroke-width="3.5"
                stroke-linecap="round"
                path-length="100"
                :class="colorClass"
                :stroke-dasharray="`${percent} ${100 - percent}`"
            />
        </svg>
        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span class="text-xl font-semibold">{{ centerTop }}</span>
            <span class="text-[10px] text-muted-foreground">
                {{ centerBottom }}
            </span>
        </div>
    </div>
</template>
