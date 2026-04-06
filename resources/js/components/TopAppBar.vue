<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Bell, LogIn, Moon, Search, Sun, User, History } from 'lucide-vue-next';
import { computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';

const page = usePage();
const user = computed(() => (page.props.auth as any)?.user);
const { appearance, updateAppearance } = useAppearance();

// Use global route helper with SSR/HMR safety
const route = (name?: string, params?: any) => {
    if (typeof window !== 'undefined' && (window as any).route) {
        try {
            if (!name) {
                return (window as any).route();
            }

            return (window as any).route(name, params);
        } catch {
            return '#';
        }
    }

    return '#';
};

const toggleAppearance = () => {
    updateAppearance(appearance.value === 'dark' ? 'light' : 'dark');
};

const isHomePage = computed(() => {
    try {
        const r = route() as any;
        return r && typeof r.current === 'function' ? r.current('home') : false;
    } catch {
        return false;
    }
});

const isCurrentRoute = (name: string) => {
    try {
        const r = route() as any;
        return r && typeof r.current === 'function' ? r.current(name) : false;
    } catch {
        return false;
    }
};

defineProps<{
    title?: string;
}>();
</script>

<template>
    <div class="sticky top-0 left-0 right-0 z-50 bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl border-b border-gray-100 dark:border-gray-800 transition-colors duration-300">
        <div class="w-full px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <!-- Logo -->
                <Link :href="route('home')" class="flex items-center gap-2 group">
                    <img src="/img/logo-easygas.png" alt="EasyGas Logo" class="w-10 h-10 object-contain shadow-sm rounded-lg" />
                    <span class="font-black text-lg text-easygas-accent dark:text-white tracking-tight">EasyGas</span>
                </Link>
                
                <!-- Page Title -->
                <h1 v-if="title && !isHomePage" class="text-sm font-bold text-gray-900 dark:text-white truncate max-w-[140px] border-l border-gray-200 dark:border-gray-700 pl-3 ml-1">
                    {{ title }}
                </h1>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                <!-- Theme Toggle -->
                <button @click="toggleAppearance" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors focus:outline-none" title="Changer de thème">
                    <Sun v-if="appearance === 'dark'" :size="20" class="text-yellow-400" />
                    <Moon v-else :size="20" class="text-gray-600 dark:text-gray-400" />
                </button>

                <!-- Auth Buttons for Guests -->
                <template v-if="!user">
                    <Link :href="route('login')" class="flex items-center p-2 text-gray-700 dark:text-gray-200 hover:text-easygas-green transition-colors focus:outline-none" aria-label="Connexion">
                        <LogIn :size="20" />
                    </Link>
                </template>

                <!-- Icons for Authenticated Users -->
                <template v-else>
                    <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors relative focus:outline-none">
                        <Bell :size="20" class="text-gray-600 dark:text-gray-400" />
                        <span class="absolute top-2.5 right-2 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-gray-900 animate-pulse"></span>
                    </button>
                    
                    <!-- Historique (replaced Profile) -->
                    <Link :href="route('activity')" class="ml-1 p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors focus:outline-none" aria-label="Historique">
                        <History :size="20" class="text-gray-600 dark:text-gray-400" />
                    </Link>
                </template>
            </div>
        </div>
    </div>
</template>
