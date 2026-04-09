<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useTranslate } from '@/composables/useTranslate';
import {
    Award,
    ChevronRight,
    Flame,
    Leaf,
    Gift,
    TrendingUp,
    Trash2,
} from 'lucide-vue-next';
import MobileLayout from '@/layouts/MobileLayout.vue';

const { __ } = useTranslate();

// Global route helper
const route = (name?: string, params?: any) => {
    if (typeof window !== 'undefined' && (window as any).route) {
        try { return (window as any).route(name, params); } catch { return '#'; }
    }
    return '#';
};

const page = usePage();
const user = computed(() => (page.props.auth as any)?.user);

const props = defineProps<{
    stats: Array<{ label: string; value: string; icon: string; color: string; bg: string }>;
    upcomingDeliveries: Array<{ id: number; type: string; status: string; time: string; driver: string }>;
}>();

const iconMap: Record<string, any> = { Flame, Trash2, Leaf, TrendingUp };

const loyaltyProgress = computed(() => {
    const pts = user.value?.points ?? 0;
    return pts % 100;
});

const ptsToNextReward = computed(() => 100 - loyaltyProgress.value);

const statusColor: Record<string, string> = {
    [__('Status.Pending')]:    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
    [__('Status.Confirmed')]:     'bg-blue-100  text-blue-700   dark:bg-blue-900/30   dark:text-blue-400',
    [__('Status.Delivering')]:  'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
};
</script>

<template>
    <Head :title="__('Dashboard.Title')" />

    <MobileLayout :title="__('Dashboard.Title')">
        <!-- ─── Carte fidélité ─────────────────────────────── -->
        <div class="relative bg-gradient-to-br from-easygas-green via-easygas-green-dark to-[#052218] p-6 rounded-3xl text-white overflow-hidden shadow-2xl shadow-easygas-green/20 mb-6">
            <!-- Décoration de fond -->
            <div class="absolute -top-8 -right-8 w-40 h-40 bg-white/5 rounded-full"></div>
            <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-white/5 rounded-full"></div>

            <div class="relative z-10">
                <div class="flex items-start justify-between mb-5">
                    <div>
                        <p class="text-xs text-green-200 uppercase tracking-widest font-bold mb-1">{{ __("Dashboard.Hello") }}</p>
                        <h2 class="text-2xl font-black leading-tight">{{ user?.name?.split(' ')[0] }}</h2>
                    </div>
                    <div class="bg-white/15 backdrop-blur-sm px-3 py-1.5 rounded-full flex items-center gap-1.5">
                        <Award class="w-4 h-4 text-yellow-300" />
                        <span class="font-black text-sm">{{ user?.points ?? 0 }} {{ __("Dashboard.pts") }}</span>
                    </div>
                </div>

                <!-- Barre de progression fidélité -->
                <div class="bg-white/20 rounded-full h-2 mb-2 overflow-hidden">
                    <div
                        class="bg-yellow-300 h-full rounded-full transition-all duration-1000"
                        :style="{ width: loyaltyProgress + '%' }"
                    ></div>
                </div>
                <p class="text-[11px] text-green-100">
                    {{ __("Dashboard.Points to next reward", { points: ptsToNextReward.toString() }) }}
                </p>
            </div>
        </div>

        <!-- ─── Actions rapides ────────────────────────────── -->
        <div class="grid grid-cols-3 gap-3 mb-6">
            <Link :href="route('order.create')" class="col-span-3 group">
                <div class="bg-gradient-to-r from-easygas-green to-easygas-green-dark p-5 rounded-2xl shadow-lg shadow-easygas-green/20 flex items-center gap-4 group-active:scale-[0.98] transition-transform">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <Flame :size="28" class="text-white" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-white font-bold text-lg leading-tight">{{ __("Dashboard.Order Gas") }}</h3>
                        <p class="text-white/70 text-xs mt-0.5">{{ __("Dashboard.Free home delivery") }}</p>
                    </div>
                    <ChevronRight :size="20" class="text-white/60 group-hover:translate-x-1 transition-transform" />
                </div>
            </Link>

            <Link :href="route('waste.create')" class="group">
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-4 rounded-2xl shadow-sm flex flex-col items-center gap-2 group-active:scale-95 transition-transform h-full">
                    <div class="bg-blue-100 dark:bg-blue-900/30 p-2.5 rounded-xl">
                        <Trash2 :size="22" class="text-blue-600 dark:text-blue-400" />
                    </div>
                    <span class="font-bold text-xs text-gray-700 dark:text-gray-200 text-center leading-tight">{{ __("Dashboard.Report Wastes") }}</span>
                </div>
            </Link>

            <Link :href="route('rewards.index')" class="group">
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-4 rounded-2xl shadow-sm flex flex-col items-center gap-2 group-active:scale-95 transition-transform h-full">
                    <div class="bg-purple-100 dark:bg-purple-900/30 p-2.5 rounded-xl">
                        <Gift :size="22" class="text-purple-600 dark:text-purple-400" />
                    </div>
                    <span class="font-bold text-xs text-gray-700 dark:text-gray-200 text-center leading-tight">{{ __("Dashboard.Rewards") }}</span>
                </div>
            </Link>

            <Link :href="route('activity')" class="group">
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-4 rounded-2xl shadow-sm flex flex-col items-center gap-2 group-active:scale-95 transition-transform h-full">
                    <div class="bg-green-100 dark:bg-green-900/30 p-2.5 rounded-xl">
                        <TrendingUp :size="22" class="text-green-600 dark:text-green-400" />
                    </div>
                    <span class="font-bold text-xs text-gray-700 dark:text-gray-200 text-center leading-tight">{{ __("Dashboard.History") }}</span>
                </div>
            </Link>
        </div>

        <!-- ─── Impact Écologique ──────────────────────────── -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-base font-bold text-gray-800 dark:text-white">{{ __("Dashboard.My Impact") }}</h2>
                <span class="text-[10px] text-gray-400 font-medium uppercase tracking-widest">{{ __("Dashboard.Cumulative") }}</span>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div
                    v-for="stat in stats"
                    :key="stat.label"
                    class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-4 flex flex-col items-center text-center shadow-sm"
                >
                    <div :class="[stat.bg, 'p-2.5 rounded-xl mb-2']">
                        <component :is="iconMap[stat.icon]" :class="stat.color" :size="20" />
                    </div>
                    <p class="text-sm font-black text-gray-800 dark:text-white leading-tight">{{ stat.value }}</p>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1 leading-tight">{{ stat.label }}</p>
                </div>
            </div>
        </div>

        <!-- ─── Livraisons en cours ────────────────────────── -->
        <div v-if="upcomingDeliveries.length > 0" class="mb-6">
            <h2 class="text-base font-bold text-gray-800 dark:text-white mb-3">{{ __("Dashboard.Ongoing") }}</h2>
            <div class="space-y-3">
                <div
                    v-for="delivery in upcomingDeliveries"
                    :key="delivery.id"
                    class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-4 flex items-center gap-4 shadow-sm"
                >
                    <div class="w-11 h-11 rounded-xl bg-easygas-green/10 flex items-center justify-center flex-shrink-0">
                        <Flame :size="22" class="text-easygas-green" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800 dark:text-white truncate">{{ delivery.type }}</p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5 truncate">{{ __("Dashboard.Driver") }} : {{ delivery.driver }}</p>
                    </div>
                    <span :class="[statusColor[delivery.status] || 'bg-gray-100 text-gray-500', 'text-[10px] font-bold px-2.5 py-1 rounded-full flex-shrink-0']">
                        {{ delivery.status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ─── Espace pour la bottom nav ─────────────────── -->
        <div class="h-24"></div>
    </MobileLayout>
</template>
