<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { ResultsCourseRow } from '@/types';
import { route } from 'ziggy-js';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Results', href: route('results.index') }],
    },
});

defineProps<{
    courses: ResultsCourseRow[];
}>();
</script>

<template>
    <Head title="Results" />

    <div class="flex flex-col gap-8 p-4">
        <Heading
            title="Results"
            description="How your students are doing across your courses"
        />

        <p
            v-if="courses.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            You have no courses yet.
        </p>

        <Table v-else>
            <TableHeader>
                <TableRow>
                    <TableHead>Course</TableHead>
                    <TableHead class="text-right">Students</TableHead>
                    <TableHead class="text-right">Attempts</TableHead>
                    <TableHead class="text-right">Avg score</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow
                    v-for="course in courses"
                    :key="course.id"
                    class="cursor-pointer"
                >
                    <TableCell class="font-medium">
                        <Link
                            :href="route('results.show', course.id)"
                            class="block"
                        >
                            {{ course.title }}
                        </Link>
                    </TableCell>
                    <TableCell class="text-right">
                        {{ course.students }}
                    </TableCell>
                    <TableCell class="text-right">
                        {{ course.attempts }}
                    </TableCell>
                    <TableCell class="text-right">
                        <span v-if="course.avg_score !== null">
                            {{ course.avg_score }} / 20
                        </span>
                        <span v-else class="text-muted-foreground">—</span>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
