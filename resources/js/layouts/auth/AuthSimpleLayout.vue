<script setup lang="ts">
import LandingNav from '@/components/LandingNav.vue';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="dark relative min-h-svh bg-[#002b42] font-sans text-white">
        <img
            src="/images/auth-bg.jpg"
            alt=""
            class="fixed inset-0 h-full w-full object-cover"
        />
        <div class="fixed inset-0 bg-black/25"></div>

        <div class="relative z-10 flex min-h-svh flex-col">
            <div class="w-full">
                <LandingNav />
            </div>

            <div class="flex flex-1 items-center justify-center p-6 md:p-10">
                <div class="w-full max-w-sm">
                    <div
                        class="flex flex-col gap-8 rounded-xl border bg-card p-8 text-card-foreground shadow-sm"
                    >
                        <div class="space-y-1.5 text-center">
                            <h1 class="text-xl font-medium text-white">
                                {{ title }}
                            </h1>
                            <p class="text-sm text-white/60">
                                {{ description }}
                            </p>
                        </div>
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
