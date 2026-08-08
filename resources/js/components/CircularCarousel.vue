<script setup lang="ts">
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import type { CSSProperties } from 'vue';
import { computed, onBeforeUnmount, ref, watch } from 'vue';

// Adapted from a 21st.dev circular carousel (React → Vue). Framer Motion is
// replaced with CSS transitions on the per-card :style; positions are computed
// from each card's offset to the active index.
export type CarouselItem = {
    id: string;
    title: string;
    description: string;
    tag?: string;
};

const props = withDefaults(
    defineProps<{
        items: CarouselItem[];
        autoPlay?: boolean;
        autoPlayInterval?: number;
    }>(),
    { autoPlay: true, autoPlayInterval: 4000 },
);

const emit = defineEmits<{
    open: [index: number];
}>();

const VISIBLE_COUNT = 5;
const RADIUS_X = 128;
const RADIUS_Y = 62;

const activeIndex = ref(0);
const isHovered = ref(false);
let timer: ReturnType<typeof setInterval> | null = null;

const total = computed(() => props.items.length);

function goTo(index: number): void {
    const t = total.value;
    activeIndex.value = ((index % t) + t) % t;
}

function next(): void {
    goTo(activeIndex.value + 1);
}

function prev(): void {
    goTo(activeIndex.value - 1);
}

function cardStyle(index: number): CSSProperties {
    const t = total.value;
    const half = Math.floor(VISIBLE_COUNT / 2);
    const offset = index - activeIndex.value;
    let adjusted = offset;

    if (offset > half) {
        adjusted = offset - t;
    }

    if (offset < -half) {
        adjusted = offset + t;
    }

    if (Math.abs(adjusted) > half * 2) {
        return {
            transform: 'translate(-50%, -50%) scale(0.8)',
            opacity: 0,
            zIndex: 0,
            pointerEvents: 'none',
        };
    }

    const angle = (adjusted / VISIBLE_COUNT) * Math.PI;
    const x = Math.sin(angle) * RADIUS_X;
    const y = -Math.cos(angle) * RADIUS_Y;
    const distance = Math.abs(adjusted);
    const maxDistance = half + 1;
    const scale = Math.max(0, 1 - (distance / maxDistance) * 0.3);
    const opacity = Math.max(0.3, 1 - (distance / maxDistance) * 0.7);

    return {
        transform: `translate(-50%, -50%) translate(${x}px, ${y}px) scale(${scale})`,
        opacity,
        zIndex: VISIBLE_COUNT - distance,
    };
}

function onCardClick(index: number): void {
    if (index === activeIndex.value) {
        emit('open', index);
    } else {
        goTo(index);
    }
}

function restartAuto(): void {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }

    if (props.autoPlay && total.value > 1 && !isHovered.value) {
        timer = setInterval(next, props.autoPlayInterval);
    }
}

watch([() => props.autoPlay, isHovered, total], restartAuto, {
    immediate: true,
});

onBeforeUnmount(() => {
    if (timer) {
        clearInterval(timer);
    }
});
</script>

<template>
    <div
        class="relative flex flex-col items-center justify-center gap-5 outline-none"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
    >
        <div class="relative h-[200px] w-full max-w-sm">
            <button
                v-for="(item, i) in items"
                :key="item.id"
                type="button"
                :style="cardStyle(i)"
                :aria-selected="i === activeIndex"
                class="absolute top-1/2 left-1/2 flex h-28 w-40 cursor-pointer flex-col items-start justify-between rounded-2xl border border-white/10 bg-gradient-to-b from-zinc-800 to-zinc-900 p-3 transition-[transform,opacity,box-shadow] duration-[650ms] [transition-timing-function:cubic-bezier(0.22,1,0.36,1)]"
                :class="
                    i === activeIndex ? 'shadow-lg' : 'shadow-sm hover:shadow-md'
                "
                @click="onCardClick(i)"
            >
                <span
                    v-if="item.tag"
                    class="rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-medium tracking-wider text-white/70 uppercase"
                >
                    {{ item.tag }}
                </span>
                <div class="w-full">
                    <h3
                        class="leading-tight font-semibold"
                        :class="
                            i === activeIndex
                                ? 'text-base text-white'
                                : 'text-sm text-white/80'
                        "
                    >
                        {{ item.title }}
                    </h3>
                    <p
                        class="mt-1 line-clamp-2 text-xs leading-relaxed"
                        :class="
                            i === activeIndex
                                ? 'text-white/60'
                                : 'text-white/40'
                        "
                    >
                        {{ item.description }}
                    </p>
                </div>
            </button>
        </div>

        <div
            class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center"
        >
            <span class="text-3xl font-bold tracking-tight text-white/90">
                {{ String(activeIndex + 1).padStart(2, '0') }}
            </span>
            <span class="mt-1 text-xs text-white/40">
                of {{ String(total).padStart(2, '0') }}
            </span>
        </div>

        <div class="flex items-center gap-4">
            <button
                type="button"
                aria-label="Previous"
                class="flex size-10 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white/70 backdrop-blur-sm transition-colors hover:bg-white/10 hover:text-white active:scale-95"
                @click="prev"
            >
                <ChevronLeft class="size-5" />
            </button>

            <div class="flex items-center gap-1.5">
                <button
                    v-for="(_, i) in items"
                    :key="i"
                    type="button"
                    :aria-label="`Go to ${i + 1}`"
                    class="h-1.5 rounded-full transition-all duration-300"
                    :class="
                        i === activeIndex
                            ? 'w-6 bg-white/80'
                            : 'w-1.5 bg-white/20 hover:bg-white/40'
                    "
                    @click="goTo(i)"
                />
            </div>

            <button
                type="button"
                aria-label="Next"
                class="flex size-10 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white/70 backdrop-blur-sm transition-colors hover:bg-white/10 hover:text-white active:scale-95"
                @click="next"
            >
                <ChevronRight class="size-5" />
            </button>
        </div>
    </div>
</template>
