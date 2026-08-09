<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ChevronDown } from '@lucide/vue';
import LandingNav from '@/components/LandingNav.vue';

import { route } from 'ziggy-js';

const faqs = [
    {
        q: 'What is SkillLab?',
        a: 'SkillLab is an AI-powered adaptive learning and assessment platform. Lecturers build courses, the AI helps generate material and questions, and every exam adapts to the individual student to reveal their real level.',
    },
    {
        q: 'What makes the exams “adaptive”?',
        a: 'The exam reads your answers as you go. Answer correctly and the next question gets harder; slip up and it eases off. So every student sits a personalised exam tuned to their ability — not a one-size-fits-all test.',
    },
    {
        q: 'How is my final grade calculated?',
        a: 'On a 20-point scale, weighted by question difficulty: 18–20 excellent, 13–17 good, 8–12 satisfactory, 1–7 unsatisfactory. Harder questions carry more weight, so tackling tough ones pays off.',
    },
    {
        q: 'What does the AI actually do?',
        a: 'It can outline a course into topics, write study material for each, generate a question bank tagged by difficulty, and grade open-ended answers by meaning — not just keywords. Lecturers stay in full control and can edit everything.',
    },
    {
        q: 'What kinds of questions will I get?',
        a: 'Single choice, multiple choice, true / false, and open-ended written answers. Open answers are graded by AI against the model answer, so wording differences are handled fairly.',
    },
    {
        q: 'Can I practise and retake?',
        a: 'Yes. Each topic has a free self-check you can retry as often as you like, and you can retake a course exam — especially handy for the weak topics the results screen flags for revision.',
    },
    {
        q: 'How do I become a lecturer?',
        a: 'Head to “Become a lecturer”, tell us about yourself and attach a short CV. An administrator reviews the application and sets up your teaching account.',
    },
    {
        q: 'Do I need to install anything?',
        a: 'No. SkillLab runs entirely in your browser — sign in and start learning. Nothing to download, on any device.',
    },
];
</script>

<template>
    <Head title="FAQ" />

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
                <p class="mx-auto mt-4 max-w-xl text-white/60">
                    Everything you might want to know about how SkillLab teaches
                    and assesses.
                </p>
            </header>

            <div class="mx-auto w-full max-w-2xl px-6 pb-16 sm:px-8">
                <div class="flex flex-col gap-3">
                    <details
                        v-for="faq in faqs"
                        :key="faq.q"
                        class="faq liquid-glass group rounded-2xl px-5"
                    >
                        <summary
                            class="flex cursor-pointer list-none items-center justify-between gap-4 py-4 font-medium"
                        >
                            {{ faq.q }}
                            <ChevronDown
                                class="size-5 shrink-0 text-white/60 transition-transform duration-200 group-open:rotate-180"
                            />
                        </summary>
                        <p class="pb-5 text-sm leading-relaxed text-white/60">
                            {{ faq.a }}
                        </p>
                    </details>
                </div>
            </div>

            <footer class="mt-auto flex flex-col items-center gap-4 px-8 pb-16">
                <p class="text-sm text-white/50">Still curious?</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <Link
                        :href="route('register')"
                        class="rounded-full bg-white px-6 py-2.5 text-sm font-medium text-[#03070f] transition-transform hover:scale-[1.03]"
                    >
                        Try it yourself
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
.faq summary::-webkit-details-marker {
    display: none;
}
</style>
