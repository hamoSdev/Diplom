<script setup lang="ts">
import { computed } from 'vue';

type ColorTheme = 'orange' | 'blue' | 'purple';

type Step = {
    title: string;
    description: string;
    colorTheme?: ColorTheme;
};

const props = defineProps<{
    features?: Step[];
}>();

// Adapted from a 21st.dev "How it works" component (React → Vue). The looping
// dashed connector uses a CSS animation (.hiw-path) instead of a motion library.
const defaultFeatures: Step[] = [
    {
        title: 'Create a course',
        description:
            'A lecturer sets up a subject and a course. That bare course is the seed the AI builds on.',
        colorTheme: 'blue',
    },
    {
        title: 'AI drafts the topics',
        description:
            'If the course has no topics yet, the AI breaks the subject into a clear, ordered set of topics.',
        colorTheme: 'purple',
    },
    {
        title: 'AI writes the material',
        description:
            'For each topic the AI generates study material — or analyses the files the lecturer uploaded.',
        colorTheme: 'orange',
    },
    {
        title: 'Adaptive exam',
        description:
            'The student answers questions while the engine picks each next one from their ability — harder or easier.',
        colorTheme: 'blue',
    },
    {
        title: 'Insights & revision',
        description:
            'The AI grades the attempt, highlights weak topics and suggests exactly what to revise before a retake.',
        colorTheme: 'purple',
    },
];

const positions = [
    { className: 'md:absolute md:top-0 md:left-[15%]', rotate: 'rotate-6' },
    {
        className: 'md:absolute md:top-[120px] md:right-[15%]',
        rotate: '-rotate-6',
    },
    {
        className: 'md:absolute md:top-[450px] md:left-[15%]',
        rotate: 'rotate-6',
    },
    {
        className: 'md:absolute md:top-[570px] md:right-[10%]',
        rotate: '-rotate-6',
    },
    {
        className: 'md:absolute md:top-[850px] md:left-[15%]',
        rotate: 'rotate-6',
    },
];

const numberColor: Record<ColorTheme, string> = {
    orange: 'text-orange-400',
    blue: 'text-blue-400',
    purple: 'text-purple-400',
};
const innerBg: Record<ColorTheme, string> = {
    orange: 'bg-orange-500/10',
    blue: 'bg-blue-500/10',
    purple: 'bg-purple-500/10',
};
const innerBorder: Record<ColorTheme, string> = {
    orange: 'border-orange-500/25',
    blue: 'border-blue-500/25',
    purple: 'border-purple-500/25',
};

const data = computed(() =>
    props.features && props.features.length > 0
        ? props.features
        : defaultFeatures,
);

const heights: Record<number, number> = { 1: 400, 2: 450, 3: 800, 4: 900 };

const height = computed(() => heights[data.value.length] ?? 1130);

const pathD = computed(() => {
    let acc = '';

    for (let index = 0; index < data.value.length - 1; index++) {
        if (index === 0) {
            acc = 'M 290 150 C 500 150, 550 270, 710 270';
        } else if (index === 1) {
            acc += ' C 850 270, 500 350, 290 450';
        } else if (index === 2) {
            acc += ' C 290 600, 550 720, 750 720';
        } else if (index === 3) {
            acc += ' C 950 720, 500 800, 290 850';
        }
    }

    return acc;
});
</script>

<template>
    <div class="relative px-8 py-12 md:py-16">
        <div class="relative z-10 mx-auto max-w-6xl">
            <div
                class="relative mx-auto flex h-auto w-full max-w-[1000px] flex-col gap-8 md:block md:h-[var(--md-height)] md:gap-0"
                :style="{ '--md-height': `${height}px` }"
            >
                <svg
                    v-if="data.length > 1"
                    class="pointer-events-none absolute top-0 left-0 z-0 hidden h-full w-full md:block"
                    :viewBox="`0 0 1000 ${height}`"
                    preserveAspectRatio="none"
                >
                    <path
                        :d="pathD"
                        stroke="currentColor"
                        class="hiw-path text-white/25"
                        stroke-width="2"
                        stroke-dasharray="8 6"
                        fill="none"
                        stroke-linecap="round"
                        vector-effect="non-scaling-stroke"
                    />
                </svg>

                <div
                    v-for="(step, index) in data"
                    :key="step.title"
                    class="relative w-full transition-transform duration-300 hover:z-30 hover:scale-105 md:w-[280px]"
                    :class="[
                        positions[index % positions.length].className,
                        positions[index % positions.length].rotate,
                    ]"
                >
                    <div
                        class="rounded-[25px] border border-white/10 bg-neutral-900/80 p-2 shadow-xl backdrop-blur"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="mx-auto mb-5 h-8 w-8"
                            :class="numberColor[step.colorTheme ?? 'blue']"
                        >
                            <path
                                d="M16 3a1 1 0 0 1 .117 1.993l-.117 .007v4.764l1.894 3.789a1 1 0 0 1 .1 .331l.006 .116v2a1 1 0 0 1 -.883 .993l-.117 .007h-4v4a1 1 0 0 1 -1.993 .117l-.007 -.117v-4h-4a1 1 0 0 1 -.993 -.883l-.007 -.117v-2a1 1 0 0 1 .06 -.34l.046 -.107l1.894 -3.791v-4.762a1 1 0 0 1 -.117 -1.993l.117 -.007h8z"
                            />
                        </svg>
                        <div
                            class="flex h-full flex-col rounded-[15px] border p-4"
                            :class="[
                                innerBg[step.colorTheme ?? 'blue'],
                                innerBorder[step.colorTheme ?? 'blue'],
                            ]"
                        >
                            <span
                                class="mb-4 text-4xl font-semibold"
                                :class="numberColor[step.colorTheme ?? 'blue']"
                                style="
                                    font-family:
                                        'Comic Sans MS', 'Chalkboard SE',
                                        sans-serif;
                                "
                            >
                                0{{ index + 1 }}
                            </span>
                            <h3
                                class="mb-2 text-xl leading-tight font-semibold text-white"
                            >
                                {{ step.title }}
                            </h3>
                            <p class="text-sm tracking-tight text-white/60">
                                {{ step.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
