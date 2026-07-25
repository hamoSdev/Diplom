<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { SelectOption, SubjectListItem } from '@/types';

const props = defineProps<{
    faculties: SelectOption[];
    lecturers: SelectOption[];
    errors: Record<string, string>;
    subject?: SubjectListItem;
}>();

const selectClass =
    'h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs';

const isAttached = (lecturerId: number): boolean =>
    props.subject?.lecturer_ids.includes(lecturerId) ?? false;
</script>

<template>
    <div class="grid gap-2">
        <Label for="name">Name</Label>
        <Input
            id="name"
            name="name"
            :default-value="props.subject?.name"
            required
        />
        <InputError :message="props.errors.name" />
    </div>

    <div class="grid gap-2">
        <Label for="description">Description</Label>
        <textarea
            id="description"
            name="description"
            rows="2"
            :class="selectClass + ' h-auto'"
            :value="props.subject?.description ?? ''"
        ></textarea>
        <InputError :message="props.errors.description" />
    </div>

    <div class="grid gap-2">
        <Label for="faculty_id">Faculty</Label>
        <select id="faculty_id" name="faculty_id" :class="selectClass" required>
            <option value="" disabled :selected="!props.subject">
                — select —
            </option>
            <option
                v-for="faculty in props.faculties"
                :key="faculty.id"
                :value="faculty.id"
                :selected="props.subject?.faculty_id === faculty.id"
            >
                {{ faculty.name }}
            </option>
        </select>
        <InputError :message="props.errors.faculty_id" />
    </div>

    <div class="grid gap-2">
        <Label>Lecturers</Label>
        <p
            v-if="props.lecturers.length === 0"
            class="text-sm text-muted-foreground"
        >
            No lecturers yet.
        </p>
        <label
            v-for="lecturer in props.lecturers"
            :key="lecturer.id"
            class="flex items-center gap-2 text-sm"
        >
            <input
                type="checkbox"
                name="lecturer_ids[]"
                :value="lecturer.id"
                :checked="isAttached(lecturer.id)"
            />
            {{ lecturer.name }}
        </label>
        <InputError :message="props.errors.lecturer_ids" />
    </div>
</template>
