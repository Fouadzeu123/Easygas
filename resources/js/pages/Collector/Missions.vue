<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import MobileLayout from '@/layouts/MobileLayout.vue';
import AppBadge from '@/components/AppBadge.vue';
import AppButton from '@/components/AppButton.vue';
import AppInput from '@/components/AppInput.vue';
import AppModal from '@/components/AppModal.vue';
import { 
    MapPin, 
    Package, 
    Trash2, 
    CheckCircle2, 
    User as UserIcon, 
    Clock, 
    Navigation,
    Phone,
    ArrowRight,
    Search,
    Filter
} from 'lucide-vue-next';

const props = defineProps<{
    role: string;
    pendingOrders: any[];
    pendingWastes: any[];
    activeOrders: any[];
    activeWastes: any[];
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

const activeTab = ref('active');
const selectedWaste = ref<any>(null);
const showWasteModal = ref(false);
const userCoords = ref<[number, number] | null>(null);

const wasteForm = useForm({
    status: 'collecte',
    quantity_real: 0,
});

// Calculate distance in KM
const getDistance = (lat1: number, lon1: number, lat2: number, lon2: number) => {
    const R = 6371; // km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return (R * c).toFixed(1);
};

const processedPending = computed(() => {
    const all = [
        ...props.pendingOrders.map(o => ({ ...o, itemType: 'order' })),
        ...props.pendingWastes.map(w => ({ ...w, itemType: 'waste' }))
    ];

    if (!userCoords.value) return all;

    return all.map(item => {
        if (item.latitude && item.longitude) {
            item.distance = parseFloat(getDistance(userCoords.value![0], userCoords.value![1], item.latitude, item.longitude));
        } else {
            item.distance = 999;
        }
        return item;
    }).sort((a, b) => (a.distance || 999) - (b.distance || 999));
});

onMounted(() => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((pos) => {
            userCoords.value = [pos.coords.latitude, pos.coords.longitude];
        });
    }
});

const updateOrderStatus = (orderId: number, status: string) => {
    useForm({ status }).patch(route('collector.order.update', orderId));
};

const updateWasteStatus = (wasteId: number, status: string) => {
    useForm({ status }).patch(route('collector.waste.update', wasteId));
};

const openWasteModal = (waste: any) => {
    selectedWaste.value = waste;
    wasteForm.quantity_real = waste.quantity || 0;
    showWasteModal.value = true;
};

const submitWasteUpdate = () => {
    wasteForm.patch(route('collector.waste.update', selectedWaste.value.id), {
        onSuccess: () => {
            showWasteModal.value = false;
        },
    });
};
</script>

<template>
    <Head title="Missions" />

    <MobileLayout>
        <div class="px-6 pt-10 mb-8 flex justify-between items-end">
            <div>
                <p class="text-[10px] font-black text-easygas-green uppercase tracking-[0.2em] mb-1">Centre Opérationnel</p>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Missions</h1>
            </div>
            <div class="flex gap-2">
                <button class="w-10 h-10 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center border border-gray-100 dark:border-gray-700">
                    <Search class="w-5 h-5 text-gray-400" />
                </button>
                <button class="w-10 h-10 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center border border-gray-100 dark:border-gray-700">
                    <Filter class="w-5 h-5 text-gray-400" />
                </button>
            </div>
        </div>

        <!-- Custom Tab Navigation -->
        <div class="mx-6 p-1.5 bg-gray-100 dark:bg-gray-800 rounded-3xl mb-8 flex relative overflow-hidden">
            <button 
                @click="activeTab = 'active'"
                :class="[
                    'flex-1 py-3 text-xs font-black uppercase tracking-widest rounded-2xl transition-all duration-300 z-10',
                    activeTab === 'active' ? 'bg-white dark:bg-gray-700 text-easygas-green shadow-xl shadow-black/5' : 'text-gray-400'
                ]"
            >
                Mes Tâches ({{ activeOrders.length + activeWastes.length }})
            </button>
            <button 
                @click="activeTab = 'pending'"
                :class="[
                    'flex-1 py-3 text-xs font-black uppercase tracking-widest rounded-2xl transition-all duration-300 z-10',
                    activeTab === 'pending' ? 'bg-white dark:bg-gray-700 text-easygas-green shadow-xl shadow-black/5' : 'text-gray-400'
                ]"
            >
                Disponibles ({{ processedPending.length }})
            </button>
        </div>

        <div class="px-6 pb-32 space-y-6">
            
            <!-- Missions En Cours -->
            <template v-if="activeTab === 'active'">
                <!-- Orders -->
                <div v-for="item in activeOrders" :key="'order-'+item.id" class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-2xl shadow-black/5 border border-gray-100 dark:border-gray-700 overflow-hidden relative group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-orange-500/5 rounded-bl-full group-hover:scale-110 transition-transform"></div>
                    
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/20 rounded-2xl flex items-center justify-center">
                                <Package class="w-6 h-6 text-orange-500" />
                            </div>
                            <div>
                                <h3 class="font-black text-gray-900 dark:text-white uppercase tracking-tight">Livraison Gaz</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">#{{ item.id }} • {{ item.quantity }}kg</p>
                            </div>
                        </div>
                        <AppBadge variant="warning">En trajet</AppBadge>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-white dark:bg-gray-700 flex items-center justify-center border border-gray-100 dark:border-gray-600">
                                    <UserIcon class="w-4 h-4 text-gray-400" />
                                </div>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ item.user?.name || 'Client Inconnu' }}</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center shrink-0">
                                    <MapPin class="w-4 h-4 text-red-500" />
                                </div>
                                <span class="text-xs text-gray-500 font-medium leading-relaxed">{{ item.address || 'Localisation requise' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <Link 
                            v-if="item.latitude" 
                            :href="route('collector.map', { lat: item.latitude, lng: item.longitude, address: item.address })"
                            class="flex items-center justify-center gap-2 py-4 text-[10px] font-black uppercase tracking-widest text-easygas-green bg-easygas-green/5 rounded-2xl border border-easygas-green/20 hover:bg-easygas-green/10 transition-all"
                        >
                            <Navigation class="w-4 h-4" /> Itinéraire
                        </Link>
                        <a :href="'tel:' + (item.user?.phone || '')" class="flex items-center justify-center gap-2 py-4 text-[10px] font-black uppercase tracking-widest text-blue-500 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-800/30">
                            <Phone class="w-4 h-4" /> Appeler
                        </a>
                        <AppButton @click="updateOrderStatus(item.id, 'livre')" variant="primary" class="col-span-2 py-4 rounded-2xl shadow-lg shadow-easygas-green/20">
                            <CheckCircle2 class="w-5 h-5 mr-2" /> Marquer comme livré
                        </AppButton>
                    </div>
                </div>

                <!-- Wastes -->
                <div v-for="item in activeWastes" :key="'waste-'+item.id" class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-2xl shadow-black/5 border border-gray-100 dark:border-gray-700 overflow-hidden relative group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-green-500/5 rounded-bl-full group-hover:scale-110 transition-transform"></div>
                    
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-2xl flex items-center justify-center">
                                <Trash2 class="w-6 h-6 text-green-500" />
                            </div>
                            <div>
                                <h3 class="font-black text-gray-900 dark:text-white uppercase tracking-tight">Collecte Déchets</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">#{{ item.id }} • {{ item.type }}</p>
                            </div>
                        </div>
                        <AppBadge variant="warning">Assigné</AppBadge>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl space-y-3">
                             <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-white dark:bg-gray-700 flex items-center justify-center border border-gray-100 dark:border-gray-600">
                                    <UserIcon class="w-4 h-4 text-gray-400" />
                                </div>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ item.user?.name || 'Client Inconnu' }}</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center shrink-0">
                                    <MapPin class="w-4 h-4 text-red-500" />
                                </div>
                                <span class="text-xs text-gray-500 font-medium leading-relaxed">{{ item.address || 'Localisation requise' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <Link 
                            v-if="item.latitude" 
                            :href="route('collector.map', { lat: item.latitude, lng: item.longitude, address: item.address })"
                            class="flex items-center justify-center gap-2 py-4 text-[10px] font-black uppercase tracking-widest text-easygas-green bg-easygas-green/5 rounded-2xl border border-easygas-green/20 hover:bg-easygas-green/10 transition-all"
                        >
                            <Navigation class="w-4 h-4" /> Itinéraire
                        </Link>
                        <a :href="'tel:' + (item.user?.phone || '')" class="flex items-center justify-center gap-2 py-4 text-[10px] font-black uppercase tracking-widest text-blue-500 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-800/30">
                            <Phone class="w-4 h-4" /> Appeler
                        </a>
                        <AppButton @click="openWasteModal(item)" variant="secondary" class="col-span-2 py-4 rounded-2xl shadow-lg shadow-green-500/20">
                            <CheckCircle2 class="w-5 h-5 mr-2" /> Terminer & Peser
                        </AppButton>
                    </div>
                </div>

                <div v-if="activeOrders.length === 0 && activeWastes.length === 0" class="text-center py-20 bg-gray-50 dark:bg-gray-800/30 rounded-[3rem] border-2 border-dashed border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl shadow-black/5">
                        <Clock class="w-10 h-10 text-gray-200 animate-pulse" />
                    </div>
                    <h3 class="font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2">Aucune mission</h3>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Allez dans "Disponibles" pour en trouver</p>
                </div>
            </template>

            <!-- Missions Disponibles -->
            <template v-if="activeTab === 'pending'">
                <div v-for="item in processedPending" :key="item.itemType+'-'+item.id" class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-xl shadow-black/5 border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                    <div v-if="item.distance && item.distance < 999" class="absolute top-0 right-0 bg-easygas-green text-white px-5 py-2 rounded-bl-[1.5rem] text-[10px] font-black flex items-center gap-1 shadow-lg shadow-easygas-green/20 z-10">
                        <Navigation class="w-3 h-3" /> {{ item.distance }} km
                    </div>

                    <div class="flex items-center gap-4 mb-6">
                         <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg', item.itemType === 'order' ? 'bg-orange-50 dark:bg-orange-900/20' : 'bg-green-50 dark:bg-green-900/20']">
                            <Package v-if="item.itemType === 'order'" class="w-7 h-7 text-orange-500" />
                            <Trash2 v-else class="w-7 h-7 text-green-500" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Mission #{{ item.id }}</p>
                            <h3 class="font-black text-lg text-gray-900 dark:text-white leading-none">
                                {{ item.itemType === 'order' ? `${item.quantity}kg Gaz` : `Déchets ${item.type}` }}
                            </h3>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-2 text-xs text-gray-500 mb-6 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-2xl">
                        <MapPin class="w-4 h-4 text-red-400 shrink-0 mt-0.5" />
                        <span class="font-medium line-clamp-2">{{ item.address || 'Localisation client' }}</span>
                    </div>
                    
                    <AppButton 
                        v-if="item.itemType === 'order'" 
                        @click="updateOrderStatus(item.id, 'en_livraison')" 
                        variant="primary" 
                        class="w-full py-4 rounded-2xl shadow-xl shadow-orange-500/10 group-hover:scale-[1.02] transition-transform"
                    >
                        Accepter la mission <ArrowRight class="w-4 h-4 ml-2" />
                    </AppButton>
                    <AppButton 
                        v-else 
                        @click="updateWasteStatus(item.id, 'assigne')" 
                        variant="secondary" 
                        class="w-full py-4 rounded-2xl shadow-xl shadow-green-500/10 group-hover:scale-[1.02] transition-transform"
                    >
                        Accepter la mission <ArrowRight class="w-4 h-4 ml-2" />
                    </AppButton>
                </div>
                
                <div v-if="processedPending.length === 0" class="text-center py-20 bg-gray-50 dark:bg-gray-800/30 rounded-[3rem] border-2 border-dashed border-gray-100 dark:border-gray-800">
                     <div class="w-20 h-20 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl shadow-black/5">
                        <Search class="w-10 h-10 text-gray-200 animate-pulse" />
                    </div>
                    <h3 class="font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2">Recherche en cours...</h3>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Aucune mission disponible à proximité</p>
                </div>
            </template>

        </div>

        <AppModal v-if="selectedWaste" v-model="showWasteModal" title="Validation Collecte">
            <template #default>
                <div class="space-y-6">
                    <div class="bg-gray-50 dark:bg-gray-800 p-5 rounded-3xl border border-gray-100 dark:border-gray-700">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Client</p>
                        <p class="font-black text-lg text-gray-900 dark:text-white">{{ selectedWaste.user?.name }}</p>
                        <p class="text-[10px] font-bold text-easygas-green uppercase bg-easygas-green/10 px-2 py-0.5 rounded-full inline-block mt-2">Déchets: {{ selectedWaste.type }}</p>
                    </div>

                    <form @submit.prevent="submitWasteUpdate" class="space-y-6">
                        <AppInput 
                            id="quantity_real"
                            v-model="wasteForm.quantity_real"
                            type="number"
                            step="0.1"
                            label="Poids réel pesé (kg)"
                        />
                        <div class="flex gap-3 mt-8">
                            <AppButton type="button" variant="ghost" @click="showWasteModal = false" class="flex-1 rounded-2xl py-4">Annuler</AppButton>
                            <AppButton type="submit" variant="primary" class="flex-1 rounded-2xl py-4" :loading="wasteForm.processing">Valider</AppButton>
                        </div>
                    </form>
                </div>
            </template>
        </AppModal>
    </MobileLayout>
</template>

<style scoped>
.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
}
</style>
