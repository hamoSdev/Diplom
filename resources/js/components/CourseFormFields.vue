<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { AdminCourseListItem, SelectOption } from '@/types';

const props = defineProps<{
    subjects: SelectOption[];
    lecturers: SelectOption[];
    errors: Record<string, string>;
    course?: AdminCourseListItem;
}>();

const selectClass =
    'h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs';
</script>

<template>
    <div class="grid gap-2">
        <Label for="title">Title</Label>
        <Input
            id="title"
            name="title"
            :default-value="props.course?.title"
            required
        />
        <InputError :message="props.errors.title" />
    </div>

    <div class="grid gap-2">
        <Label for="subject_id">Subject</Label>
        <select id="subject_id" name="subject_id" :class="selectClass" required>
            <option value="" disabled :selected="!props.course">
                — select —
            </option>
            <option
                v-for="subject in props.subjects"
                :key="subject.id"
                :value="subject.id"
                :selected="props.course?.subject_id === subject.id"
            >
                {{ subject.name }}
            </option>
        </select>
        <InputError :message="props.errors.subject_id" />
    </div>

    <div class="grid gap-2">
        <Label for="lecturer_id">Lecturer</Label>
        <select
            id="lecturer_id"
            name="lecturer_id"
            :class="selectClass"
            required
        >
            <option value="" disabled :selected="!props.course">
                — select —
            </option>
            <option
                v-for="lecturer in props.lecturers"
                :key="lecturer.id"
                :value="lecturer.id"
                :selected="props.course?.lecturer_id === lecturer.id"
            >
                {{ lecturer.name }}
            </option>
        </select>
        <InputError :message="props.errors.lecturer_id" />
    </div>
</template>
