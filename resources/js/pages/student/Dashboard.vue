<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import CourseGrid from '@/components/CourseGrid.vue';
import DashboardHero from '@/components/DashboardHero.vue';
import DashboardOverview from '@/components/DashboardOverview.vue';
import RecentResultsList from '@/components/RecentResultsList.vue';
import TopicMasteryList from '@/components/TopicMasteryList.vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { route } from 'ziggy-js';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: route('dashboard') }],
    },
});

type Level = {
    grade_label: string | null;
    score: number | null;
    level_label: string;
};

type ResultRow = {
    id: number;
    label: string;
    grade: string | null;
    grade_label: string | null;
    score: number | null;
    date: string | null;
    url: string;
};

type TopicStat = {
    title: string;
    course: string;
    percent: number;
    url: string;
};

type CourseCard = {
    id: number;
    title: string;
    subject_name: string;
    url: string;
};

defineProps<{
    faculty: { id: number; name: string } | null;
    stats: {
        subjects: number;
        courses: number;
        courses_examined: number;
        exams_taken: number;
        avg_score: number | null;
    };
    level: Level | null;
    recentResults: ResultRow[];
    topicMastery: TopicStat[];
    courses: CourseCard[];
}>();
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-6 p-4">
        <DashboardHero :enrolled="!!faculty" :level="level" />

        <template v-if="faculty">
            <DashboardOverview
                :subjects="stats.subjects"
                :courses="stats.courses"
                :courses-examined="stats.courses_examined"
                :exams-taken="stats.exams_taken"
                :avg-score="stats.avg_score"
            />

            <div class="grid items-start gap-6 lg:grid-cols-2">
                <Card>
                    <div class="flex flex-col divide-y">
                        <div class="flex flex-col gap-3 p-6">
                            <h3 class="font-semibold">Recent results</h3>
                            <RecentResultsList
                                v-if="recentResults.length > 0"
                                :results="recentResults"
                            />
                            <p v-else class="text-sm text-muted-foreground">
                                No exam results yet.
                            </p>
                        </div>
                        <div class="flex flex-col gap-4 p-6">
                            <h3 class="font-semibold">Topic mastery</h3>
                            <TopicMasteryList
                                v-if="topicMastery.length > 0"
                                :topics="topicMastery"
                            />
                            <p v-else class="text-sm text-muted-foreground">
                                Take a course exam to see topic mastery.
                            </p>
                        </div>
                    </div>
                </Card>

                <div v-if="courses.length > 0" class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium">Continue learning</h2>
                        <Button as-child variant="ghost" size="sm">
                            <Link :href="route('learning.index')"
                                >View all</Link
                            >
                        </Button>
                    </div>
                    <CourseGrid :courses="courses" />
                </div>
            </div>
        </template>
    </div>
</template>
