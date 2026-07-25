<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import SubjectFormFields from '@/components/SubjectFormFields.vue';
import SubjectRowActions from '@/components/SubjectRowActions.vue';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { SelectOption, SubjectListItem } from '@/types';
import { route } from 'ziggy-js';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Subjects', href: route('admin.subjects.index') },
        ],
    },
});

defineProps<{
    subjects: SubjectListItem[];
    faculties: SelectOption[];
    lecturers: SelectOption[];
}>();
</script>

<template>
    <Head title="Subjects" />

    <div class="flex flex-col gap-8 p-4">
        <Heading
            title="Subjects"
            description="Create subjects and attach lecturers"
        />

        <Form
            :action="route('admin.subjects.store')"
            method="post"
            :reset-on-success="['name', 'description']"
            class="grid max-w-xl gap-4"
            v-slot="{ errors, processing }"
        >
            <SubjectFormFields
                :faculties="faculties"
                :lecturers="lecturers"
                :errors="errors"
            />
            <div>
                <Button
                    :disabled="processing"
                    data-test="create-subject-button"
                >
                    Create subject
                </Button>
            </div>
        </Form>

        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Name</TableHead>
                    <TableHead>Faculty</TableHead>
                    <TableHead>Lecturers</TableHead>
                    <TableHead class="text-right">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="subject in subjects" :key="subject.id">
                    <TableCell class="font-medium">{{
                        subject.name
                    }}</TableCell>
                    <TableCell>{{ subject.faculty_name }}</TableCell>
                    <TableCell>
                        {{ subject.lecturer_names.join(', ') || '—' }}
                    </TableCell>
                    <TableCell>
                        <SubjectRowActions
                            :subject="subject"
                            :faculties="faculties"
                            :lecturers="lecturers"
                        />
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
