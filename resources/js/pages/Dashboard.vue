<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

defineOptions({
    layout: { breadcrumbs: [{ title: 'Dashboard', href: route('dashboard') }] },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Dashboard" />
    <div>{{ user.name }} — {{ user.role }}</div>
</template>
