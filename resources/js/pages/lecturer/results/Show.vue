<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import GradeBadge from '@/components/GradeBadge.vue';
import Heading from '@/components/Heading.vue';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type {
    ResultsGroupSummary,
    ResultsStudentRow,
} from '@/types';
import { route } from 'ziggy-js';

defineProps<{
    course: { id: number; title: string };
    summary: ResultsGroupSummary;
    students: ResultsStudentRow[];
}>();

defineOptions({
    layout: (page: { course: { id: number; title: string } }) => ({
        breadcrumbs: [
            { title: 'Results', href: route('results.index') },
            {
                title: page.course.title,
                href: route('results.show', page.course.id),
            },
        ],
    }),
});

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
    <Head :title="`${course.title} — results`" />

    <div class="flex flex-col gap-6 p-4">
        <Heading :title="course.title" description="Group results" />

        <p
            v-if="summary.students === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            No students have taken this course exam yet.
        </p>

        <template v-else>
            <div class="grid gap-4 sm:grid-cols-3">
                <Card>
                    <CardContent class="pt-6">
                        <p class="text-sm text-muted-foreground">Students</p>
                        <p class="text-2xl font-semibold">
                            {{ summary.students }}
                        </p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6">
                        <p class="text-sm text-muted-foreground">Avg score</p>
                        <p class="text-2xl font-semibold">
                            {{ summary.avg_score ?? '—' }}
                            <span class="text-base text-muted-foreground">
                                / {{ summary.max_score }}
                            </span>
                        </p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="pt-6">
                        <p class="text-sm text-muted-foreground">Attempts</p>
                        <p class="text-2xl font-semibold">
                            {{ summary.attempts }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Grade distribution</CardTitle>
                </CardHeader>
                <CardContent class="flex flex-wrap gap-2">
                    <div
                        v-for="g in summary.grades"
                        :key="g.grade"
                        class="flex items-center gap-2 rounded-lg border px-3 py-1.5 text-sm"
                    >
                        <GradeBadge :grade="g.grade" :label="g.label" />
                        <span class="font-medium">{{ g.count }}</span>
                    </div>
                </CardContent>
            </Card>

            <Card v-if="summary.topics.length > 0">
                <CardHeader>
                    <CardTitle>Group performance by topic</CardTitle>
                </CardHeader>
                <CardContent class="flex flex-col gap-4">
                    <div
                        v-for="topic in summary.topics"
                        :key="topic.title"
                        class="flex flex-col gap-1"
                    >
                        <div class="flex justify-between text-sm">
                            <span class="font-medium">{{ topic.title }}</span>
                            <span class="text-muted-foreground">
                                {{ topic.correct }}/{{ topic.total }} ·
                                {{ topic.percent }}%
                            </span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-muted">
                            <div
                                class="h-full rounded-full transition-all"
                                :class="barClass(topic.percent)"
                                :style="{ width: `${topic.percent}%` }"
                            />
                        </div>
                    </div>

                    <p
                        v-if="summary.revise.length > 0"
                        class="rounded-lg border border-dashed p-3 text-sm text-muted-foreground"
                    >
                        Weakest topics for the group:
                        <span class="font-medium text-foreground">
                            {{ summary.revise.join(', ') }}
                        </span>
                    </p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Students</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Student</TableHead>
                                <TableHead>Level</TableHead>
                                <TableHead class="text-right">Score</TableHead>
                                <TableHead>Grade</TableHead>
                                <TableHead>Date</TableHead>
                                <TableHead class="text-right">Tries</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="student in students"
                                :key="student.attempt_id"
                                class="cursor-pointer"
                            >
                                <TableCell class="font-medium">
                                    <Link
                                        :href="
                                            route(
                                                'results.attempt',
                                                student.attempt_id,
                                            )
                                        "
                                        class="block"
                                    >
                                        {{ student.name }}
                                    </Link>
                                </TableCell>
                                <TableCell>{{ student.level_label }}</TableCell>
                                <TableCell class="text-right">
                                    {{ student.score ?? '—' }}
                                </TableCell>
                                <TableCell>
                                    <GradeBadge
                                        :grade="student.grade"
                                        :label="student.grade_label"
                                    />
                                </TableCell>
                                <TableCell>
                                    {{ student.finished_at ?? '—' }}
                                </TableCell>
                                <TableCell class="text-right">
                                    {{ student.attempts_count }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </template>
    </div>
</template>
