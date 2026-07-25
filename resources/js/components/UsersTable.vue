<script setup lang="ts">
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import UserRowActions from '@/components/UserRowActions.vue';
import type { AdminUserListItem, SelectOption } from '@/types';

defineProps<{
    users: AdminUserListItem[];
    faculties: SelectOption[];
}>();
</script>

<template>
    <Table>
        <TableHeader>
            <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Role</TableHead>
                <TableHead>Linked to</TableHead>
                <TableHead class="text-right">Actions</TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow v-for="user in users" :key="user.id">
                <TableCell class="font-medium">{{ user.name }}</TableCell>
                <TableCell>{{ user.email }}</TableCell>
                <TableCell>{{ user.role_label }}</TableCell>
                <TableCell class="text-sm text-muted-foreground">
                    <template v-if="user.role === 'student'">
                        <span v-if="user.faculty_name">
                            {{ user.faculty_name }} ·
                            {{ user.faculty_subjects_count }} subjects
                        </span>
                        <span v-else>No faculty</span>
                    </template>
                    <template v-else-if="user.role === 'lecturer'">
                        <span v-if="user.taught_subjects.length">
                            Teaches: {{ user.taught_subjects.join(', ') }} ·
                        </span>
                        <span>{{ user.courses_count }} courses</span>
                    </template>
                    <span v-else>—</span>
                </TableCell>
                <TableCell>
                    <UserRowActions :user="user" :faculties="faculties" />
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
