<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronDown } from '@lucide/vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { route } from 'ziggy-js';

const links = [
    { label: 'AI learning', href: route('ai-learning') },
    { label: 'Our courses', href: route('our-courses') },
    { label: 'FAQ', href: route('faq') },
];
</script>

<template>
    <nav
        class="relative z-10 mx-auto flex max-w-7xl items-center justify-between px-8 py-6"
    >
        <Link
            :href="route('home')"
            class="flex items-center gap-2.5 text-2xl font-bold tracking-tight"
        >
            <AppLogoIcon class="size-8 text-primary" />
            <span>Skill<span class="text-primary">Lab</span></span>
        </Link>

        <div class="hidden items-center gap-8 md:flex">
            <DropdownMenu>
                <DropdownMenuTrigger
                    class="flex items-center gap-1 text-sm text-white/60 transition-colors outline-none hover:text-white"
                >
                    Lecturers
                    <ChevronDown class="h-4 w-4" />
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    align="start"
                    class="liquid-glass min-w-44 rounded-2xl p-1.5 text-white"
                >
                    <DropdownMenuItem
                        as-child
                        class="cursor-pointer rounded-xl px-3 py-2 text-white focus:bg-white/10 focus:text-white"
                    >
                        <Link :href="route('become-lecturer')">
                            Become lecturer
                        </Link>
                    </DropdownMenuItem>
                    <DropdownMenuItem
                        as-child
                        class="cursor-pointer rounded-xl px-3 py-2 text-white focus:bg-white/10 focus:text-white"
                    >
                        <Link :href="route('our-lecturers')">
                            Our lecturers
                        </Link>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>

            <Link
                v-for="link in links"
                :key="link.label"
                :href="link.href"
                class="text-sm text-white/60 transition-colors hover:text-white"
            >
                {{ link.label }}
            </Link>
        </div>

        <div class="flex items-center gap-3">
            <Link
                :href="route('login')"
                class="text-sm text-white/60 transition-colors hover:text-white"
            >
                Sign in
            </Link>
            <Link
                :href="route('register')"
                class="liquid-glass rounded-full px-5 py-2 text-sm text-white transition-transform hover:scale-[1.03]"
            >
                Register
            </Link>
        </div>
    </nav>
</template>
