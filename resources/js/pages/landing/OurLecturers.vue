<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Star } from '@lucide/vue';
import BotAvatar from '@/components/landing/BotAvatar.vue';
import LandingNav from '@/components/LandingNav.vue';
import { route } from 'ziggy-js';

const lecturers = [
    {
        name: 'Dr. Nova Byte',
        field: 'Operating Systems',
        bio: 'Turns kernels and schedulers into bedtime stories.',
        color: '#6366f1',
        courses: 12,
        rating: 4.9,
    },
    {
        name: 'Ada Circuit',
        field: 'Algorithms & Data Structures',
        bio: 'Big-O optimist. Will race your brute force any day.',
        color: '#3b82f6',
        courses: 9,
        rating: 4.8,
    },
    {
        name: 'Max Kernel',
        field: 'Computer Networks',
        bio: 'Explains TCP handshakes with actual handshakes.',
        color: '#06b6d4',
        courses: 7,
        rating: 4.7,
    },
    {
        name: 'Lina Vector',
        field: 'Databases',
        bio: 'Normalises everything, including small talk.',
        color: '#10b981',
        courses: 10,
        rating: 4.9,
    },
    {
        name: 'Neo Turing',
        field: 'Artificial Intelligence',
        bio: 'Passed the Turing test on the second try.',
        color: '#8b5cf6',
        courses: 14,
        rating: 5.0,
    },
    {
        name: 'Ruby Stack',
        field: 'Web Development',
        bio: 'Ships to prod on Fridays. Fearlessly.',
        color: '#f43f5e',
        courses: 11,
        rating: 4.6,
    },
    {
        name: 'Sam Quantum',
        field: 'Cryptography',
        bio: 'Keeps secrets — especially the exam answers.',
        color: '#f59e0b',
        courses: 6,
        rating: 4.8,
    },
    {
        name: 'Iris Pixel',
        field: 'Computer Graphics',
        bio: 'Renders sunsets faster than your GPU.',
        color: '#14b8a6',
        courses: 8,
        rating: 4.7,
    },
];
</script>

<template>
     <Head title="Our Lecturers" />

    <div class="relative min-h-screen bg-[#002b42] font-sans text-white">
        <img
            src="/images/auth-bg.jpg"
            alt=""
            class="fixed inset-0 h-full w-full object-cover"
        />
        <div class="fixed inset-0 bg-black/40"></div>

        <div class="relative z-10">
            <LandingNav />

    
        <header class="mx-auto max-w-3xl px-16 pt-16 pb-4 text-center ">
            <span
                class="text-xs font-medium tracking-[0.2em] text-white/50 uppercase"
            >
                Our lecturers
            </span>
            <h1 class="mt-4 font-display text-4xl tracking-tight sm:text-6xl">
                Meet the minds (and bots)
            </h1>
            <p class="mx-auto mt-4 max-w-xl text-white/60">
                A hand-picked crew of specialists — each backed by AI that
                builds their courses, drafts questions and grades fairly.
            </p>
        </header>

        <div class="mx-auto w-full max-w-6xl px-6 py-16 sm:px-8">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="lecturer in lecturers"
                    :key="lecturer.name"
                    class="liquid-glass flex flex-col items-center gap-3 rounded-2xl p-6 text-center"
                >
                    <div
                        class="flex size-20 items-center justify-center rounded-full"
                        :style="{ backgroundColor: `${lecturer.color}22` }"
                    >
                        <BotAvatar :color="lecturer.color" class="size-14" />
                    </div>
                    <div>
                        <p class="font-semibold">{{ lecturer.name }}</p>
                        <p class="text-sm" :style="{ color: lecturer.color }">
                            {{ lecturer.field }}
                        </p>
                    </div>
                    <p class="text-sm text-white/60">{{ lecturer.bio }}</p>
                    <div
                        class="mt-auto flex items-center gap-4 pt-2 text-xs text-white/50"
                    >
                        <span>{{ lecturer.courses }} courses</span>
                        <span class="flex items-center gap-1">
                            <Star
                                class="size-3.5 fill-amber-400 text-amber-400"
                            />
                            {{ lecturer.rating.toFixed(1) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <footer class="mt-auto flex flex-col items-center gap-4 px-8 pb-16">
            <p class="text-sm text-white/50">Want to teach with SkillLab?</p>
            <div class="flex flex-wrap justify-center gap-3">
                <Link
                    :href="route('become-lecturer')"
                    class="rounded-full bg-white px-6 py-2.5 text-sm font-medium text-[#03070f] transition-transform hover:scale-[1.03]"
                >
                    Become a lecturer
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
