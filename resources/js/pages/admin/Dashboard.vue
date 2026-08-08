<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { BookOpen, GraduationCap, Library, Users } from '@lucide/vue';
import GreetingHero from '@/components/GreetingHero.vue';
import StatCard from '@/components/StatCard.vue';
import { route } from 'ziggy-js';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: route('dashboard') }],
    },
});

defineProps<{
    stats: {
        students: number;
        lecturers: number;
        faculties: number;
        subjects: number;
        courses: number;
    };
}>();
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-6 p-4">
        <GreetingHero
            subline="Manage users, faculties, subjects and courses across the platform."
            cta-label="Manage courses"
            :cta-href="route('admin.courses.index')"
        />

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <StatCard
                label="Students"
                :value="stats.students"
                :icon="Users"
                accent="bg-blue-500/10 text-blue-500"
                :href="route('admin.users.index')"
            />
            <StatCard
                label="Lecturers"
                :value="stats.lecturers"
                :icon="GraduationCap"
                accent="bg-violet-500/10 text-violet-500"
                :href="route('admin.users.index')"
            />
            <StatCard
                label="Faculties"
                :value="stats.faculties"
                :icon="GraduationCap"
                accent="bg-emerald-500/10 text-emerald-500"
                :href="route('admin.faculties.index')"
            />
            <StatCard
                label="Subjects"
                :value="stats.subjects"
                :icon="Library"
                accent="bg-amber-500/10 text-amber-500"
                :href="route('admin.subjects.index')"
            />
            <StatCard
                label="Courses"
                :value="stats.courses"
                :icon="BookOpen"
                accent="bg-sky-500/10 text-sky-500"
                :href="route('admin.courses.index')"
            />
        </div>
    </div>
</template>
