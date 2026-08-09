<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { BookOpen, Check, Pencil, Sparkles, Trash2, X } from '@lucide/vue';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { TopicItem } from '@/types';
import { route } from 'ziggy-js';

const props = defineProps<{
    courseId: number;
    topics: TopicItem[];
    generating?: boolean;
}>();

const keep = { preserveScroll: true, preserveState: true };

const addForm = useForm({ title: '' });
const editForm = useForm({ title: '' });
const editingId = ref<number | null>(null);
const openId = ref<number | null>(null);

function toggleMaterial(id: number): void {
    openId.value = openId.value === id ? null : id;
}

function add(): void {
    addForm.post(route('courses.topics.store', props.courseId), {
        ...keep,
        onSuccess: () => addForm.reset('title'),
    });
}

function startEdit(topic: TopicItem): void {
    editingId.value = topic.id;
    editForm.title = topic.title;
}

function saveEdit(topic: TopicItem): void {
    editForm.put(route('courses.topics.update', [props.courseId, topic.id]), {
        ...keep,
        onSuccess: () => (editingId.value = null),
    });
}

function remove(topic: TopicItem): void {
    router.delete(
        route('courses.topics.destroy', [props.courseId, topic.id]),
        keep,
    );
}

function generateTopic(topic: TopicItem): void {
    router.post(
        route('courses.topics.generate', [props.courseId, topic.id]),
        {},
        { preserveScroll: true },
    );
}
</script>

<template>
    <div class="flex flex-col gap-4">
        <form class="flex gap-2" @submit.prevent="add">
            <Input v-model="addForm.title" placeholder="New topic title" />
            <Button
                :disabled="!addForm.title || addForm.processing"
                type="submit"
            >
                Add topic
            </Button>
        </form>
        <p v-if="addForm.errors.title" class="-mt-2 text-sm text-destructive">
            {{ addForm.errors.title }}
        </p>

        <p
            v-if="topics.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            No topics yet.
        </p>
        <ol v-else class="flex flex-col gap-2">
            <li
                v-for="(topic, index) in topics"
                :key="topic.id"
                class="flex flex-col gap-3 rounded-lg border p-3 text-sm"
            >
                <div class="flex items-center gap-3">
                    <span class="text-muted-foreground">{{ index + 1 }}.</span>

                    <template v-if="editingId === topic.id">
                        <Input
                            v-model="editForm.title"
                            class="h-8"
                            @keyup.enter="saveEdit(topic)"
                        />
                        <Button
                            variant="ghost"
                            size="icon"
                            class="size-8"
                            @click="saveEdit(topic)"
                        >
                            <Check class="size-4" />
                        </Button>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="size-8"
                            @click="editingId = null"
                        >
                            <X class="size-4" />
                        </Button>
                    </template>

                    <template v-else>
                        <span class="flex-1 font-medium">{{ topic.title }}</span>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="size-8"
                            :disabled="generating"
                            title="Generate material & questions with AI"
                            @click="generateTopic(topic)"
                        >
                            <Sparkles class="size-4" />
                        </Button>
                        <Button
                            v-if="topic.content"
                            variant="ghost"
                            size="icon"
                            class="size-8"
                            :aria-pressed="openId === topic.id"
                            title="View AI material"
                            @click="toggleMaterial(topic.id)"
                        >
                            <BookOpen class="size-4" />
                        </Button>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="size-8"
                            @click="startEdit(topic)"
                        >
                            <Pencil class="size-4" />
                        </Button>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="size-8"
                            @click="remove(topic)"
                        >
                            <Trash2 class="size-4" />
                        </Button>
                    </template>
                </div>

                <div
                    v-if="topic.content && openId === topic.id"
                    class="tiptap border-t pt-3 pl-6"
                    v-html="topic.content"
                />
            </li>
        </ol>
    </div>
</template>
