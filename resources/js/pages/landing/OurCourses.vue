<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Star } from '@lucide/vue';
import LandingNav from '@/components/LandingNav.vue';
import { route } from 'ziggy-js';

const courses = [
    {
        title: 'Operating Systems',
        icon: '🖥️',
        lessons: 24,
        level: 'Intermediate',
    },
    {
        title: 'Data Structures & Algorithms',
        icon: '🌳',
        lessons: 30,
        level: 'Core',
    },
    {
        title: 'Computer Networks',
        icon: '🌐',
        lessons: 20,
        level: 'Intermediate',
    },
    { title: 'Databases', icon: '🗄️', lessons: 22, level: 'Core' },
    {
        title: 'Artificial Intelligence',
        icon: '🤖',
        lessons: 28,
        level: 'Advanced',
    },
    { title: 'Web Development', icon: '🕸️', lessons: 26, level: 'Beginner' },
    { title: 'Cryptography', icon: '🔐', lessons: 18, level: 'Advanced' },
    {
        title: 'Computer Graphics',
        icon: '🎨',
        lessons: 21,
        level: 'Intermediate',
    },
];

const comments = [
    {
        name: 'Ани',
        text: 'The adaptive exam found my weak spots. Scary accurate.',
        color: '#3b82f6',
        stars: 5,
    },
    {
        name: 'David',
        text: 'From “what is a pointer” to acing OS in three weeks.',
        color: '#10b981',
        stars: 5,
    },
    {
        name: 'Mariam',
        text: 'AI wrote practice questions from my lecturer’s PDF. Wild.',
        color: '#8b5cf6',
        stars: 5,
    },
    {
        name: 'Karen',
        text: 'Finally an exam that doesn’t bore the strong students.',
        color: '#f59e0b',
        stars: 4,
    },
    {
        name: 'Sofi',
        text: 'The per-topic breakdown told me exactly what to revise.',
        color: '#f43f5e',
        stars: 5,
    },
    {
        name: 'Tigran',
        text: 'Open answers graded by AI — the feedback was genuinely fair.',
        color: '#06b6d4',
        stars: 5,
    },
];

function initial(name: string): string {
    return name.charAt(0).toUpperCase();
}
</script>

<template>
    <Head title="Our courses" />
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
                class="mx-auto max-w-7xl px-8 pt-16 pb-16 text-center "
            >
                <span
                    class="text-xs font-medium tracking-[0.2em] text-white/50 uppercase"
                >
                    Our courses
                </span>
                <h1
                    class="mt-4 font-display text-5xl leading-[0.95] tracking-tight sm:text-7xl md:text-8xl"
                >
                    A catalogue that keeps moving
                </h1>
                <p class="mx-auto mt-4 max-w-xl text-white/60">
                    Dozens of AI-assisted courses across computer science — each
                    with an adaptive exam that meets you at your level.
                </p>
            </header>

            <!-- Courses marquee -->
            <div class="marquee py-2">
                <div class="track track--left">
                    <div
                        v-for="course in [...courses, ...courses]"
                        :key="`${course.title}-a`"
                        class="liquid-glass flex w-64 shrink-0 flex-col gap-3 rounded-2xl p-5"
                    >
                        <span class="text-3xl">{{ course.icon }}</span>
                        <p class="font-semibold">{{ course.title }}</p>
                        <div
                            class="flex items-center gap-2 text-xs text-white/50"
                        >
                            <span
                                class="rounded-full bg-white/10 px-2 py-0.5 text-white/70"
                            >
                                {{ course.level }}
                            </span>
                            <span>{{ course.lessons }} lessons</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Testimonials marquee (opposite direction) -->
            <div class="marquee py-2">
                <div class="track track--right">
                    <div
                        v-for="(c, i) in [...comments, ...comments]"
                        :key="`${c.name}-${i}`"
                        class="liquid-glass flex w-80 shrink-0 flex-col gap-3 rounded-2xl p-5"
                    >
                        <div class="flex items-center gap-1 text-amber-400">
                            <Star
                                v-for="n in c.stars"
                                :key="n"
                                class="size-3.5 fill-amber-400"
                            />
                        </div>
                        <p class="text-sm text-white/80">“{{ c.text }}”</p>
                        <div class="mt-1 flex items-center gap-2">
                            <span
                                class="flex size-7 items-center justify-center rounded-full text-xs font-semibold text-white"
                                :style="{ backgroundColor: c.color }"
                            >
                                {{ initial(c.name) }}
                            </span>
                            <span class="text-sm text-white/60">{{
                                c.name
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <footer
                class="mt-auto flex flex-col items-center gap-4 px-8 pt-10 pb-16"
            >
                <p class="text-sm text-white/50">
                    Find the course that fits you.
                </p>
                <div class="flex flex-wrap justify-center gap-3">
                    <Link
                        :href="route('register')"
                        class="rounded-full bg-white px-6 py-2.5 text-sm font-medium text-[#03070f] transition-transform hover:scale-[1.03]"
                    >
                        Start learning
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

<style scoped>
.marquee {
    overflow: hidden;
    -webkit-mask-image: linear-gradient(
        to right,
        transparent,
        #000 8%,
        #000 92%,
        transparent
    );
    mask-image: linear-gradient(
        to right,
        transparent,
        #000 8%,
        #000 92%,
        transparent
    );
}

.track {
    display: flex;
    width: max-content;
    gap: 1rem;
    padding-inline: 0.5rem;
}

.track--left {
    animation: marquee-scroll 42s linear infinite;
}

.track--right {
    animation: marquee-scroll 52s linear infinite reverse;
}

.marquee:hover .track {
    animation-play-state: paused;
}

@keyframes marquee-scroll {
    from {
        transform: translateX(0);
    }
    to {
        transform: translateX(-50%);
    }
}

@media (prefers-reduced-motion: reduce) {
    .track {
        animation: none;
    }
}
</style>
