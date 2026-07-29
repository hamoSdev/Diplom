<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { route } from 'ziggy-js';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: route('dashboard') }],
    },
});

defineProps<{
    faculty: { id: number; name: string } | null;
    subjectsCount: number;
    coursesCount: number;
}>();

const page = usePage();
const name = computed(() => page.props.auth.user.name);
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            :title="`Welcome, ${name}`"
            description="Your learning at a glance"
        />

        <Card v-if="!faculty">
            <CardHeader>
                <CardTitle>You're not enrolled yet</CardTitle>
            </CardHeader>
            <CardContent class="text-sm text-muted-foreground">
                An administrator will enroll you in a faculty. Once enrolled,
                your subjects and courses will appear here.
            </CardContent>
        </Card>

        <template v-else>
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader>
                        <CardTitle class="text-sm text-muted-foreground">
                            Faculty
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="text-lg font-medium">
                        {{ faculty.name }}
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader>
                        <CardTitle class="text-sm text-muted-foreground">
                            Subjects
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="text-lg font-medium">
                        {{ subjectsCount }}
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader>
                        <CardTitle class="text-sm text-muted-foreground">
                            Courses
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="text-lg font-medium">
                        {{ coursesCount }}
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Level exam</CardTitle>
                </CardHeader>
                <CardContent class="flex flex-col items-start gap-4">
                    <p class="text-sm text-muted-foreground">
                        Take the adaptive level exam to reveal your IT level. It
                        starts from where you say you stand and adapts to every
                        answer.
                    </p>
                    <Button as-child variant="outline">
                        <Link :href="route('exam.show')"
                            >Take the level exam</Link
                        >
                    </Button>
                </CardContent>
            </Card>

            <div>
                <Button as-child>
                    <Link :href="route('learning.index')"
                        >Go to my learning</Link
                    >
                </Button>
            </div>
        </template>
    </div>
</template>
