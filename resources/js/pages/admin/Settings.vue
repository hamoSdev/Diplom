<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { route } from 'ziggy-js';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Settings', href: route('admin.settings.index') }],
    },
});

const props = defineProps<{
    settings: {
        default_exam_length: number;
        default_questions_per_topic: number;
        revise_threshold: number;
        ai_model: string;
    };
}>();

const form = useForm({ ...props.settings });

function submit(): void {
    form.put(route('admin.settings.update'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Settings" />

    <div class="mx-auto flex w-full max-w-xl flex-col gap-6 p-4">
        <Heading
            title="System parameters"
            description="Platform-wide defaults used by exams and AI generation"
        />

        <Card>
            <CardHeader>
                <CardTitle>Exams &amp; AI</CardTitle>
                <CardDescription>
                    Changes apply to new exams, generations and courses.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <form class="grid gap-5" @submit.prevent="submit">
                    <div class="grid gap-2">
                        <Label for="default_exam_length">
                            Exam length (questions)
                        </Label>
                        <Input
                            id="default_exam_length"
                            v-model="form.default_exam_length"
                            type="number"
                            min="1"
                            max="50"
                        />
                        <InputError :message="form.errors.default_exam_length" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="default_questions_per_topic">
                            AI questions generated per topic
                        </Label>
                        <Input
                            id="default_questions_per_topic"
                            v-model="form.default_questions_per_topic"
                            type="number"
                            min="1"
                            max="20"
                        />
                        <InputError
                            :message="form.errors.default_questions_per_topic"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="revise_threshold">
                            Weak-topic threshold (%)
                        </Label>
                        <Input
                            id="revise_threshold"
                            v-model="form.revise_threshold"
                            type="number"
                            min="1"
                            max="100"
                        />
                        <p class="text-xs text-muted-foreground">
                            Topics scored below this are suggested for revision.
                        </p>
                        <InputError :message="form.errors.revise_threshold" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="ai_model">AI model</Label>
                        <Input id="ai_model" v-model="form.ai_model" type="text" />
                        <p class="text-xs text-muted-foreground">
                            Anthropic model id used for generation and grading.
                        </p>
                        <InputError :message="form.errors.ai_model" />
                    </div>

                    <div>
                        <Button type="submit" :disabled="form.processing">
                            Save settings
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
