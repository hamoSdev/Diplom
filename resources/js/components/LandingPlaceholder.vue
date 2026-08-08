<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LandingNav from '@/components/LandingNav.vue';
import { route } from 'ziggy-js';

defineProps<{
    title: string;
}>();
</script>

<template>
    <Head :title="title" />

    <div class="relative min-h-screen bg-[#002b42] font-sans text-white">
        <img
            src="/images/auth-bg.jpg"
            alt=""
            class="fixed inset-0 h-full w-full object-cover"
        />
        <div class="fixed inset-0 bg-black/40"></div>

        <div class="relative z-10 flex min-h-screen flex-col">
            <div class="w-full">
                <LandingNav />
            </div>

            <div
                class="flex flex-1 flex-col items-center justify-center gap-6 px-6 text-center"
            >
                <h1 class="font-display text-5xl tracking-tight sm:text-6xl">
                    {{ title }}
                </h1>
                <p class="text-white/60">This page is coming soon.</p>
                <Link
                    :href="route('home')"
                    class="liquid-glass rounded-full px-6 py-2.5 text-sm text-white transition-transform hover:scale-[1.03]"
                >
                    ← Back to home
                </Link>
            </div>
        </div>
    </div>
</template>
