<script setup lang="ts">
defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="dark relative flex min-h-svh flex-col items-center justify-center bg-[#002b42] p-6 font-sans text-white md:p-10"
    >
        <img
            src="/images/auth-bg.jpg"
            alt=""
            class="absolute inset-0 h-full w-full object-cover"
        />
        <div class="absolute inset-0 bg-black/25"></div>

        <div class="relative z-10 w-full max-w-sm">
            <div
                class="flex flex-col gap-8 rounded-xl border bg-card p-8 text-card-foreground shadow-sm"
            >
                <div class="space-y-1.5 text-center">
                    <h1 class="text-xl font-medium text-white">
                        {{ title }}
                    </h1>
                    <p class="text-sm text-white/60">
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
