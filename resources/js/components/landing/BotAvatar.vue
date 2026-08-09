<script setup lang="ts">
// A cute little teaching-bot avatar. Single SVG, coloured by prop so each
// "lecturer" gets a distinct friendly robot.
defineProps<{
    color: string;
}>();
</script>

<template>
    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- antenna -->
        <line
            x1="32"
            y1="13"
            x2="32"
            y2="6"
            :stroke="color"
            stroke-width="3"
            stroke-linecap="round"
        />
        <circle cx="32" cy="5" r="3" :fill="color" />
        <!-- side knobs -->
        <circle cx="8" cy="33" r="3" :fill="color" />
        <circle cx="56" cy="33" r="3" :fill="color" />
        <!-- head -->
        <rect x="11" y="13" width="42" height="40" rx="13" :fill="color" />
        <!-- face screen -->
        <rect x="17" y="21" width="30" height="24" rx="9" fill="#0b1220" />
        <!-- eyes -->
        <circle cx="26" cy="31" r="3.4" fill="#ffffff" />
        <circle cx="38" cy="31" r="3.4" fill="#ffffff" />
        <!-- smile -->
        <path
            d="M25 38 q7 5 14 0"
            fill="none"
            stroke="#ffffff"
            stroke-width="2.4"
            stroke-linecap="round"
        />
    </svg>
</template>
