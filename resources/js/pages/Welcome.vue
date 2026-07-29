<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import LandingNav from '@/components/LandingNav.vue';
import { route } from 'ziggy-js';

const page = usePage();
const startHref = computed(() =>
    page.props.auth.user ? route('dashboard') : route('register'),
);

const video = ref<HTMLVideoElement | null>(null);

onMounted(() => {
    if (video.value) {
        video.value.muted = true;
    }
});
</script>

<template>
    <Head title="Velorah" />

    <div
        class="relative min-h-screen overflow-hidden bg-[#002b42] font-sans text-white"
    >
        <video
            ref="video"
            class="absolute inset-0 z-0 h-full w-full object-cover"
            autoplay
            loop
            muted
            playsinline
            src="/videos/hero.mp4"
        ></video>

        <LandingNav />

        <section
            class="relative z-10 flex flex-col items-center px-6 pt-32 pb-40 text-center"
        >
            <h1
                class="animate-fade-rise max-w-7xl font-display text-5xl leading-[0.95] font-normal tracking-[-2.46px] sm:text-7xl md:text-8xl"
            >
                Learning that
                <em class="text-white/50 not-italic">adapts</em> to
                <em class="text-white/50 not-italic">every mind.</em>
            </h1>

            <p
                class="animate-fade-rise-delay mt-8 max-w-2xl text-base leading-relaxed text-white/60 sm:text-lg"
            >
                An adaptive platform where AI builds the course, tailors every
                exam to the student's level, and turns each answer into
                personalized feedback.
            </p>

            <Link
                :href="startHref"
                class="liquid-glass animate-fade-rise-delay-2 mt-12 rounded-full px-14 py-5 text-base text-white transition-transform hover:scale-[1.03]"
            >
                Begin Journey
            </Link>
        </section>
    </div>
</template>
