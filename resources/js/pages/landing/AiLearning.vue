<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import HowItWorks from '@/components/landing/HowItWorks.vue';
import LandingNav from '@/components/LandingNav.vue';
import { route } from 'ziggy-js';
</script>

<template>
    <Head title="AI learning" />

    <div class="relative min-h-screen bg-[#002b42] font-sans text-white">
        <img
            src="/images/auth-bg.jpg"
            alt=""
            class="fixed inset-0 h-full w-full object-cover"
        />
        <div class="fixed inset-0 bg-black/40"></div>

        <div class="relative z-10">
            <LandingNav />

            <header
                class="mx-auto max-w-3xl px-8 pt-10 pb-4 text-center sm:pt-16"
            >
                <span
                    class="text-xs font-medium tracking-[0.2em] text-white/50 uppercase"
                >
                    AI learning
                </span>
                <h1
                    class="mt-4 font-display text-4xl tracking-tight sm:text-6xl"
                >
                    How AI learning works
                </h1>
                <p class="mx-auto mt-4 max-w-xl text-white/60">
                    From a bare course to a personalised, adaptive path — here
                    is what the AI does at every step of the journey.
                </p>
            </header>

            <HowItWorks />

            <footer class="flex flex-col items-center gap-4 px-8 pt-4 pb-20">
                <p class="text-sm text-white/50">Ready to see it in action?</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <Link
                        :href="route('register')"
                        class="rounded-full bg-white px-6 py-2.5 text-sm font-medium text-[#03070f] transition-transform hover:scale-[1.03]"
                    >
                        Begin journey
                    </Link>
                    <Link
                        :href="route('home')"
                        class="liquid-glass rounded-full px-6 py-2.5 text-sm text-white transition-transform hover:scale-[1.03]"
                    >
                        ← Back to home
                    </Link>
                </div>
            </footer>
        </div>
    </div>
</template>
