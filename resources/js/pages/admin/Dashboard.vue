<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    BookOpen,
    ClipboardCheck,
    Gauge,
    GraduationCap,
    Library,
    UserCheck,
    Users,
} from '@lucide/vue';
import GradeBadge from '@/components/GradeBadge.vue';
import GreetingHero from '@/components/GreetingHero.vue';
import StatCard from '@/components/StatCard.vue';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
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
    activity: {
        exams_taken: number;
        active_students: number;
        avg_score: number | null;
    };
    grades: { grade: string; label: string; count: number }[];
    weakTopics: { title: string; course: string; percent: number }[];
}>();

function barClass(percent: number): string {
    if (percent >= 80) {
        return 'bg-emerald-500';
    }

    if (percent >= 60) {
        return 'bg-amber-500';
    }

    return 'bg-destructive';
}
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

        <h2 class="mt-2 text-lg font-semibold">Learning activity</h2>

        <div class="grid gap-4 sm:grid-cols-3">
            <StatCard
                label="Exams taken"
                :value="activity.exams_taken"
                :icon="ClipboardCheck"
                accent="bg-indigo-500/10 text-indigo-500"
            />
            <StatCard
                label="Avg score"
                :value="
                    activity.avg_score === null
                        ? '—'
                        : `${activity.avg_score} / 20`
                "
                :icon="Gauge"
                accent="bg-teal-500/10 text-teal-500"
            />
            <StatCard
                label="Active students"
                :value="activity.active_students"
                :icon="UserCheck"
                accent="bg-rose-500/10 text-rose-500"
            />
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>Grade distribution</CardTitle>
                </CardHeader>
                <CardContent class="flex flex-wrap gap-2">
                    <div
                        v-for="g in grades"
                        :key="g.grade"
                        class="flex items-center gap-2 rounded-lg border px-3 py-1.5 text-sm"
                    >
                        <GradeBadge :grade="g.grade" :label="g.label" />
                        <span class="font-medium">{{ g.count }}</span>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Weakest topics platform-wide</CardTitle>
                </CardHeader>
                <CardContent>
                    <p
                        v-if="weakTopics.length === 0"
                        class="text-sm text-muted-foreground"
                    >
                        No exam data yet.
                    </p>
                    <div v-else class="flex flex-col gap-4">
                        <div
                            v-for="topic in weakTopics"
                            :key="`${topic.course}-${topic.title}`"
                            class="flex flex-col gap-1"
                        >
                            <div class="flex justify-between gap-3 text-sm">
                                <span class="min-w-0 truncate font-medium">
                                    {{ topic.title }}
                                    <span class="text-muted-foreground">
                                        · {{ topic.course }}
                                    </span>
                                </span>
                                <span class="shrink-0 text-muted-foreground">
                                    {{ topic.percent }}%
                                </span>
                            </div>
                            <div
                                class="h-2 overflow-hidden rounded-full bg-muted"
                            >
                                <div
                                    class="h-full rounded-full transition-all"
                                    :class="barClass(topic.percent)"
                                    :style="{ width: `${topic.percent}%` }"
                                />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
