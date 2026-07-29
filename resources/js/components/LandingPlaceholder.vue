<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

defineProps<{
    title: string;
}>();
</script>

<template>
    <Head :title="title" />

    <div
        class="relative flex min-h-screen flex-col items-center justify-center bg-[#002b42] px-6 text-center font-sans text-white"
    >
        <img
            src="/images/auth-bg.jpg"
            alt=""
            class="absolute inset-0 h-full w-full object-cover"
        />
        <div class="absolute inset-0 bg-black/40"></div>

        <div class="relative z-10 flex flex-col items-center gap-6">
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
</template>
