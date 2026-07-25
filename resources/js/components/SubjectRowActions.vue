<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { ref } from 'vue';
import SubjectFormFields from '@/components/SubjectFormFields.vue';
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
import type { SelectOption, SubjectListItem } from '@/types';
import { route } from 'ziggy-js';

const props = defineProps<{
    subject: SubjectListItem;
    faculties: SelectOption[];
    lecturers: SelectOption[];
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
                    :action="route('admin.subjects.update', props.subject.id)"
                    method="put"
                    class="grid gap-4"
                    v-slot="{ errors, processing }"
                    @success="editOpen = false"
                >
                    <DialogHeader>
                        <DialogTitle>Edit subject</DialogTitle>
                    </DialogHeader>
                    <SubjectFormFields
                        :faculties="props.faculties"
                        :lecturers="props.lecturers"
                        :errors="errors"
                        :subject="props.subject"
                    />
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
                    :action="route('admin.subjects.destroy', props.subject.id)"
                    method="delete"
                    v-slot="{ processing }"
                    @success="deleteOpen = false"
                >
                    <DialogHeader>
                        <DialogTitle
                            >Delete {{ props.subject.name }}?</DialogTitle
                        >
                        <DialogDescription>
                            Its courses will be deleted too. This cannot be
                            undone.
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
