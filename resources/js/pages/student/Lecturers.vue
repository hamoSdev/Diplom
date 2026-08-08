<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { route } from 'ziggy-js';

type LecturerItem = {
    id: number;
    name: string;
    subjects: string[];
    courses_count: number;
};

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Lecturers', href: route('lecturers.index') }],
    },
});

defineProps<{
    lecturers: LecturerItem[];
}>();

function initial(name: string): string {
    return name.charAt(0).toUpperCase();
}
</script>

<template>
    <Head title="Lecturers" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="Lecturers"
            description="The people teaching across the platform"
        />

        <p
            v-if="lecturers.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            No lecturers yet.
        </p>

        <div v-else class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <Card v-for="lecturer in lecturers" :key="lecturer.id">
                <CardHeader class="flex-row items-center gap-3 space-y-0">
                    <div
                        class="flex size-10 items-center justify-center rounded-full bg-primary/10 font-semibold text-primary"
                    >
                        {{ initial(lecturer.name) }}
                    </div>
                    <div class="flex flex-col">
                        <CardTitle class="text-base">
                            {{ lecturer.name }}
                        </CardTitle>
                        <span class="text-xs text-muted-foreground">
                            {{ lecturer.courses_count }} courses
                        </span>
                    </div>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="lecturer.subjects.length > 0"
                        class="flex flex-wrap gap-1.5"
                    >
                        <span
                            v-for="subject in lecturer.subjects"
                            :key="subject"
                            class="rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground"
                        >
                            {{ subject }}
                        </span>
                    </div>
                    <p v-else class="text-sm text-muted-foreground">
                        Not attached to any subject yet.
                    </p>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
