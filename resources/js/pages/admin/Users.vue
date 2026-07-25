<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import UserFormFields from '@/components/UserFormFields.vue';
import UsersTable from '@/components/UsersTable.vue';
import type { AdminUserListItem, SelectOption } from '@/types';
import { route } from 'ziggy-js';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Users', href: route('admin.users.index') }],
    },
});

defineProps<{
    users: AdminUserListItem[];
    faculties: SelectOption[];
}>();
</script>

<template>
    <Head title="Users" />

    <div class="flex flex-col gap-8 p-4">
        <Heading title="Users" description="Create accounts and assign roles" />

        <Form
            :action="route('admin.users.store')"
            method="post"
            :reset-on-success="['name', 'email', 'password']"
            class="grid max-w-xl gap-4"
            v-slot="{ errors, processing }"
        >
            <UserFormFields :faculties="faculties" :errors="errors" />
            <div>
                <Button :disabled="processing" data-test="create-user-button">
                    Create user
                </Button>
            </div>
        </Form>

        <UsersTable :users="users" :faculties="faculties" />
    </div>
</template>
