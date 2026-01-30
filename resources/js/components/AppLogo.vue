<script setup>
// No necesitamos scripts complejos aquí, solo es visual
</script>

<template>
    <div class="flex items-center gap-2 truncate">
        
        <div class="flex aspect-square size-8 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground">
            <svg 
              xmlns="http://www.w3.org/2000/svg" 
              viewBox="0 0 24 24" 
              fill="none" 
              stroke="currentColor" 
              stroke-width="2" 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              class="size-5"
            >
              <circle cx="12" cy="12" r="10"/>
              <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/>
              <path d="M2 12h20"/>
              <path d="M12 2v20"/>
              <path d="M4.93 4.93l14.14 14.14"/>
              <path d="M19.07 4.93L4.93 19.07"/>
            </svg>
        </div>

        <div class="grid flex-1 text-left text-sm leading-tight truncate">
            <span class="truncate font-semibold text-gray-900 dark:text-white">
                ScoutMarket
            </span>
            <span class="truncate text-xs text-gray-500 dark:text-gray-400">
                Player Scouting
            </span>
        </div>
        
    </div>
</template>