<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { History, Flame, Trash2, Clock, ShoppingBag, ArrowDownLeft, ArrowUpRight } from 'lucide-vue-next';
import MobileLayout from '@/layouts/MobileLayout.vue';

const props = defineProps<{
    orders: Array<{
        id: number;
        type: string;
        title: string;
        date: string;
        status: string;
        amount: string;
        isPositive: boolean;
    }>;
    wastes: Array<{
        id: number;
        type: string;
        title: string;
        date: string;
        status: string;
        amount: string;
        isPositive: boolean;
    }>;
}>();

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

type Tab = 'all' | 'orders' | 'wastes';
const activeTab = ref<Tab>('all');

const allActivities = computed(() => {
    const orders = props.orders.map(o => ({ ...o, icon: Flame, bg: 'bg-orange-100 dark:bg-orange-900/30', color: 'text-orange-500' }));
    const wastes = props.wastes.map(w => ({ ...w, icon: Trash2, bg: 'bg-green-100 dark:bg-green-900/30', color: 'text-green-500' }));
    return [...orders, ...wastes].sort((a, b) => a.id < b.id ? 1 : -1);
});

const filteredActivities = computed(() => {
    if (activeTab.value === 'orders') return allActivities.value.filter(a => a.type === 'gaz');
    if (activeTab.value === 'wastes') return allActivities.value.filter(a => a.type === 'recyclage');
    return allActivities.value;
});

const totalSpent = computed(() =>
    props.orders.reduce((sum, o) => {
        const match = o.amount.replace(/\s/g, '').match(/-([\d]+)/);
        return sum + (match ? parseInt(match[1]) : 0);
    }, 0)
);

const totalPoints = computed(() =>
    props.wastes.reduce((sum, w) => {
        const match = w.amount.match(/\+(\d+)/);
        return sum + (match ? parseInt(match[1]) : 0);
    }, 0)
);
</script>

<template>
    <Head title="Historique" />

    <MobileLayout title="Mes Activités">
        <div class="px-1 pt-4 pb-24">
            <!-- Summary Card -->
            <div class="grid grid-cols-2 gap-3 mb-6">
                <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800 p-4 rounded-2xl">
                    <div class="flex items-center gap-2 mb-1">
                        <ArrowUpRight :size="14" class="text-orange-500" />
                        <span class="text-[10px] font-bold uppercase tracking-wide text-orange-500">Dépenses</span>
                    </div>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ totalSpent.toLocaleString() }} <span class="text-xs font-normal">FCFA</span></p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 p-4 rounded-2xl">
                    <div class="flex items-center gap-2 mb-1">
                        <ArrowDownLeft :size="14" class="text-easygas-green" />
                        <span class="text-[10px] font-bold uppercase tracking-wide text-easygas-green">Points Gagnés</span>
                    </div>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ totalPoints }} <span class="text-xs font-normal">pts</span></p>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="flex gap-2 mb-6 p-1 bg-gray-100 dark:bg-gray-800 rounded-2xl">
                <button
                    @click="activeTab = 'all'"
                    :class="['flex-1 py-2.5 text-xs font-bold rounded-xl transition-all', activeTab === 'all' ? 'bg-white dark:bg-gray-700 shadow-sm text-easygas-green' : 'text-gray-400']"
                >Tout</button>
                <button
                    @click="activeTab = 'orders'"
                    :class="['flex-1 py-2.5 text-xs font-bold rounded-xl transition-all', activeTab === 'orders' ? 'bg-white dark:bg-gray-700 shadow-sm text-easygas-green' : 'text-gray-400']"
                >Commandes</button>
                <button
                    @click="activeTab = 'wastes'"
                    :class="['flex-1 py-2.5 text-xs font-bold rounded-xl transition-all', activeTab === 'wastes' ? 'bg-white dark:bg-gray-700 shadow-sm text-easygas-green' : 'text-gray-400']"
                >Recyclage</button>
            </div>

            <!-- Activity List -->
            <div v-if="filteredActivities.length > 0" class="space-y-3">
                <div
                    v-for="item in filteredActivities"
                    :key="item.type + item.id"
                    class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-4 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow"
                >
                    <div :class="[item.bg, 'p-3 rounded-2xl flex-shrink-0']">
                        <component :is="item.icon" :class="item.color" :size="22" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ item.title }}</h4>
                        <p class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-1">
                            <Clock :size="10" /> {{ item.date }}
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p :class="[item.isPositive ? 'text-easygas-green' : 'text-orange-500', 'font-bold text-sm']">
                            {{ item.amount }}
                        </p>
                        <p class="text-[10px] font-medium mt-1" :class="item.status === 'En livraison' || item.status === 'Assigné' ? 'text-blue-500' : 'text-gray-400'">
                            <span v-if="item.status === 'En livraison' || item.status === 'Assigné'" class="inline-block w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse mr-1"></span>
                            {{ item.status }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="flex flex-col items-center justify-center py-20 text-center">
                <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-full mb-4">
                    <History :size="48" class="text-gray-300 dark:text-gray-600" />
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white">Aucune activité</h3>
                <p class="text-sm text-gray-400 mt-1 mb-6">Vos futures commandes et recyclages apparaîtront ici.</p>
                <Link :href="route('order.create')" class="btn-primary">
                    <ShoppingBag :size="16" class="mr-2" />
                    Commander du gaz
                </Link>
            </div>
        </div>
    </MobileLayout>
</template>
