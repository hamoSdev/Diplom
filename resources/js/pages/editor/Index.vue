<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import RichTextEditor from '@/components/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { EditorSubject } from '@/types';
import { route } from 'ziggy-js';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Editor', href: route('editor.index') }],
    },
});

defineProps<{
    subjects: EditorSubject[];
}>();

const content = ref('<h2>New article</h2><p>Start writing…</p>');
const open = ref(false);

const form = useForm({
    title: '',
    content: '',
    subject_id: null as number | null,
});

function openSaveDialog(): void {
    if (!form.title) {
        const text = new DOMParser()
            .parseFromString(content.value, 'text/html')
            .body.textContent?.trim();
        form.title = text ? text.slice(0, 80) : '';
    }

    open.value = true;
}

function saveTo(subject: EditorSubject): void {
    form.content = content.value;
    form.subject_id = subject.id;
    form.post(route('editor.store'), {
        onSuccess: () => {
            open.value = false;
            form.reset('subject_id');
        },
    });
}
</script>

<template>
    <Head title="Editor" />

    <div class="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Editor"
                description="Write an article, then save it as a course of one of your subjects."
            />
            <Button @click="openSaveDialog">Save as…</Button>
        </div>

        <RichTextEditor v-model="content" />

        <Dialog v-model:open="open">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Save as course</DialogTitle>
                    <DialogDescription>
                        Give it a title and pick the subject to save it under.
                    </DialogDescription>
                </DialogHeader>

                <div class="flex flex-col gap-2">
                    <Label for="course-title">Title</Label>
                    <Input
                        id="course-title"
                        v-model="form.title"
                        placeholder="Article title"
                    />
                    <p
                        v-if="form.errors.title"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.title }}
                    </p>
                </div>

                <div class="flex flex-col gap-2">
                    <Label>Subject</Label>
                    <p
                        v-if="subjects.length === 0"
                        class="rounded-lg border border-dashed p-4 text-center text-sm text-muted-foreground"
                    >
                        You have no subjects to save into yet.
                    </p>
                    <button
                        v-for="subject in subjects"
                        :key="subject.id"
                        type="button"
                        :disabled="form.processing || !form.title"
                        class="rounded-lg border p-3 text-left text-sm transition-colors hover:bg-muted disabled:cursor-not-allowed disabled:opacity-50"
                        @click="saveTo(subject)"
                    >
                        {{ subject.name }}
                    </button>
                    <p
                        v-if="form.errors.subject_id"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.subject_id }}
                    </p>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>
