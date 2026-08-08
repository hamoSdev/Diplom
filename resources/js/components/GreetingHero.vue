<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, GraduationCap, Sparkles } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';

defineProps<{
    subline: string;
    ctaLabel?: string;
    ctaHref?: string;
}>();

const name = computed(() => usePage().props.auth.user.name);
const initial = computed(() => name.value.charAt(0).toUpperCase());

const greeting = computed(() => {
    const hour = new Date().getHours();

    if (hour < 12) {
        return 'Good morning';
    }

    if (hour < 18) {
        return 'Good afternoon';
    }

    return 'Good evening';
});
</script>

<template>
    <section
        class="dashboard-hero relative overflow-hidden rounded-2xl p-6 text-white shadow-sm sm:p-8"
    >
        <div class="relative z-10 flex flex-col gap-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex flex-col gap-1.5">
                    <span
                        class="text-xs font-medium tracking-wider text-white/70 uppercase"
                    >
                        {{ greeting }}
                    </span>
                    <h1 class="text-2xl font-semibold sm:text-3xl">
                        {{ name }}
                    </h1>
                    <p class="max-w-md text-sm text-white/80">{{ subline }}</p>
                </div>
                <div
                    class="flex size-12 shrink-0 items-center justify-center rounded-full bg-white/15 text-lg font-semibold backdrop-blur"
                >
                    {{ initial }}
                </div>
            </div>

            <div v-if="ctaHref">
                <Button
                    as-child
                    variant="secondary"
                    class="bg-white text-primary hover:bg-white/90"
                >
                    <Link :href="ctaHref">
                        {{ ctaLabel }}
                        <ArrowRight class="size-4" />
                    </Link>
                </Button>
            </div>
        </div>

        <Sparkles
            class="pointer-events-none absolute -top-4 right-24 size-16 text-white/10"
        />
        <GraduationCap
            class="pointer-events-none absolute -right-6 -bottom-8 size-44 text-white/10"
        />
    </section>
</template>
