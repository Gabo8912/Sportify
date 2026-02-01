<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ChevronsUpDown, LogOut, Sparkles, BadgeCheck, Bell, CreditCard } from 'lucide-vue-next';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { type AppPageProps } from '@/types';
import { route } from 'ziggy-js';

// 1. Obtener datos
const page = usePage<AppPageProps>();
const user = computed(() => page.props.auth.user);
const { isMobile, state } = useSidebar();

// 2. Generar iniciales
const initials = computed(() => 
    user.value.name
        ? user.value.name.split(' ').map((n) => n[0]).join('').substring(0, 2).toUpperCase()
        : 'U'
);

// 3. Función Logout
const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton
                        size="lg"
                        class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                    >
                        <Avatar class="h-8 w-8 rounded-lg">
                            <AvatarImage v-if="user.profile_photo_url" :src="user.profile_photo_url" :alt="user.name" />
                            <AvatarFallback class="rounded-lg">{{ initials }}</AvatarFallback>
                        </Avatar>
                        <div class="grid flex-1 text-left text-sm leading-tight">
                            <span class="truncate font-semibold">{{ user.name }}</span>
                            <span class="truncate text-xs text-muted-foreground">{{ user.email }}</span>
                        </div>
                        <ChevronsUpDown class="ml-auto size-4" />
                    </SidebarMenuButton>
                </DropdownMenuTrigger>

                <DropdownMenuContent
                    class="w-[--reka-dropdown-menu-trigger-width] min-w-56 rounded-lg"
                    :side="isMobile ? 'bottom' : (state === 'collapsed' ? 'left' : 'bottom')"
                    align="end"
                    :side-offset="4"
                >
                    <DropdownMenuLabel class="p-0 font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                            <Avatar class="h-8 w-8 rounded-lg">
                                <AvatarImage v-if="user.profile_photo_url" :src="user.profile_photo_url" :alt="user.name" />
                                <AvatarFallback class="rounded-lg">{{ initials }}</AvatarFallback>
                            </Avatar>
                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ user.name }}</span>
                                <span class="truncate text-xs text-muted-foreground">{{ user.email }}</span>
                            </div>
                        </div>
                    </DropdownMenuLabel>
                    
                    <DropdownMenuSeparator />

                    
                    <DropdownMenuSeparator />
                    
                    
                    <DropdownMenuSeparator />
                    
                    <DropdownMenuItem @click="logout">
                        <LogOut class="mr-2 h-4 w-4" />
                        <span>Log out</span>
                    </DropdownMenuItem>
                    </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
</template>