<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Trash2 } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { coursesIndexUrl } from '@/lib/coursesIndexUrl';
import { formatFileSize } from '@/lib/formatFileSize';
import type { CourseMaterialItem } from '@/types';
import { route } from 'ziggy-js';

type Course = { id: number; title: string };
type LayoutProps = { course: Course; auth: { user: { role: string } } };

// Функциональная форма layout — название курса и ролевой родитель в breadcrumbs.
defineOptions({
    layout: (props: LayoutProps) => ({
        breadcrumbs: [
            { title: 'Courses', href: coursesIndexUrl(props.auth.user.role) },
            {
                title: props.course.title,
                href: route('courses.materials.index', props.course.id),
            },
        ],
    }),
});

const props = defineProps<{
    course: Course;
    materials: CourseMaterialItem[];
}>();

const form = useForm<{ files: File[] }>({ files: [] });

function onPick(event: Event): void {
    const input = event.target as HTMLInputElement;
    form.files = input.files ? Array.from(input.files) : [];
}

function upload(): void {
    form.post(route('courses.materials.store', props.course.id), {
        onSuccess: () => form.reset('files'),
    });
}

function remove(material: CourseMaterialItem): void {
    router.delete(
        route('courses.materials.destroy', [props.course.id, material.id]),
    );
}
</script>

<template>
    <Head :title="`${course.title} — materials`" />

    <div class="mx-auto flex w-full max-w-2xl flex-col gap-6 p-4">
        <Heading
            :title="course.title"
            description="Upload PDF, Word or PowerPoint files. Students of the faculty can download them."
        />

        <div class="flex flex-col gap-3 rounded-xl border p-4">
            <Input
                type="file"
                multiple
                accept=".pdf,.doc,.docx,.ppt,.pptx"
                @change="onPick"
            />
            <p v-if="form.errors.files" class="text-sm text-destructive">
                {{ form.errors.files }}
            </p>
            <div>
                <Button
                    :disabled="form.files.length === 0 || form.processing"
                    @click="upload"
                >
                    Upload
                </Button>
            </div>
        </div>

        <p
            v-if="materials.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            No files uploaded yet.
        </p>
        <ul v-else class="flex flex-col gap-2">
            <li
                v-for="material in materials"
                :key="material.id"
                class="flex items-center justify-between rounded-lg border p-3 text-sm"
            >
                <span class="font-medium">{{ material.original_name }}</span>
                <div class="flex items-center gap-3">
                    <span class="text-muted-foreground">
                        {{ formatFileSize(material.size) }}
                    </span>
                    <Button
                        variant="ghost"
                        size="icon"
                        class="size-8"
                        @click="remove(material)"
                    >
                        <Trash2 class="size-4" />
                    </Button>
                </div>
            </li>
        </ul>
    </div>
</template>
