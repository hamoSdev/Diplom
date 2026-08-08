<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Download, Trash2 } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import SectionCard from '@/components/SectionCard.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { route } from 'ziggy-js';

type Application = {
    id: number;
    name: string;
    email: string;
    message: string;
    status: string;
    status_label: string;
    date: string | null;
    cv_url: string;
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Applications', href: route('admin.applications.index') },
        ],
    },
});

defineProps<{
    applications: Application[];
}>();

function statusTone(status: string): string {
    switch (status) {
        case 'approved':
            return 'bg-emerald-500/10 text-emerald-500';
        case 'rejected':
            return 'bg-destructive/10 text-destructive';
        default:
            return 'bg-amber-500/10 text-amber-500';
    }
}

function setStatus(application: Application, status: string): void {
    router.put(route('admin.applications.update', application.id), { status });
}

function remove(application: Application): void {
    router.delete(route('admin.applications.destroy', application.id));
}
</script>

<template>
    <Head title="Applications" />

    <div class="flex flex-col gap-8 p-4">
        <Heading
            title="Lecturer applications"
            description="Review people applying to teach, then create their account"
        />

        <p
            v-if="applications.length === 0"
            class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            No applications yet.
        </p>

        <SectionCard v-else title="All applications" flush>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Applicant</TableHead>
                        <TableHead>Message</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead class="text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="application in applications"
                        :key="application.id"
                    >
                        <TableCell>
                            <div class="flex flex-col">
                                <span class="font-medium">
                                    {{ application.name }}
                                </span>
                                <span class="text-xs text-muted-foreground">
                                    {{ application.email }} ·
                                    {{ application.date }}
                                </span>
                            </div>
                        </TableCell>
                        <TableCell class="max-w-xs">
                            <p
                                class="line-clamp-2 text-sm text-muted-foreground"
                            >
                                {{ application.message }}
                            </p>
                        </TableCell>
                        <TableCell>
                            <Badge
                                variant="secondary"
                                :class="statusTone(application.status)"
                            >
                                {{ application.status_label }}
                            </Badge>
                        </TableCell>
                        <TableCell>
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <a :href="application.cv_url">
                                        <Download class="size-4" />
                                        CV
                                    </a>
                                </Button>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    :disabled="
                                        application.status === 'approved'
                                    "
                                    @click="setStatus(application, 'approved')"
                                >
                                    Approve
                                </Button>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    :disabled="
                                        application.status === 'rejected'
                                    "
                                    @click="setStatus(application, 'rejected')"
                                >
                                    Reject
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="size-8"
                                    @click="remove(application)"
                                >
                                    <Trash2 class="size-4" />
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </SectionCard>
    </div>
</template>
