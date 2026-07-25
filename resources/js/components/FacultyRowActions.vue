<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { FacultyListItem } from '@/types';
import { route } from 'ziggy-js';

const props = defineProps<{
    faculty: FacultyListItem;
}>();

const editOpen = ref(false);
const deleteOpen = ref(false);
</script>

<template>
    <div class="flex justify-end gap-2">
        <Dialog v-model:open="editOpen">
            <DialogTrigger as-child>
                <Button variant="outline" size="sm">Edit</Button>
            </DialogTrigger>
            <DialogContent>
                <Form
                    :action="route('admin.faculties.update', props.faculty.id)"
                    method="put"
                    class="grid gap-4"
                    v-slot="{ errors, processing }"
                    @success="editOpen = false"
                >
                    <DialogHeader>
                        <DialogTitle>Edit faculty</DialogTitle>
                    </DialogHeader>
                    <div class="grid gap-2">
                        <Label :for="`name-${props.faculty.id}`">Name</Label>
                        <Input
                            :id="`name-${props.faculty.id}`"
                            name="name"
                            :default-value="props.faculty.name"
                            required
                        />
                        <InputError :message="errors.name" />
                    </div>
                    <DialogFooter class="gap-2">
                        <DialogClose as-child>
                            <Button type="button" variant="secondary">
                                Cancel
                            </Button>
                        </DialogClose>
                        <Button type="submit" :disabled="processing"
                            >Save</Button
                        >
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="deleteOpen">
            <DialogTrigger as-child>
                <Button variant="destructive" size="sm">Delete</Button>
            </DialogTrigger>
            <DialogContent>
                <Form
                    :action="route('admin.faculties.destroy', props.faculty.id)"
                    method="delete"
                    v-slot="{ processing }"
                    @success="deleteOpen = false"
                >
                    <DialogHeader>
                        <DialogTitle
                            >Delete {{ props.faculty.name }}?</DialogTitle
                        >
                        <DialogDescription>
                            Its subjects and courses will be deleted too. This
                            cannot be undone.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter class="mt-4 gap-2">
                        <DialogClose as-child>
                            <Button type="button" variant="secondary">
                                Cancel
                            </Button>
                        </DialogClose>
                        <Button
                            type="submit"
                            variant="destructive"
                            :disabled="processing"
                        >
                            Delete
                        </Button>
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>
    </div>
</template>
