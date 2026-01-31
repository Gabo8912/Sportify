<script setup>
import { ref, onUpdated, nextTick, onMounted, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { Send, User, Shield, MessageCircle } from 'lucide-vue-next';

const props = defineProps({
    conversations: Array, // Lista de contactos
    messages: Array,      // Historial del chat seleccionado
    selectedUser: Object  // Usuario seleccionado (si hay uno)
});

const messageContainer = ref(null);

const form = useForm({
    receiver_id: props.selectedUser?.id,
    body: ''
});

// Función para bajar el scroll al último mensaje
const scrollToBottom = () => {
    if (messageContainer.value) {
        messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
    }
};

// Enviar mensaje
const sendMessage = () => {
    if (!form.body.trim()) return;
    
    // Aseguramos que el receiver_id esté actualizado
    form.receiver_id = props.selectedUser.id;

    form.post(route('messages.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            nextTick(() => scrollToBottom());
        }
    });
};

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

onMounted(() => scrollToBottom());
onUpdated(() => scrollToBottom());

watch(() => props.selectedUser, (newUser) => {
    if (newUser) {
        form.receiver_id = newUser.id;
        form.body = '';
        nextTick(() => scrollToBottom());
    }
});
</script>

<template>
    <AppLayout title="Chat">
        <div class="h-[calc(100vh-65px)] flex overflow-hidden bg-gray-100 dark:bg-gray-900">
            
            <div 
                :class="['w-full md:w-1/3 lg:w-1/4 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col', selectedUser ? 'hidden md:flex' : 'flex']"
            >
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <MessageCircle class="w-5 h-5" /> Chats
                    </h2>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <div v-if="conversations.length === 0" class="p-6 text-center text-gray-500 text-sm">
                        No conversations yet. <br> Visit a player's profile to start chatting!
                    </div>

                    <ul v-else class="divide-y divide-gray-100 dark:divide-gray-700">
                        <li v-for="contact in conversations" :key="contact.id">
                            <Link 
                                :href="route('messages.index', { user_id: contact.id })"
                                class="flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                :class="{'bg-blue-50 dark:bg-gray-700 border-l-4 border-blue-500': selectedUser?.id === contact.id}"
                            >
                                <img :src="contact.profile_photo_url" alt="" class="h-10 w-10 rounded-full object-cover">
                                <div class="ml-3 flex-1 min-w-0">
                                    <div class="flex justify-between items-baseline">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ contact.name }}
                                        </h3>
                                        <span v-if="contact.role === 'scout'" class="text-xs text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 px-1 rounded">Scout</span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        Click to view chat
                                    </p>
                                </div>
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <div 
                :class="['flex-1 flex flex-col bg-gray-50 dark:bg-gray-900 w-full', !selectedUser ? 'hidden md:flex' : 'flex']"
            >
                <div v-if="selectedUser" class="p-3 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center shadow-sm z-10">
                    <div class="flex items-center">
                        <Link :href="route('messages.index')" class="md:hidden mr-3 text-gray-500">
                            &larr; Back
                        </Link>
                        
                        <img :src="selectedUser.profile_photo_url" class="h-10 w-10 rounded-full border border-gray-200 dark:border-gray-600">
                        <div class="ml-3">
                            <h3 class="text-md font-bold text-gray-800 dark:text-white flex items-center gap-1">
                                {{ selectedUser.name }}
                            </h3>
                            <Link :href="route('player.show', selectedUser.id)" class="text-xs text-indigo-600 hover:underline">
                                View Profile
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-if="selectedUser" class="flex-1 overflow-y-auto p-4 space-y-4" ref="messageContainer">
                    <div v-if="messages.length === 0" class="text-center text-gray-400 mt-10">
                        <p>This is the beginning of your conversation with {{ selectedUser.name }}.</p>
                    </div>

                    <div 
                        v-for="msg in messages" 
                        :key="msg.id" 
                        class="flex w-full"
                        :class="msg.sender_id === $page.props.auth.user.id ? 'justify-end' : 'justify-start'"
                    >
                        <div 
                            class="max-w-[75%] px-4 py-2 rounded-lg shadow-sm text-sm"
                            :class="msg.sender_id === $page.props.auth.user.id 
                                ? 'bg-indigo-600 text-white rounded-br-none' 
                                : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-bl-none border border-gray-200 dark:border-gray-600'"
                        >
                            <p class="whitespace-pre-wrap">{{ msg.body }}</p>
                            <div 
                                class="text-[10px] mt-1 text-right"
                                :class="msg.sender_id === $page.props.auth.user.id ? 'text-indigo-200' : 'text-gray-400'"
                            >
                                {{ formatTime(msg.created_at) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="selectedUser" class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                    <form @submit.prevent="sendMessage" class="flex gap-2">
                        <input 
                            v-model="form.body"
                            type="text" 
                            placeholder="Type a message..." 
                            class="flex-1 rounded-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition px-4"
                        >
                        <button 
                            type="submit" 
                            :disabled="form.processing || !form.body"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white p-3 rounded-full shadow-lg disabled:opacity-50 transition transform hover:scale-105"
                        >
                            <Send class="w-5 h-5" />
                        </button>
                    </form>
                </div>

                <div v-else class="flex-1 flex flex-col items-center justify-center text-gray-400">
                    <div class="bg-gray-200 dark:bg-gray-700 p-6 rounded-full mb-4">
                        <MessageCircle class="w-12 h-12 text-gray-500 dark:text-gray-400" />
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300">Select a Conversation</h3>
                    <p class="mt-2 text-sm">Pick a contact from the left to view your chat history.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>