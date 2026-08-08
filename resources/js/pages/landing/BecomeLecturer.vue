<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { CheckCircle2, Paperclip } from '@lucide/vue';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import LandingNav from '@/components/LandingNav.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { route } from 'ziggy-js';

const submitted = ref(false);
const fileName = ref('');

const form = useForm<{
    name: string;
    email: string;
    message: string;
    cv: File | null;
}>({
    name: '',
    email: '',
    message: '',
    cv: null,
});

function onPick(event: Event): void {
    const input = event.target as HTMLInputElement;
    form.cv = input.files?.[0] ?? null;
    fileName.value = form.cv?.name ?? '';
}

function submit(): void {
    form.post(route('become-lecturer.store'), {
        forceFormData: true,
        onSuccess: () => {
            submitted.value = true;
            form.reset();
            fileName.value = '';
        },
    });
}
</script>

<template>
    <Head title="Become a lecturer" />

    <!-- `dark` makes the shadcn form components render their dark variant on the
         night-sky landing, matching the login / register pages. -->
    <div class="dark relative min-h-screen bg-[#002b42] font-sans text-white">
        <img
            src="/images/auth-bg.jpg"
            alt=""
            class="fixed inset-0 h-full w-full object-cover"
        />
        <div class="fixed inset-0 bg-black/50"></div>

        <div class="relative z-10">
            <LandingNav />

            <div class="mx-auto w-full max-w-lg px-6 py-10 sm:py-16">
                <div class="mb-8 text-center">
                    <h1
                        class="font-display text-4xl tracking-tight sm:text-5xl"
                    >
                        Become a lecturer
                    </h1>
                    <p class="mt-3 text-white/60">
                        Tell us about yourself and attach your CV. Our team
                        reviews every application and gets back to you.
                    </p>
                </div>

                <Card v-if="submitted">
                    <CardContent
                        class="flex flex-col items-center gap-4 pt-6 text-center"
                    >
                        <CheckCircle2 class="size-10 text-emerald-500" />
                        <div>
                            <p class="text-lg font-medium">Application sent</p>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Thanks — we'll review your details and be in
                                touch by email.
                            </p>
                        </div>
                        <Button as-child variant="outline">
                            <Link :href="route('home')">← Back to home</Link>
                        </Button>
                    </CardContent>
                </Card>

                <Card v-else>
                    <CardContent class="pt-6">
                        <form
                            class="flex flex-col gap-4"
                            @submit.prevent="submit"
                        >
                            <div class="grid gap-2">
                                <Label for="name">Full name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    placeholder="Jane Doe"
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    placeholder="you@example.com"
                                />
                                <InputError :message="form.errors.email" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="message">About you</Label>
                                <Textarea
                                    id="message"
                                    v-model="form.message"
                                    rows="4"
                                    placeholder="Your experience, subjects you'd like to teach…"
                                />
                                <InputError :message="form.errors.message" />
                            </div>

                            <div class="grid gap-2">
                                <Label>CV (PDF, DOC, DOCX)</Label>
                                <label
                                    class="flex cursor-pointer items-center gap-2 rounded-md border border-dashed border-input px-3 py-2.5 text-sm text-muted-foreground transition-colors hover:border-ring"
                                >
                                    <Paperclip class="size-4" />
                                    <span class="truncate">
                                        {{ fileName || 'Attach your CV' }}
                                    </span>
                                    <input
                                        type="file"
                                        accept=".pdf,.doc,.docx"
                                        class="hidden"
                                        @change="onPick"
                                    />
                                </label>
                                <InputError :message="form.errors.cv" />
                            </div>

                            <Button
                                type="submit"
                                class="mt-2"
                                :disabled="form.processing"
                            >
                                Send application
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
