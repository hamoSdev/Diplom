<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Activity,
    BookOpen,
    BrainCircuit,
    ChartColumn,
    Compass,
    GraduationCap,
    Inbox,
    Library,
    LayoutGrid,
    PenLine,
    Settings,
    Sparkles,
    Users,
} from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';
import { route } from 'ziggy-js';

const page = usePage();
const role = computed(() => page.props.auth.user.role);

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: route('dashboard'),
            icon: LayoutGrid,
        },
    ];

    if (role.value === 'lecturer') {
        items.push(
            {
                title: 'Courses',
                href: route('courses.index'),
                icon: BookOpen,
            },
            {
                title: 'Results',
                href: route('results.index'),
                icon: ChartColumn,
            },
        );
    }

    if (role.value === 'student') {
        items.push(
            {
                title: 'My learning',
                href: route('learning.index'),
                icon: Sparkles,
            },
            {
                title: 'Explore',
                href: route('explore.index'),
                icon: Compass,
            },
            {
                title: 'Lecturers',
                href: route('lecturers.index'),
                icon: GraduationCap,
            },
            {
                title: 'Self-check',
                href: route('exam.show'),
                icon: BrainCircuit,
            },
        );
    }

    if (role.value === 'admin') {
        items.push(
            {
                title: 'Users',
                href: route('admin.users.index'),
                icon: Users,
            },
            {
                title: 'Faculties',
                href: route('admin.faculties.index'),
                icon: GraduationCap,
            },
            {
                title: 'Subjects',
                href: route('admin.subjects.index'),
                icon: Library,
            },
            {
                title: 'Courses',
                href: route('admin.courses.index'),
                icon: BookOpen,
            },
            {
                title: 'AI monitor',
                href: route('admin.ai.index'),
                icon: Activity,
            },
            {
                title: 'Applications',
                href: route('admin.applications.index'),
                icon: Inbox,
            },
            {
                title: 'Settings',
                href: route('admin.settings.index'),
                icon: Settings,
            },
        );
    }

    if (role.value === 'lecturer' || role.value === 'admin') {
        items.push({
            title: 'Editor',
            href: route('editor.index'),
            icon: PenLine,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
