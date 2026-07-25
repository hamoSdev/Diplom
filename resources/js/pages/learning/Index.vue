<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import StudentSubjectCard from '@/components/StudentSubjectCard.vue';
import type { StudentFaculty, StudentSubject } from '@/types';
import { route } from 'ziggy-js';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'My learning', href: route('learning.index') }],
    },
});

defineProps<{
    faculty: StudentFaculty | null;
    subjects: StudentSubject[];
}>();
</script>

<template>
    <Head title="My learning" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="My learning"
            :description="
                faculty
                    ? `Faculty: ${faculty.name}`
                    : 'You are not assigned to a faculty yet'
            "
        />

        <p
            v-if="!faculty"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            An administrator will assign you to a faculty. Once assigned, its
            subjects and courses appear here.
        </p>

        <p
            v-else-if="subjects.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            Your faculty has no subjects yet.
        </p>

        <div v-else class="grid gap-4 md:grid-cols-2">
            <StudentSubjectCard
                v-for="subject in subjects"
                :key="subject.id"
                :subject="subject"
            />
        </div>
    </div>
</template>
