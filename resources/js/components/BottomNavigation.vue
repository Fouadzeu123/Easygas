<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Home, Gift, History, User, PlusCircle, ShieldAlert, Map as MapIcon, Users, DollarSign } from 'lucide-vue-next';
import { useTranslate } from '@/composables/useTranslate';

const { __ } = useTranslate();
const page = usePage();
const currentUser = computed(() => (page.props.auth as any)?.user);
const role = computed(() => currentUser.value?.role || 'client');

// Global route helper
const route = (name?: string, params?: any) => {
    if (typeof window !== 'undefined' && (window as any).route) {
        try {
            return (window as any).route(name, params);
        } catch {
            return '#';
        }
    }
    return '#';
};

const isCurrentRoute = (name: string) => {
    if (typeof window !== 'undefined' && (window as any).route) {
        try {
            return (window as any).route().current(name);
        } catch {
            return false;
        }
    }
    return false;
};

const navItems = computed(() => {
    if (role.value === 'admin') {
        return [
            { name: 'admin.dashboard', label: __('Nav.Console'), icon: ShieldAlert },
            { name: 'admin.activity',  label: __('Nav.Activity'), icon: History },
            { name: 'admin.users',     label: __('Nav.Members'), icon: Users },
            { name: 'admin.prices',    label: __('Nav.Rates'), icon: DollarSign },
        ];
    }
    
    if (role.value === 'ramasseur' || role.value === 'livreur') {
        return [
            { name: 'collector.dashboard', label: __('Nav.Dashboard'), icon: Home },
            { name: 'collector.missions',  label: __('Nav.Missions'),  icon: Gift },
            { name: 'collector.map',       label: __('Nav.Map'),     icon: MapIcon },
            { name: 'profile',             label: __('Nav.Profile'),    icon: User },
        ];
    }

    // fallback / client
    return [
        { name: 'dashboard',     label: __('Nav.Home'),     icon: Home },
        { name: 'rewards.index', label: __('Nav.Rewards'), icon: Gift },
        { name: 'activity',      label: __('Nav.History'),  icon: History },
        { name: 'profile',       label: __('Nav.Profile'),      icon: User },
    ];
});
</script>

<template>
    <div class="fixed bottom-0 left-0 right-0 z-50 flex justify-center pb-4 px-4 pointer-events-none">
        <div class="w-full max-w-[420px] bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl rounded-3xl flex justify-between items-center px-3 py-2 pointer-events-auto shadow-2xl border border-gray-100 dark:border-gray-800">

            <template v-if="role === 'client'">
                <!-- Accueil -->
                <Link
                    :href="route(navItems[0].name)"
                    class="flex flex-col items-center gap-0.5 px-3 py-2 rounded-2xl transition-all duration-200"
                    :class="isCurrentRoute(navItems[0].name)
                        ? 'bg-easygas-green/10 text-easygas-green'
                        : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                >
                    <component :is="navItems[0].icon" :size="22" />
                    <span class="text-[9px] font-bold">{{ navItems[0].label }}</span>
                </Link>

                <!-- Récompenses -->
                <Link
                    :href="route(navItems[1].name)"
                    class="flex flex-col items-center gap-0.5 px-3 py-2 rounded-2xl transition-all duration-200"
                    :class="isCurrentRoute(navItems[1].name)
                        ? 'bg-easygas-green/10 text-easygas-green'
                        : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                >
                    <component :is="navItems[1].icon" :size="22" />
                    <span class="text-[9px] font-bold">{{ navItems[1].label }}</span>
                </Link>

                <!-- Bouton central Commander -->
                <div class="flex-shrink-0 -mt-6 mx-1">
                    <Link
                        :href="route('order.create')"
                        class="flex flex-col items-center gap-0.5 group"
                        aria-label="Commander du gaz"
                    >
                        <div class="bg-gradient-to-br from-easygas-green to-easygas-green-dark p-3.5 rounded-2xl shadow-lg shadow-easygas-green/30 group-active:scale-90 transition-transform border-4 border-white dark:border-gray-900">
                            <PlusCircle :size="26" class="text-white" />
                        </div>
                        <span class="text-[9px] font-bold text-easygas-green mt-0.5">{{ __('Nav.Order') }}</span>
                    </Link>
                </div>

                <!-- Historique -->
                <Link
                    :href="route(navItems[2].name)"
                    class="flex flex-col items-center gap-0.5 px-3 py-2 rounded-2xl transition-all duration-200"
                    :class="isCurrentRoute(navItems[2].name)
                        ? 'bg-easygas-green/10 text-easygas-green'
                        : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                >
                    <component :is="navItems[2].icon" :size="22" />
                    <span class="text-[9px] font-bold">{{ navItems[2].label }}</span>
                </Link>

                <!-- Profil -->
                <Link
                    :href="route(navItems[3].name)"
                    class="flex flex-col items-center gap-0.5 px-3 py-2 rounded-2xl transition-all duration-200"
                    :class="isCurrentRoute(navItems[3].name)
                        ? 'bg-easygas-green/10 text-easygas-green'
                        : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                >
                    <component :is="navItems[3].icon" :size="22" />
                    <span class="text-[9px] font-bold">{{ navItems[3].label }}</span>
                </Link>
            </template>

            <!-- Navigation Staff (Admin / Collecteur) -->
            <template v-else>
                <Link
                    v-for="item in navItems"
                    :key="item.name"
                    :href="route(item.name)"
                    class="flex flex-col items-center justify-center flex-1 gap-1 py-1 px-2 rounded-2xl transition-all duration-200 hover:-translate-y-0.5"
                    :class="isCurrentRoute(item.name)
                        ? 'text-easygas-green bg-easygas-green/5'
                        : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                >
                    <component :is="item.icon" :size="24" :class="isCurrentRoute(item.name) ? 'drop-shadow-sm' : ''" />
                    <span class="text-[10px] font-bold uppercase tracking-widest mt-1">{{ item.label }}</span>
                </Link>
            </template>

        </div>
    </div>
</template>
