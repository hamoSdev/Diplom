<script setup lang="ts">
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { AdminUserListItem, SelectOption, UserRole } from '@/types';

const props = defineProps<{
    faculties: SelectOption[];
    errors: Record<string, string>;
    user?: AdminUserListItem;
}>();

const selectClass =
    'h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs';

const isEdit = !!props.user;
const selectedRole = ref<UserRole>(props.user?.role ?? 'student');
</script>

<template>
    <div class="grid gap-2">
        <Label for="name">Name</Label>
        <Input
            id="name"
            name="name"
            :default-value="props.user?.name"
            required
            autocomplete="off"
        />
        <InputError :message="props.errors.name" />
    </div>

    <div class="grid gap-2">
        <Label for="email">Email</Label>
        <Input
            id="email"
            name="email"
            type="email"
            :default-value="props.user?.email"
            required
            autocomplete="off"
        />
        <InputError :message="props.errors.email" />
    </div>

    <div class="grid gap-2">
        <Label for="password">Password</Label>
        <Input
            id="password"
            name="password"
            type="password"
            :required="!isEdit"
            autocomplete="new-password"
        />
        <p v-if="isEdit" class="text-xs text-muted-foreground">
            Leave empty to keep the current password.
        </p>
        <InputError :message="props.errors.password" />
    </div>

    <div class="grid gap-2">
        <Label for="role">Role</Label>
        <select
            id="role"
            name="role"
            v-model="selectedRole"
            :class="selectClass"
        >
            <option value="student">Student</option>
            <option value="lecturer">Lecturer</option>
            <option value="admin">Administrator</option>
        </select>
        <InputError :message="props.errors.role" />
    </div>

    <div v-if="selectedRole === 'student'" class="grid gap-2">
        <Label for="faculty_id">Faculty</Label>
        <select id="faculty_id" name="faculty_id" :class="selectClass">
            <option value="" :selected="!props.user?.faculty_id">
                — none —
            </option>
            <option
                v-for="faculty in props.faculties"
                :key="faculty.id"
                :value="faculty.id"
                :selected="props.user?.faculty_id === faculty.id"
            >
                {{ faculty.name }}
            </option>
        </select>
        <InputError :message="props.errors.faculty_id" />
    </div>
</template>
