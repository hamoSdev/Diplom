<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { FileText, Trash2 } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { formatFileSize } from '@/lib/formatFileSize';
import type { CourseMaterialItem } from '@/types';
import { route } from 'ziggy-js';

const props = defineProps<{
    courseId: number;
    materials: CourseMaterialItem[];
}>();

const keep = { preserveScroll: true, preserveState: true };

const form = useForm<{ files: File[] }>({ files: [] });

function onPick(event: Event): void {
    const input = event.target as HTMLInputElement;
    form.files = input.files ? Array.from(input.files) : [];
}

function upload(): void {
    form.post(route('courses.materials.store', props.courseId), {
        ...keep,
        forceFormData: true,
        onSuccess: () => form.reset('files'),
    });
}

function remove(material: CourseMaterialItem): void {
    router.delete(
        route('courses.materials.destroy', [props.courseId, material.id]),
        keep,
    );
}
</script>

<template>
    <div class="flex flex-col gap-4">
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
                class="flex items-center justify-between gap-3 rounded-lg border p-3 text-sm"
            >
                <span class="flex min-w-0 items-center gap-3">
                    <span
                        class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                    >
                        <FileText class="size-4" />
                    </span>
                    <span class="flex min-w-0 flex-col">
                        <span class="truncate font-medium">
                            {{ material.original_name }}
                        </span>
                        <span class="text-xs text-muted-foreground">
                            {{ formatFileSize(material.size) }}
                        </span>
                    </span>
                </span>
                <Button
                    variant="ghost"
                    size="icon"
                    class="size-8 shrink-0"
                    @click="remove(material)"
                >
                    <Trash2 class="size-4" />
                </Button>
            </li>
        </ul>
    </div>
</template>
