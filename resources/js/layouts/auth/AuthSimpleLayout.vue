<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="mobile-container pb-0">
        <!-- Fond décoratif décoratif -->
        <div class="absolute top-0 left-0 right-0 h-64 bg-gradient-to-b from-easygas-green to-transparent opacity-20 dark:opacity-40"></div>
        
        <div class="flex-1 flex flex-col justify-center px-6 py-12 relative z-10 w-full animate-fade-in">
            <div class="w-full">
                <div class="flex flex-col gap-6">
                    <div class="flex flex-col items-center gap-4 animate-slide-up" style="animation-delay: 0.1s;">
                        <Link
                            :href="home()"
                            class="flex flex-col items-center gap-2"
                        >
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] shadow-xl border border-gray-100 dark:border-gray-700">
                                <AppLogoIcon
                                    class="size-24 md:size-32 text-easygas-green"
                                />
                            </div>
                        </Link>
                        <div class="space-y-2 text-center mt-2">
                            <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-easygas-green to-easygas-green-dark">
                                EasyGas
                            </h1>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-4">{{ title }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-[280px]">
                                {{ description }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="animate-slide-up" style="animation-delay: 0.2s;">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
