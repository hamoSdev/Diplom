<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, watch } from 'vue';
import CourseStatusBadge from '@/components/CourseStatusBadge.vue';
import Heading from '@/components/Heading.vue';
import {
    Card,
    CardContent,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { route } from 'ziggy-js';

type StatusCount = { status: string; label: string; count: number };

type CourseRow = {
    id: number;
    title: string;
    lecturer: string;
    subject: string | null;
    status: string;
    status_label: string;
    topics_count: number;
    questions_count: number;
    materials_count: number;
    updated_at: string | null;
};

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'AI monitor', href: route('admin.ai.index') }],
    },
});

const props = defineProps<{
    summary: StatusCount[];
    courses: CourseRow[];
}>();

const isGenerating = computed(() =>
    props.courses.some((course) => course.status === 'generating'),
);

// Refresh while a generation is in flight so the board tracks it live.
let poll: ReturnType<typeof setInterval> | null = null;

function stopPoll(): void {
    if (poll !== null) {
        clearInterval(poll);
        poll = null;
    }
}

watch(
    isGenerating,
    (generating) => {
        if (generating && poll === null) {
            poll = setInterval(() => router.reload(), 5000);
        } else if (!generating) {
            stopPoll();
        }
    },
    { immediate: true },
);

onBeforeUnmount(stopPoll);
</script>

<template>
    <Head title="AI monitor" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="AI monitor"
            description="Generation status of every course across the platform"
        />

        <Card>
            <CardContent class="flex flex-wrap gap-2 pt-6">
                <div
                    v-for="s in summary"
                    :key="s.status"
                    class="flex items-center gap-2 rounded-lg border px-3 py-1.5 text-sm"
                >
                    <CourseStatusBadge :status="s.status" :label="s.label" />
                    <span class="font-medium">{{ s.count }}</span>
                </div>
            </CardContent>
        </Card>

        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Course</TableHead>
                    <TableHead>Lecturer</TableHead>
                    <TableHead>Subject</TableHead>
                    <TableHead class="text-right">
                        Topics / Questions / Files
                    </TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Updated</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="course in courses" :key="course.id">
                    <TableCell class="font-medium">{{ course.title }}</TableCell>
                    <TableCell>{{ course.lecturer }}</TableCell>
                    <TableCell>{{ course.subject ?? '—' }}</TableCell>
                    <TableCell class="text-right text-muted-foreground">
                        {{ course.topics_count }} / {{ course.questions_count }}
                        / {{ course.materials_count }}
                    </TableCell>
                    <TableCell>
                        <CourseStatusBadge
                            :status="course.status"
                            :label="course.status_label"
                        />
                    </TableCell>
                    <TableCell class="text-muted-foreground">
                        {{ course.updated_at ?? '—' }}
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
