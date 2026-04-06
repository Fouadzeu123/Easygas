<script setup lang="ts">
import { onMounted, onUnmounted, computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import MobileLayout from '@/layouts/MobileLayout.vue';
import AppMap from '@/components/AppMap.vue';
import { useGeolocation } from '@/composables/useGeolocation';
import { MapPin, Navigation, ArrowLeft, Phone } from 'lucide-vue-next';

const props = defineProps<{
    role: string;
    target?: {
        lat: string;
        lng: string;
        address: string;
    };
}>();

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

const { coords, startWatching, stopWatching, updateCoords, loading, error: geoError } = useGeolocation();

const markers = computed(() => {
    const list: Array<{ position: [number, number]; label: string; icon: 'red' | 'blue' | 'green' | 'orange' | 'default' }> = [];
    if (coords.value.latitude !== null && coords.value.longitude !== null) {
        list.push({
            position: [coords.value.latitude!, coords.value.longitude!] as [number, number],
            label: 'Ma position (Moi)',
            icon: 'blue'
        });
    }
    if (props.target?.lat) {
        list.push({
            position: [parseFloat(props.target.lat), parseFloat(props.target.lng)] as [number, number],
            label: props.target.address || 'Destination Client',
            icon: 'red'
        });
    }
    return list;
});

const itinerary = computed(() => {
    if (coords.value.latitude !== null && props.target?.lat) {
        return {
            start: [coords.value.latitude, coords.value.longitude] as [number, number],
            end: [parseFloat(props.target.lat), parseFloat(props.target.lng)] as [number, number]
        };
    }
    return undefined;
});

onMounted(() => {
    startWatching();
});

onUnmounted(() => {
    stopWatching();
});
</script>

<template>
    <Head title="Navigation Temps Réel" />

    <MobileLayout>
        <!-- Full Screen Map View -->
        <div class="h-[92vh] w-full relative -mt-5 -mx-5 mb-[-2rem]">
            
            <!-- Map Component -->
            <AppMap 
                :markers="markers" 
                :itinerary="itinerary"
                :center="coords.latitude !== null && coords.longitude !== null ? [coords.latitude, coords.longitude] : [4.0511, 9.7679]"
                :zoom="itinerary ? 17 : 14"
                height="100%"
            />

            <!-- Loading Overlay -->
            <div v-if="loading && !coords.latitude" class="absolute inset-0 bg-white/50 dark:bg-black/50 backdrop-blur-sm z-[60] flex flex-col items-center justify-center gap-4">
                <div class="w-12 h-12 border-4 border-easygas-green border-t-transparent rounded-full animate-spin"></div>
                <p class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest">Recherche GPS...</p>
            </div>

            <!-- Error Alert -->
            <div v-if="geoError" class="absolute top-24 left-4 right-4 z-[70] bg-red-500 text-white p-4 rounded-2xl shadow-2xl animate-shake flex items-center gap-3">
                <div class="shrink-0 w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                    <MapPin class="w-4 h-4" />
                </div>
                <p class="text-xs font-black uppercase tracking-tight">{{ geoError }}</p>
            </div>

            <!-- Floating Top Card -->
            <div class="absolute top-8 left-4 right-4 z-50">
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl p-4 shadow-2xl border border-white/20 dark:border-gray-700/30 flex items-center gap-4">
                    <Link :href="route('collector.missions')" class="w-10 h-10 bg-white dark:bg-gray-700 rounded-2xl flex items-center justify-center shadow-lg hover:scale-105 transition-transform">
                        <ArrowLeft class="w-5 h-5 text-gray-900 dark:text-white" />
                    </Link>
                    <div class="flex-1 overflow-hidden">
                        <h2 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight truncate">Navigation</h2>
                        <p class="text-[10px] font-bold text-easygas-green uppercase tracking-[0.15em] flex items-center gap-1">
                            <Navigation class="w-3 h-3" /> {{ target ? 'Vers destination client' : 'Exploration libre' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Floating Bottom Card if Target exists -->
            <div v-if="target" class="absolute bottom-10 left-4 right-4 z-50 animate-slide-up">
                <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-6 shadow-2xl border border-gray-100 dark:border-gray-800">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 bg-red-50 dark:bg-red-900/20 rounded-2xl flex items-center justify-center shrink-0">
                                <MapPin class="w-6 h-6 text-red-500" />
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Destination client</p>
                                <h3 class="font-black text-lg text-gray-900 dark:text-white truncate">{{ target.address }}</h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 pt-2">
                         <a :href="'https://www.google.com/maps/dir/?api=1&destination=' + target.lat + ',' + target.lng" target="_blank" class="w-full py-4 rounded-2xl flex items-center justify-center gap-2 text-[10px] font-black uppercase tracking-widest bg-easygas-green text-white shadow-xl shadow-easygas-green/20">
                            <Navigation class="w-4 h-4" /> Google Maps Ext.
                        </a>
                        <Link :href="route('collector.missions')" class="w-full py-4 rounded-2xl flex items-center justify-center gap-2 text-[10px] font-black uppercase tracking-widest bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-100 dark:border-gray-700">
                            Fermer
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Recenter Button -->
            <button v-else @click="updateCoords" class="absolute bottom-10 right-6 w-14 h-14 bg-white dark:bg-gray-800 rounded-full shadow-2xl flex items-center justify-center border border-gray-100 dark:border-gray-700 z-50">
                <Navigation class="w-6 h-6 text-easygas-green" />
            </button>
        </div>
    </MobileLayout>
</template>

<style scoped>
@keyframes slide-up {
    from { transform: translateY(100px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.animate-slide-up {
    animation: slide-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Hide navigation components in full screen map */
:deep(nav) {
    display: none !important;
}
</style>
