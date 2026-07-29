<script setup lang="ts">
import {
    Bold,
    Code,
    Heading1,
    Heading2,
    Italic,
    List,
    ListOrdered,
    Quote,
    Redo,
    Strikethrough,
    Undo,
} from '@lucide/vue';
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import type { Component } from 'vue';
import { onBeforeUnmount, watch } from 'vue';

type Props = {
    modelValue: string;
};

type Emits = {
    'update:modelValue': [value: string];
};

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

// В @tiptap/vue-3 v3 useEditor создаёт редактор внутри onMounted — то есть только
// на клиенте, поэтому при SSR ProseMirror не трогает DOM и рендер на сервере не падает.
// До монтирования editor.value === undefined, шаблон это учитывает.
const editor = useEditor({
    content: props.modelValue,
    extensions: [StarterKit],
    editorProps: {
        attributes: {
            class: 'tiptap min-h-96 rounded-b-xl px-4 py-3 focus:outline-none',
        },
    },
    onUpdate: ({ editor }) => emit('update:modelValue', editor.getHTML()),
});

watch(
    () => props.modelValue,
    (value) => {
        if (editor.value && value !== editor.value.getHTML()) {
            editor.value.commands.setContent(value, { emitUpdate: false });
        }
    },
);

onBeforeUnmount(() => editor.value?.destroy());

type ToolbarItem = {
    icon: Component;
    label: string;
    run: () => void;
    isActive?: () => boolean;
};

const toolbar: ToolbarItem[] = [
    {
        icon: Bold,
        label: 'Bold',
        run: () => editor.value?.chain().focus().toggleBold().run(),
        isActive: () => editor.value?.isActive('bold') ?? false,
    },
    {
        icon: Italic,
        label: 'Italic',
        run: () => editor.value?.chain().focus().toggleItalic().run(),
        isActive: () => editor.value?.isActive('italic') ?? false,
    },
    {
        icon: Strikethrough,
        label: 'Strikethrough',
        run: () => editor.value?.chain().focus().toggleStrike().run(),
        isActive: () => editor.value?.isActive('strike') ?? false,
    },
    {
        icon: Heading1,
        label: 'Heading 1',
        run: () =>
            editor.value?.chain().focus().toggleHeading({ level: 1 }).run(),
        isActive: () =>
            editor.value?.isActive('heading', { level: 1 }) ?? false,
    },
    {
        icon: Heading2,
        label: 'Heading 2',
        run: () =>
            editor.value?.chain().focus().toggleHeading({ level: 2 }).run(),
        isActive: () =>
            editor.value?.isActive('heading', { level: 2 }) ?? false,
    },
    {
        icon: List,
        label: 'Bullet list',
        run: () => editor.value?.chain().focus().toggleBulletList().run(),
        isActive: () => editor.value?.isActive('bulletList') ?? false,
    },
    {
        icon: ListOrdered,
        label: 'Ordered list',
        run: () => editor.value?.chain().focus().toggleOrderedList().run(),
        isActive: () => editor.value?.isActive('orderedList') ?? false,
    },
    {
        icon: Quote,
        label: 'Blockquote',
        run: () => editor.value?.chain().focus().toggleBlockquote().run(),
        isActive: () => editor.value?.isActive('blockquote') ?? false,
    },
    {
        icon: Code,
        label: 'Code block',
        run: () => editor.value?.chain().focus().toggleCodeBlock().run(),
        isActive: () => editor.value?.isActive('codeBlock') ?? false,
    },
    {
        icon: Undo,
        label: 'Undo',
        run: () => editor.value?.chain().focus().undo().run(),
    },
    {
        icon: Redo,
        label: 'Redo',
        run: () => editor.value?.chain().focus().redo().run(),
    },
];
</script>

<template>
    <div class="rounded-xl border">
        <div class="flex flex-wrap gap-1 border-b p-1">
            <button
                v-for="item in toolbar"
                :key="item.label"
                type="button"
                :title="item.label"
                class="inline-flex size-8 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                :class="item.isActive?.() && 'bg-muted text-foreground'"
                @click="item.run"
            >
                <component :is="item.icon" class="size-4" />
            </button>
        </div>

        <EditorContent :editor="editor" />
    </div>
</template>
