<script setup lang="ts">
import { computed } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { type User } from '@/types/auth'; // Importamos el tipo real

const props = defineProps<{
    user: User
}>();

const initials = computed(() => 
    props.user.name 
    ? props.user.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() 
    : 'U'
);

// Aquí accedemos a las propiedades de forma segura gracias al nuevo tipo Auth
const description = computed(() => {
    // Si existe perfil y tiene club...
    if (props.user.profile?.current_club) return props.user.profile.current_club;
    // Si existe perfil y tiene posición...
    if (props.user.profile?.position) return props.user.profile.position;
    
    // Si no (es Scout o player incompleto), mostramos rol o email
    return props.user.role || props.user.email;
});

// Usamos profile_photo_url si existe, o avatar como fallback
const avatarUrl = computed(() => props.user.profile_photo_url || props.user.avatar);
</script>

<template>
    <Avatar class="h-8 w-8 rounded-lg">
        <AvatarImage v-if="avatarUrl" :src="avatarUrl" :alt="user.name" />
        <AvatarFallback class="rounded-lg">{{ initials }}</AvatarFallback>
    </Avatar>
    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-semibold">{{ user.name }}</span>
        <span class="truncate text-xs text-muted-foreground">{{ description }}</span>
    </div>
</template>