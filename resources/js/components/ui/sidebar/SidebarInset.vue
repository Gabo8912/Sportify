<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <main
    :class="cn(
      'relative flex min-h-svh flex-1 flex-col transition-colors duration-300',
      'bg-white dark:bg-gray-950', 
      props.class
    )"
  >
    <slot />
  </main>
</template>