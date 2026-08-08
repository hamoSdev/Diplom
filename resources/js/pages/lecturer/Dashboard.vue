<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    BookOpen,
    HelpCircle,
    Library,
    ListChecks,
    PenLine,
} from '@lucide/vue';
import GreetingHero from '@/components/GreetingHero.vue';
import StatCard from '@/components/StatCard.vue';
import { Button } from '@/components/ui/button';
import { route } from 'ziggy-js';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: route('dashboard') }],
    },
});

defineProps<{
    stats: {
        courses: number;
        subjects: number;
        topics: number;
        questions: number;
    };
}>();
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-6 p-4">
        <GreetingHero
            subline="Your teaching at a glance — build courses, topics and question banks."
            cta-label="My courses"
            :cta-href="route('courses.index')"
        />

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <StatCard
                label="Courses"
                :value="stats.courses"
                :icon="BookOpen"
                accent="bg-blue-500/10 text-blue-500"
                :href="route('courses.index')"
            />
            <StatCard
                label="Subjects"
                :value="stats.subjects"
                :icon="Library"
                accent="bg-violet-500/10 text-violet-500"
            />
            <StatCard
                label="Topics"
                :value="stats.topics"
                :icon="ListChecks"
                accent="bg-emerald-500/10 text-emerald-500"
            />
            <StatCard
                label="Questions"
                :value="stats.questions"
                :icon="HelpCircle"
                accent="bg-amber-500/10 text-amber-500"
            />
        </div>

        <div class="flex gap-3">
            <Button as-child variant="outline">
                <Link :href="route('editor.index')">
                    <PenLine class="size-4" />
                    Open editor
                </Link>
            </Button>
        </div>
    </div>
</template>
