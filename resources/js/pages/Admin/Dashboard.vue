<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import {
    Bell,
    Flame,
    Package,
    Trash2,
    TrendingUp,
    Users,
} from 'lucide-vue-next';
import { ref, watch } from 'vue';
import AppBadge from '@/components/AppBadge.vue';
import AppButton from '@/components/AppButton.vue';
import AppInput from '@/components/AppInput.vue';
import AppMap from '@/components/AppMap.vue';
import MobileLayout from '@/layouts/MobileLayout.vue';

const props = defineProps<{
    stats: {
        total_revenue:         number;
        total_gas_kg:          number;
        total_waste_kg:        number;
        active_users:          number;
        active_collectors:     number;
        pending_orders_count:  number;
        pending_wastes_count:  number;
    };
    recentOrders: any[];
    recentWastes: any[];
    users:        any[];
    prices:       any;
    mapMarkers:   any[];
    tab?:         string;
}>();

const activeTab = ref(props.tab || 'overview');

// Sync activeTab if inertia navigating via BottomNav
watch(() => props.tab, (newVal) => {
    if (newVal) {
        activeTab.value = newVal;
    }
});

// Statuts conformes aux enums DB
const getStatusVariant = (status: string) => {
    switch (status) {
        case 'en_attente': return 'info';
        case 'confirme':   return 'warning';
        case 'en_livraison': return 'warning';
        case 'livre':      return 'success';
        case 'annule':     return 'error';
        case 'signale':    return 'info';
        case 'assigne':    return 'warning';
        case 'collecte':   return 'success';
        case 'traite':     return 'success';
        default:           return 'default';
    }
};

const statusLabels: Record<string, string> = {
    en_attente:   'En attente',
    confirme:     'Confirmé',
    en_livraison: 'En livraison',
    livre:        'Livré',
    annule:       'Annulé',
    signale:      'Signalé',
    assigne:      'Assigné',
    collecte:     'Collecté',
    traite:       'Traité',
};

const formatStatus = (status: string) => statusLabels[status] ?? status.replace('_', ' ');

const r = (name: string, params?: any) => {
    if (typeof window !== 'undefined' && (window as any).route) {
        try { return (window as any).route(name, params); } catch { return '#'; }
    }
    return '#';
};

const updateRole = (userId: number, newRole: string) => {
    if (confirm(`Voulez-vous vraiment changer ce rôle en ${newRole} ?`)) {
        router.patch(r('admin.users.updateRole', userId), { role: newRole }, { preserveScroll: true });
    }
};

const priceForm = useForm({
    gas_price_per_kg: props.prices?.gas_price_per_kg || 500,
    waste_reward_per_kg: props.prices?.waste_reward_per_kg || 10,
    delivery_fee: props.prices?.delivery_fee || 1000,
});

const submitPrices = () => {
    priceForm.patch(r('admin.prices.update'), {
        preserveScroll: true,
        onSuccess: () => alert('Tarifs mis à jour avec succès.')
    });
};

</script>

<template>
    <Head title="Console Administration" />

    <MobileLayout>
        <!-- Header Admin -->
        <div class="mb-6 flex justify-between items-center -mx-5 px-5 py-5 bg-gray-950 text-white rounded-b-3xl shadow-xl">
            <div class="flex items-center gap-3">
                <img src="/img/logo-easygas.png" alt="Logo" class="w-11 h-11 object-contain bg-white p-1.5 rounded-xl shadow-lg" />
                <div>
                    <h1 class="text-base font-black tracking-tight uppercase">Admin Console</h1>
                    <p class="text-[10px] opacity-50 font-bold uppercase tracking-widest">Contrôle Global EasyGas</p>
                </div>
            </div>
            <div class="relative">
                <button class="p-2 bg-white/10 rounded-xl hover:bg-white/20 transition-colors">
                    <Bell class="w-5 h-5 text-gray-300" />
                </button>
                <span
                    v-if="stats.pending_orders_count + stats.pending_wastes_count > 0"
                    class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full border-2 border-gray-950 text-[9px] flex items-center justify-center font-black"
                >
                    {{ stats.pending_orders_count + stats.pending_wastes_count }}
                </span>
            </div>
        </div>

        <div v-if="activeTab === 'overview'" class="pb-24">
            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-3 mb-6">
                <!-- ... existing stats grid items ... -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-2xl p-4">
                    <TrendingUp class="w-5 h-5 text-blue-600 dark:text-blue-400 mb-2" />
                    <p class="text-[10px] text-blue-600 dark:text-blue-400 uppercase font-bold mb-1">Revenu Total</p>
                    <p class="text-xl font-black text-blue-900 dark:text-blue-200">
                        {{ stats.total_revenue.toLocaleString() }} <span class="text-xs font-normal">FCFA</span>
                    </p>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800 rounded-2xl p-4">
                    <Flame class="w-5 h-5 text-orange-600 dark:text-orange-400 mb-2" />
                    <p class="text-[10px] text-orange-600 dark:text-orange-400 uppercase font-bold mb-1">Gaz Livré</p>
                    <p class="text-xl font-black text-orange-900 dark:text-orange-200">
                        {{ stats.total_gas_kg }} <span class="text-xs font-normal">kg</span>
                    </p>
                </div>

                <div class="bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 rounded-2xl p-4">
                    <Trash2 class="w-5 h-5 text-green-600 dark:text-green-400 mb-2" />
                    <p class="text-[10px] text-green-600 dark:text-green-400 uppercase font-bold mb-1">Déchets Collectés</p>
                    <p class="text-xl font-black text-green-900 dark:text-green-200">
                        {{ stats.total_waste_kg }} <span class="text-xs font-normal">kg</span>
                    </p>
                </div>

                <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800 rounded-2xl p-4">
                    <Users class="w-5 h-5 text-purple-600 dark:text-purple-400 mb-2" />
                    <p class="text-[10px] text-purple-600 dark:text-purple-400 uppercase font-bold mb-1">Communauté</p>
                    <p class="text-xl font-black text-purple-900 dark:text-purple-200">
                        {{ stats.active_users + stats.active_collectors }}
                        <span class="text-xs font-normal">membres</span>
                    </p>
                </div>
            </div>

            <!-- Mini-stats pendantes -->
            <div class="grid grid-cols-2 gap-3 mb-6">
                <!-- ... existing pending items ... -->
                <div class="flex items-center gap-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-4">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/40 rounded-xl flex-shrink-0">
                        <Package class="w-5 h-5 text-yellow-700 dark:text-yellow-400" />
                    </div>
                    <div>
                        <p class="text-xl font-black text-yellow-900 dark:text-yellow-200">{{ stats.pending_orders_count }}</p>
                        <p class="text-[10px] text-yellow-600 dark:text-yellow-400 font-bold uppercase">Commandes</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800 rounded-2xl p-4">
                    <div class="p-2 bg-teal-100 dark:bg-teal-900/40 rounded-xl flex-shrink-0">
                        <Trash2 class="w-5 h-5 text-teal-700 dark:text-teal-400" />
                    </div>
                    <div>
                        <p class="text-xl font-black text-teal-900 dark:text-teal-200">{{ stats.pending_wastes_count }}</p>
                        <p class="text-[10px] text-teal-600 dark:text-teal-400 font-bold uppercase">Signalements</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tables récentes (Activité) -->
        <div v-if="activeTab === 'activity'" class="space-y-8 pb-24">
            <!-- Commandes récentes -->
            <section>
                <div class="flex justify-between items-center mb-3">
                    <h2 class="font-black text-gray-900 dark:text-white text-base">Commandes Récentes</h2>
                    <button class="text-[10px] text-easygas-green font-bold uppercase tracking-widest">Voir tout</button>
                </div>
                <div class="space-y-2">
                    <div v-for="order in recentOrders" :key="order.id" class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl px-4 py-3 flex justify-between items-center shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center font-black text-sm text-orange-600 dark:text-orange-400">
                                {{ order.user.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ order.user.name }}</p>
                                <p class="text-[10px] text-gray-400 font-medium">{{ order.quantity }}kg · {{ order.price.toLocaleString() }} FCFA</p>
                            </div>
                        </div>
                        <AppBadge :variant="getStatusVariant(order.status)">{{ formatStatus(order.status) }}</AppBadge>
                    </div>
                    <div v-if="recentOrders.length === 0" class="text-center py-8 text-gray-400 dark:text-gray-500 text-sm">
                        Aucune commande récente.
                    </div>
                </div>
            </section>

            <!-- Apports déchets récents -->
            <section>
                <div class="flex justify-between items-center mb-3">
                    <h2 class="font-black text-gray-900 dark:text-white text-base">Apports Déchets</h2>
                    <button class="text-[10px] text-easygas-green font-bold uppercase tracking-widest">Voir tout</button>
                </div>
                <div class="space-y-2">
                    <div
                        v-for="waste in recentWastes"
                        :key="waste.id"
                        class="bg-white dark:bg-gray-800 border-l-4 border border-gray-100 dark:border-gray-700 rounded-2xl px-4 py-3 flex justify-between items-center shadow-sm"
                        :class="waste.status === 'collecte' || waste.status === 'traite' ? 'border-l-green-500' : 'border-l-blue-400'"
                    >
                        <div>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ waste.user.name }}</p>
                            <p class="text-[10px] text-gray-400 font-medium capitalize">{{ waste.type }} · {{ waste.quantity ?? '?' }} kg</p>
                        </div>
                        <AppBadge :variant="getStatusVariant(waste.status)">{{ formatStatus(waste.status) }}</AppBadge>
                    </div>
                    <div v-if="recentWastes.length === 0" class="text-center py-8 text-gray-400 dark:text-gray-500 text-sm">
                        Aucun apport récent.
                    </div>
                </div>
            </section>
        </div>

        <!-- Section Utilisateurs -->
        <div v-if="activeTab === 'users'" class="space-y-4 pb-24">
            <h2 class="font-black text-gray-900 dark:text-white text-base mb-3">Gestion des Rôles</h2>
            
            <div v-for="u in users" :key="u.id" class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-4 flex flex-col gap-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center font-black text-sm text-blue-600 dark:text-blue-400">
                        {{ u.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ u.name }}</p>
                        <p class="text-[10px] text-gray-400 font-medium">{{ u.email }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <label :for="'role-'+u.id" class="text-xs text-gray-500 font-bold uppercase">Rôle :</label>
                    <select 
                        :id="'role-'+u.id" 
                        class="text-xs flex-1 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-easygas-green focus:border-easygas-green transition-colors"
                        @change="updateRole(u.id, $event.target.value)"
                    >
                        <option value="client">Client</option>
                        <option value="ramasseur">Ramasseur</option>
                        <option value="livreur">Livreur</option>
                        <option value="admin">Administrateur</option>
                    </select>
                </div>
            </div>
            
            <div v-if="users.length === 0" class="text-center py-8 text-gray-400 dark:text-gray-500 text-sm">
                Aucun utilisateur.
            </div>
        </div>

        <!-- Section Tarifs -->
        <div v-if="activeTab === 'prices'" class="space-y-6 pb-24">
            <h2 class="font-black text-gray-900 dark:text-white text-base">Configuration des Tarifs</h2>
            
            <form @submit.prevent="submitPrices" class="bg-white dark:bg-gray-800 rounded-3xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm space-y-5">
                <AppInput 
                    id="gas_price" 
                    type="number" 
                    label="Prix du Gaz (FCFA / kg)" 
                    v-model="priceForm.gas_price_per_kg" 
                    placeholder="Ex: 500"
                />
                
                <AppInput 
                    id="waste_reward" 
                    type="number" 
                    label="Points/Gains par Kg de Déchet" 
                    v-model="priceForm.waste_reward_per_kg" 
                    placeholder="Ex: 10"
                />
                
                <AppInput 
                    id="delivery_fee" 
                    type="number" 
                    label="Frais de livraison fixes (FCFA)" 
                    v-model="priceForm.delivery_fee" 
                    placeholder="Ex: 1000"
                />

                <AppButton type="submit" variant="primary" class="w-full" :loading="priceForm.processing">
                    Enregistrer les tarifs
                </AppButton>
            </form>
        </div>

        <!-- Section Carte Globale -->
        <div v-if="activeTab === 'map'" class="pb-24">
            <div class="flex justify-between items-center mb-3">
                <h2 class="font-black text-gray-900 dark:text-white text-base">Carte Globale</h2>
            </div>
            
            <div class="h-[60vh] w-full rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <AppMap 
                    :markers="mapMarkers" 
                    :center="[5.36, -3.94]" 
                    :zoom="11" 
                    height="100%" 
                />
            </div>
            
            <div class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-sm mb-2">Légende</h3>
                <div class="flex flex-wrap gap-4 text-xs font-bold text-gray-600 dark:text-gray-300">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-orange-500"></span> Commandes Gaz
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span> Signalements Déchets
                    </div>
                </div>
            </div>
        </div>

    </MobileLayout>
</template>
