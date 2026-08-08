<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Plus, X } from '@lucide/vue';
import { watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { TopicItem, ValueLabelOption } from '@/types';
import { route } from 'ziggy-js';

type QuestionOption = { body: string; is_correct: boolean };

const props = defineProps<{
    courseId: number;
    topics: TopicItem[];
    typeOptions: ValueLabelOption[];
    difficultyOptions: ValueLabelOption[];
}>();

const selectClass =
    'h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs';

function emptyOptions(): QuestionOption[] {
    return [
        { body: '', is_correct: true },
        { body: '', is_correct: false },
    ];
}

const form = useForm<{
    topic_id: number | '';
    type: string;
    difficulty: string;
    body: string;
    explanation: string;
    options: QuestionOption[];
}>({
    topic_id: '',
    type: 'single',
    difficulty: 'medium',
    body: '',
    explanation: '',
    options: emptyOptions(),
});

// True/false questions are just two fixed options, so the author only picks
// which one is correct — the option bodies are locked.
watch(
    () => form.type,
    (type) => {
        form.options =
            type === 'boolean'
                ? [
                      { body: 'True', is_correct: true },
                      { body: 'False', is_correct: false },
                  ]
                : emptyOptions();
    },
);

function setCorrect(index: number): void {
    form.options.forEach((option, i) => (option.is_correct = i === index));
}

function addOption(): void {
    if (form.options.length < 6) {
        form.options.push({ body: '', is_correct: false });
    }
}

function removeOption(index: number): void {
    if (form.options.length <= 2) {
        return;
    }

    const wasCorrect = form.options[index].is_correct;
    form.options.splice(index, 1);

    if (wasCorrect) {
        form.options[0].is_correct = true;
    }
}

function submit(): void {
    form.post(route('courses.questions.store', props.courseId), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset();
            form.options = emptyOptions();
        },
    });
}
</script>

<template>
    <form class="grid gap-4 rounded-xl border p-4" @submit.prevent="submit">
        <div class="grid gap-2 sm:grid-cols-3">
            <div class="grid gap-2">
                <Label for="topic_id">Topic</Label>
                <select
                    id="topic_id"
                    v-model="form.topic_id"
                    :class="selectClass"
                >
                    <option value="" disabled>— select —</option>
                    <option
                        v-for="topic in topics"
                        :key="topic.id"
                        :value="topic.id"
                    >
                        {{ topic.title }}
                    </option>
                </select>
                <InputError :message="form.errors.topic_id" />
            </div>
            <div class="grid gap-2">
                <Label for="type">Type</Label>
                <select id="type" v-model="form.type" :class="selectClass">
                    <option
                        v-for="o in typeOptions"
                        :key="o.value"
                        :value="o.value"
                    >
                        {{ o.label }}
                    </option>
                </select>
            </div>
            <div class="grid gap-2">
                <Label for="difficulty">Difficulty</Label>
                <select
                    id="difficulty"
                    v-model="form.difficulty"
                    :class="selectClass"
                >
                    <option
                        v-for="o in difficultyOptions"
                        :key="o.value"
                        :value="o.value"
                    >
                        {{ o.label }}
                    </option>
                </select>
            </div>
        </div>

        <div class="grid gap-2">
            <Label for="body">Question</Label>
            <Textarea id="body" v-model="form.body" rows="2" />
            <InputError :message="form.errors.body" />
        </div>

        <div class="grid gap-2">
            <Label>Options (pick the correct one)</Label>
            <div
                v-for="(option, index) in form.options"
                :key="index"
                class="flex items-center gap-2"
            >
                <input
                    type="radio"
                    :checked="option.is_correct"
                    @change="setCorrect(index)"
                />
                <Input
                    v-model="option.body"
                    :readonly="form.type === 'boolean'"
                    placeholder="Option text"
                />
                <Button
                    v-if="form.type === 'single'"
                    type="button"
                    variant="ghost"
                    size="icon"
                    class="size-8 shrink-0"
                    :disabled="form.options.length <= 2"
                    @click="removeOption(index)"
                >
                    <X class="size-4" />
                </Button>
            </div>
            <Button
                v-if="form.type === 'single' && form.options.length < 6"
                type="button"
                variant="outline"
                size="sm"
                class="w-fit"
                @click="addOption"
            >
                <Plus class="size-4" /> Add option
            </Button>
            <InputError :message="form.errors.options" />
        </div>

        <div class="grid gap-2">
            <Label for="explanation">Explanation (optional)</Label>
            <Textarea id="explanation" v-model="form.explanation" rows="2" />
        </div>

        <div>
            <Button type="submit" :disabled="form.processing"
                >Add question</Button
            >
        </div>
    </form>
</template>
