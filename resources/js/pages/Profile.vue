<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import MobileLayout from '@/layouts/MobileLayout.vue';
import AppCard from '@/components/AppCard.vue';
import AppButton from '@/components/AppButton.vue';
import AppBadge from '@/components/AppBadge.vue';
import { User, Settings, LogOut, Package, Trash2, Award, ChevronRight } from 'lucide-vue-next';

const props = defineProps<{
    user: any;
}>();

// Global route helper access - safe for SSR and HMR
const route = (name?: string, params?: any) => {
    if (typeof window !== 'undefined' && (window as any).route) {
        return (window as any).route(name, params);
    }
    return '#';
};

const logoutForm = useForm({});

const logout = () => {
    logoutForm.post(route('logout'));
};

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'en_attente':
        case 'signale':
            return 'info';
        case 'en_cours':
            return 'warning';
        case 'livre':
        case 'collecte':
            return 'success';
        case 'annule':
            return 'error';
        default:
            return 'default';
    }
};

const formatStatus = (status: string) => {
    return status.replace('_', ' ').toUpperCase();
};
</script>

<template>
    <Head title="Mon Profil" />

    <MobileLayout>
        <!-- Header Profil -->
        <div class="flex flex-col items-center py-8 bg-gradient-to-b from-easygas-green/10 to-transparent rounded-b-[3rem] -mx-6 mb-6 px-6">
            <div class="relative mb-4">
                <div class="w-24 h-24 rounded-full bg-easygas-green flex items-center justify-center text-white text-3xl font-bold border-4 border-white dark:border-gray-900 shadow-xl overflow-hidden">
                    <img v-if="user.avatar" :src="user.avatar" class="w-full h-full object-cover" />
                    <span v-else>{{ user.name.charAt(0) }}</span>
                </div>
                <button class="absolute bottom-0 right-0 p-2 bg-white dark:bg-gray-800 rounded-full shadow-lg border border-gray-100 dark:border-gray-700">
                    <Settings class="w-4 h-4 text-gray-500" />
                </button>
            </div>
            
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ user.name }}</h1>
            <p class="text-gray-500 text-sm mb-4">{{ user.phone }}</p>
            
            <div class="flex gap-4 w-full max-w-xs">
                <div class="flex-1 bg-white dark:bg-gray-800 p-3 rounded-2xl shadow-sm text-center border border-gray-100 dark:border-gray-700">
                    <p class="text-xs text-gray-400 mb-1">Points</p>
                    <div class="flex items-center justify-center gap-1">
                        <Award class="w-4 h-4 text-yellow-500" />
                        <span class="font-bold text-gray-800 dark:text-gray-100">{{ user.points || 0 }}</span>
                    </div>
                </div>
                <div class="flex-1 bg-white dark:bg-gray-800 p-3 rounded-2xl shadow-sm text-center border border-gray-100 dark:border-gray-700">
                    <p class="text-xs text-gray-400 mb-1">Commandes</p>
                    <div class="flex items-center justify-center gap-1">
                        <Package class="w-4 h-4 text-easygas-green" />
                        <span class="font-bold text-gray-800 dark:text-gray-100">{{ user.orders?.length || 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique Récent -->
        <div class="space-y-6 pb-20">
            <div>
                <div class="flex justify-between items-end mb-4">
                    <h2 class="font-bold text-gray-800 dark:text-gray-200">Activités récentes</h2>
                    <Link :href="route('activity')" class="text-xs text-easygas-green font-medium hover:underline">Voir tout</Link>
                </div>
                
                <div v-if="[...user.orders, ...user.wastes].length === 0" class="text-center py-10 text-gray-400 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border-2 border-dashed border-gray-100 dark:border-gray-800">
                    Pas d'activité récente.
                </div>
                
                <div v-else class="space-y-3">
                    <!-- Orders -->
                    <div v-for="order in user.orders.slice(0, 3)" :key="'order-'+order.id" class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                            <Package class="w-6 h-6 text-orange-600" />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-sm text-gray-800 dark:text-gray-100">Commande Gaz ({{ order.quantity }}kg)</h3>
                            <p class="text-[10px] text-gray-400">{{ new Date(order.created_at).toLocaleDateString() }}</p>
                        </div>
                        <AppBadge :variant="getStatusVariant(order.status)">{{ formatStatus(order.status) }}</AppBadge>
                    </div>

                    <!-- Wastes -->
                    <div v-for="waste in user.wastes.slice(0, 3)" :key="'waste-'+waste.id" class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <Trash2 class="w-6 h-6 text-green-600" />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-sm text-gray-800 dark:text-gray-100">Apport Déchets ({{ waste.type }})</h3>
                            <p class="text-[10px] text-gray-400">{{ new Date(waste.created_at).toLocaleDateString() }}</p>
                        </div>
                        <AppBadge :variant="getStatusVariant(waste.status)">{{ formatStatus(waste.status) }}</AppBadge>
                    </div>
                </div>
            </div>

            <!-- Menu de réglages -->
            <div class="space-y-2">
                <h2 class="font-bold text-gray-800 dark:text-gray-200 mb-2">Application</h2>
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <Link :href="route('profile.edit')" class="w-full flex items-center gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors text-left">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg"><User class="w-4 h-4 text-blue-600" /></div>
                        <span class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-200">Modifier mon profil</span>
                        <ChevronRight class="w-4 h-4 text-gray-300" />
                    </Link>
                    <div class="h-px bg-gray-50 dark:bg-gray-700 mx-4"></div>
                    <button class="w-full flex items-center gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors text-left font-bold text-red-500" @click="logout" :disabled="logoutForm.processing">
                        <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg"><LogOut class="w-4 h-4 text-red-600" /></div>
                        <span class="flex-1 text-sm">Déconnexion</span>
                    </button>
                </div>
            </div>
        </div>
    </MobileLayout>
</template>
