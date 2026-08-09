<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <!-- SkillLab: a lab beaker holding rising skill bars. Single-colour
         (currentColor) so it inherits ink/white/blue wherever it is placed. -->
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 48 48"
        :class="className"
        v-bind="$attrs"
    >
        <g
            fill="none"
            stroke="currentColor"
            stroke-width="3"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="M18 7h12" />
            <path
                d="M20 7v13L10.4 37.6A3 3 0 0 0 13 42h22a3 3 0 0 0 2.6-4.4L28 20V7"
            />
        </g>
        <g fill="currentColor">
            <rect x="16.5" y="34" width="4" height="6" rx="1.2" />
            <rect x="22" y="30" width="4" height="10" rx="1.2" />
            <rect x="27.5" y="26" width="4" height="14" rx="1.2" />
        </g>
    </svg>
</template>
