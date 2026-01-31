<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { LayoutGrid, User as UserIcon, GalleryVerticalEnd, Video, Mail } from 'lucide-vue-next';
import NavUser from '@/components/NavUser.vue';
import NavMain from '@/components/NavMain.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import AppLogo from './AppLogo.vue';
import { route } from 'ziggy-js';

// Definimos la interfaz (igual que la tenías)
interface User {
    id: number;
    role: string;
    name: string;
    email: string;
    profile?: {
        position?: string;
        current_club?: string;
    } | null;
}

const page = usePage();

// CAMBIO 1: Agregamos "as User | null" para que TS sepa que puede estar vacío
const user = computed(() => page.props.auth.user as User | null);

// CAMBIO 2: Usamos "?." (optional chaining). Si user es null, devuelve false y no explota.
const isPlayer = computed(() => user.value?.role === 'player');

const mainNavItems = computed(() => [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    // Solo mostramos Mensajes si hay usuario
    ...(user.value ? [{
        title: 'Messages',
        href: route('messages.index'),
        icon: Mail,
    }] : []),

    // Highlights: Solo si existe usuario Y es player
    ...(user.value && isPlayer.value ? [{
        title: 'My Highlights',
        href: route('player.show', user.value.id),
        icon: Video,
    }] : []),

    // Perfil: Solo si existe usuario
    ...(user.value ? [{
        title: isPlayer.value ? 'Edit Player Profile' : 'Edit Scout Profile ',
        href: '/player-profile',
        icon: UserIcon,
    }] : []),

    // El Feed es público, lo dejamos siempre
    {
        title: 'Video Feed',
        href: '/feed',
        icon: GalleryVerticalEnd,
    },
]);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="user ? dashboard() : '/'">
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
            <NavUser v-if="user" :user="user" />
            
            <div v-else class="p-2">
                <Link :href="route('login')" class="flex w-full items-center justify-center rounded-md bg-green-600 py-2 text-sm font-bold text-white">
                    Log In
                </Link>
            </div>
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>