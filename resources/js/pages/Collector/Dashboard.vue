<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import MobileLayout from '@/layouts/MobileLayout.vue';
import { 
    Package, 
    TrendingUp, 
    CheckCircle2, 
    MapPin, 
    Power, 
    Navigation, 
    History,
    Wallet,
    Award
} from 'lucide-vue-next';

const props = defineProps<{
    role: string;
    stats: any;
    auth: {
        user: {
            id: number;
            name: string;
            is_available: boolean;
            points: number;
        }
    }
}>();

// Global route helper for template and script
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

const isOnline = ref(props.auth.user.is_available);
const statusForm = useForm({
    is_available: false
});

const toggleStatus = () => {
    isOnline.value = !isOnline.value;
    statusForm.is_available = isOnline.value;
    statusForm.patch(route('collector.availability.update'), {
        preserveScroll: true
    });
};

// Simulation of current position update
const updatePosition = () => {
    if (navigator.geolocation && isOnline.value) {
        navigator.geolocation.getCurrentPosition((pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            
            // Silently update background location
            fetch(route('collector.location.update'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as any)?.content
                },
                body: JSON.stringify({ lat, lng })
            });
        });
    }
};

onMounted(() => {
    if (isOnline.value) {
        updatePosition();
    }
    // Update every 2 minutes if online
    setInterval(() => {
        if (isOnline.value) updatePosition();
    }, 120000);
});
</script>

<template>
    <Head :title="role === 'ramasseur' ? 'Dashboard Ramasseur' : 'Dashboard Livreur'" />

    <MobileLayout>
        <!-- Header with Status Toggle -->
        <div class="relative px-6 pt-10 pb-20 bg-easygas-green rounded-b-[3.5rem] shadow-xl overflow-hidden -mx-6 mb-8">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
            
            <div class="flex justify-between items-start relative z-10">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/30 shadow-lg">
                        <img src="/img/logo-easygas.png" alt="EasyGas" class="w-10 h-10 object-contain invert grayscale" />
                    </div>
                    <div>
                        <p class="text-xs font-black text-white/50 uppercase tracking-widest leading-none mb-1">PRO SERVICE</p>
                        <h1 class="text-2xl font-black text-white leading-tight">Bonjour, {{ auth.user.name.split(' ')[0] }}</h1>
                    </div>
                </div>
                
                <button 
                    @click="toggleStatus"
                    :class="[
                        'p-3 rounded-2xl transition-all duration-500 shadow-lg border relative',
                        isOnline ? 'bg-white text-easygas-green border-white animate-pulse-slow' : 'bg-gray-900/40 text-white/50 border-white/10'
                    ]"
                >
                    <Power class="w-6 h-6" />
                    <span v-if="isOnline" class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full border-2 border-white"></span>
                </button>
            </div>

            <!-- Dashboard Stats Overlay Card -->
            <div class="absolute -bottom-6 left-6 right-6 bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-2xl shadow-black/10 flex justify-between items-center border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/20 rounded-2xl flex items-center justify-center">
                        <Wallet class="w-6 h-6 text-orange-500" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Gains du jour</p>
                        <p class="text-xl font-black text-gray-900 dark:text-white">{{ stats.gains?.toLocaleString() || 0 }} <span class="text-xs font-medium opacity-50">CFA</span></p>
                    </div>
                </div>
                <div class="w-px h-10 bg-gray-100 dark:bg-gray-700"></div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center">
                        <Award class="w-6 h-6 text-blue-500" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Points Fidélité</p>
                        <p class="text-xl font-black text-gray-900 dark:text-white">{{ auth.user.points || 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 pb-32 space-y-8">
            <div v-if="!isOnline" class="bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/30 p-5 rounded-3xl flex items-center gap-4">
                <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center shrink-0">
                    <Power class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="font-bold text-amber-900 dark:text-amber-200">Vous êtes hors ligne</h3>
                    <p class="text-xs text-amber-700 dark:text-amber-400">Passez en ligne pour recevoir de nouvelles missions à proximité.</p>
                </div>
            </div>

            <!-- Summary section -->
            <section class="space-y-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Statistiques globales</h2>
                    <span class="text-xs font-bold text-easygas-green bg-easygas-green/10 px-3 py-1 rounded-full flex items-center gap-1">
                        <TrendingUp class="w-3 h-3" /> +12% vs hier
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-800/50 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col gap-4">
                        <div class="w-10 h-10 bg-white dark:bg-gray-700 rounded-xl shadow-sm flex items-center justify-center">
                            <CheckCircle2 class="w-5 h-5 text-easygas-green" />
                        </div>
                        <div>
                            <p class="text-2xl font-black text-gray-900 dark:text-white">{{ role === 'ramasseur' ? stats.total_collectes : stats.total_livraisons }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ role === 'ramasseur' ? 'Collectes Terminées' : 'Livraisons Faites' }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-800/50 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col gap-4">
                        <div class="w-10 h-10 bg-white dark:bg-gray-700 rounded-xl shadow-sm flex items-center justify-center">
                            <Package class="w-5 h-5 text-blue-500" />
                        </div>
                        <div>
                            <p v-if="role === 'ramasseur'" class="text-2xl font-black text-gray-900 dark:text-white">{{ stats.total_kg || 0 }} <span class="text-xs font-bold">KG</span></p>
                            <p v-else class="text-2xl font-black text-gray-900 dark:text-white">4.8 <span class="text-xs font-bold">/ 5</span></p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ role === 'ramasseur' ? 'Volume Collecté' : 'Note Moyenne' }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick Actions -->
            <section class="space-y-4">
                <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Accès Rapide</h2>
                <div class="grid grid-cols-3 gap-4">
                    <Link :href="route('collector.missions')" class="flex flex-col items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:translate-y-[-2px] transition-all">
                        <div class="w-12 h-12 bg-easygas-green/10 rounded-2xl flex items-center justify-center">
                            <Navigation class="w-6 h-6 text-easygas-green" />
                        </div>
                        <span class="text-[10px] font-black uppercase text-gray-500 text-center tracking-tighter">Missions</span>
                    </Link>
                    
                    <Link :href="route('collector.map')" class="flex flex-col items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:translate-y-[-2px] transition-all">
                        <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center">
                            <MapPin class="w-6 h-6 text-blue-500" />
                        </div>
                        <span class="text-[10px] font-black uppercase text-gray-500 text-center tracking-tighter">Carte</span>
                    </Link>
                    
                    <Link :href="route('activity')" class="flex flex-col items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:translate-y-[-2px] transition-all">
                        <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/20 rounded-2xl flex items-center justify-center">
                            <History class="w-6 h-6 text-orange-500" />
                        </div>
                        <span class="text-[10px] font-black uppercase text-gray-500 text-center tracking-tighter">Historique</span>
                    </Link>
                </div>
            </section>

            <!-- Bottom Note -->
            <div class="text-center">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">EasyGas S.A. &copy; 2026 - Version 2.0</p>
            </div>
        </div>
    </MobileLayout>
</template>

<style scoped>
.animate-pulse-slow {
    animation: shadowPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes shadowPulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(46, 125, 50, 0.4);
    }
    50% {
        box-shadow: 0 0 0 10px rgba(46, 125, 50, 0);
    }
}
</style>
