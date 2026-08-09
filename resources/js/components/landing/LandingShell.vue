<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import LandingNav from '@/components/LandingNav.vue';

defineProps<{
    title: string;
}>();
</script>

<template>
    <Head :title="title" />

    <div class="relative min-h-screen bg-[#002b42] font-sans text-white">
        <img
            src="/images/auth-bg.jpg"
            alt=""
            class="fixed inset-0 h-full w-full object-cover"
        />
        <div class="fixed inset-0 bg-black/40"></div>

        <div class="relative z-10 flex min-h-screen flex-col">
            <LandingNav />
            <slot />
        </div>
    </div>
</template>
