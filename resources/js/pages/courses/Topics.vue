<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Check, Pencil, Trash2, X } from '@lucide/vue';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { coursesIndexUrl } from '@/lib/coursesIndexUrl';
import type { TopicItem } from '@/types';
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
                href: route('courses.topics.index', props.course.id),
            },
        ],
    }),
});

const props = defineProps<{
    course: Course;
    topics: TopicItem[];
}>();

const addForm = useForm({ title: '' });
const editForm = useForm({ title: '' });
const editingId = ref<number | null>(null);

function add(): void {
    addForm.post(route('courses.topics.store', props.course.id), {
        onSuccess: () => addForm.reset('title'),
    });
}

function startEdit(topic: TopicItem): void {
    editingId.value = topic.id;
    editForm.title = topic.title;
}

function saveEdit(topic: TopicItem): void {
    editForm.put(route('courses.topics.update', [props.course.id, topic.id]), {
        onSuccess: () => (editingId.value = null),
    });
}

function remove(topic: TopicItem): void {
    router.delete(route('courses.topics.destroy', [props.course.id, topic.id]));
}
</script>

<template>
    <Head :title="`${course.title} — topics`" />

    <div class="mx-auto flex w-full max-w-2xl flex-col gap-6 p-4">
        <Heading
            :title="course.title"
            description="Break the course into topics. Questions and per-topic material will attach to these."
        />

        <form class="flex gap-2" @submit.prevent="add">
            <Input v-model="addForm.title" placeholder="New topic title" />
            <Button
                :disabled="!addForm.title || addForm.processing"
                type="submit"
            >
                Add topic
            </Button>
        </form>
        <p v-if="addForm.errors.title" class="-mt-4 text-sm text-destructive">
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
                class="flex items-center gap-3 rounded-lg border p-3 text-sm"
            >
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
            </li>
        </ol>
    </div>
</template>
