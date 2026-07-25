<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import FacultyRowActions from '@/components/FacultyRowActions.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { FacultyListItem } from '@/types';
import { route } from 'ziggy-js';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Faculties', href: route('admin.faculties.index') },
        ],
    },
});

defineProps<{
    faculties: FacultyListItem[];
}>();
</script>

<template>
    <Head title="Faculties" />

    <div class="flex flex-col gap-8 p-4">
        <Heading title="Faculties" description="Create and manage faculties" />

        <Form
            :action="route('admin.faculties.store')"
            method="post"
            :reset-on-success="['name']"
            class="flex max-w-xl items-end gap-2"
            v-slot="{ errors, processing }"
        >
            <div class="grid flex-1 gap-2">
                <Label for="name">Name</Label>
                <Input id="name" name="name" required autocomplete="off" />
                <InputError :message="errors.name" />
            </div>
            <Button :disabled="processing" data-test="create-faculty-button">
                Create
            </Button>
        </Form>

        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Name</TableHead>
                    <TableHead>Subjects</TableHead>
                    <TableHead class="text-right">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="faculty in faculties" :key="faculty.id">
                    <TableCell class="font-medium">{{
                        faculty.name
                    }}</TableCell>
                    <TableCell>{{ faculty.subjects_count }}</TableCell>
                    <TableCell>
                        <FacultyRowActions :faculty="faculty" />
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
