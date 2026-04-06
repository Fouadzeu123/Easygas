<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Award, Flame, Info, MapPin, Navigation } from 'lucide-vue-next';
import AppButton from '@/components/AppButton.vue';
import AppCard from '@/components/AppCard.vue';
import AppInput from '@/components/AppInput.vue';
import MobileLayout from '@/layouts/MobileLayout.vue';

const props = defineProps<{
    userPoints: number;
    bottleSizes: Array<{ label: string; quantity: number; price: number }>;
}>();

// Tarifs synchronisés avec OrderController (string keys)
const bottleSizes = computed(() => props.bottleSizes);

const form = useForm({
    quantity:    12.5,
    bottle_type: 'Grande Bouteille',
    latitude:    null as number | null,
    longitude:   null as number | null,
    address:     '',
    notes:       '',
    use_points:  false,
});

const selectBottle = (size: { label: string; quantity: number; price: number }) => {
    form.quantity    = size.quantity;
    form.bottle_type = size.label;
};

const totalPrice = computed(() => {
    const size = bottleSizes.value.find(s => s.quantity === form.quantity);
    return size ? size.price : 0;
});

const discount = computed(() => {
    if (!form.use_points) return 0;
    
    return Math.min(props.userPoints * 10, totalPrice.value);
});

const finalPrice = computed(() => totalPrice.value - discount.value);


const getLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                form.latitude  = position.coords.latitude;
                form.longitude = position.coords.longitude;
                if (!form.address) form.address = 'Position GPS récupérée';
            },
            (err) => { console.warn('Géolocalisation refusée:', err.message); }
        );
    }
};

onMounted(() => { getLocation(); });

const submit = () => {
    if (typeof window !== 'undefined' && (window as any).route) {
        form.post((window as any).route('order.store'));
    }
};
</script>

<template>
    <Head title="Commander du Gaz" />

    <MobileLayout>
        <div class="mb-6 animate-fade-in">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <Flame class="text-easygas-green" /> Commander du Gaz
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Biogaz pur et écologique livré chez vous.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6 pb-24">
            <!-- Sélection de bouteille -->
            <AppCard class="animate-slide-up" style="animation-delay: 0.1s">
                <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Choisir votre bouteille</h2>

                <div class="grid grid-cols-2 gap-3">
                    <button
                        v-for="size in bottleSizes"
                        :key="size.label"
                        type="button"
                        @click="selectBottle(size)"
                        :class="[
                            'p-4 rounded-2xl border-2 transition-all flex flex-col items-center gap-2',
                            form.quantity === size.quantity
                                ? 'border-easygas-green bg-easygas-green/5 text-easygas-green'
                                : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300'
                        ]"
                    >
                        <Flame :size="28" :class="form.quantity === size.quantity ? 'text-easygas-green' : 'text-gray-400 dark:text-gray-500'" />
                        <span class="font-bold text-sm">{{ size.label }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ size.quantity }} kg</span>
                        <span class="text-sm font-black">{{ size.price.toLocaleString() }} FCFA</span>
                    </button>
                </div>
            </AppCard>

            <!-- Localisation -->
            <AppCard class="animate-slide-up" style="animation-delay: 0.2s">
                <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <MapPin class="w-5 h-5 text-red-500" /> Lieu de livraison
                </h2>

                <AppInput
                    id="address"
                    v-model="form.address"
                    label="Adresse ou repères"
                    placeholder="Ex: Rue 12, à côté de la pharmacie"
                    :error="form.errors.address"
                    required
                    class="mb-4"
                />

                <div v-if="form.latitude" class="flex items-center gap-2 text-xs text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20 p-3 rounded-xl border border-green-200 dark:border-green-800">
                    <Navigation class="w-4 h-4 flex-shrink-0" />
                    Position GPS verrouillée ({{ form.latitude.toFixed(4) }}, {{ form.longitude?.toFixed(4) }})
                </div>
                <button v-else type="button" @click="getLocation" class="text-sm text-easygas-green font-semibold flex items-center gap-1.5 hover:underline">
                    <MapPin class="w-4 h-4" />
                    Récupérer ma position GPS
                </button>
            </AppCard>

            <!-- Notes optionnelles -->
            <AppCard class="animate-slide-up" style="animation-delay: 0.25s">
                <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-3">Instructions (optionnel)</h2>
                <textarea
                    v-model="form.notes"
                    placeholder="Ex: Appeler avant d'arriver, code portail..."
                    class="app-input h-20 resize-none text-sm text-gray-800 dark:text-gray-100"
                ></textarea>
            </AppCard>

            <!-- Résumé & Points -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border-2 border-easygas-green shadow-lg animate-slide-up" style="animation-delay: 0.3s">
                <!-- Toggle points -->
                <div v-if="userPoints > 0" class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl">
                            <Award class="w-5 h-5 text-yellow-500" />
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-100">Utiliser mes points</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">{{ userPoints }} pts = {{ (userPoints * 10).toLocaleString() }} FCFA</p>
                        </div>
                    </div>
                    <label class="relative cursor-pointer">
                        <input type="checkbox" v-model="form.use_points" class="sr-only peer" />
                        <div class="w-11 h-6 bg-gray-200 dark:bg-gray-600 rounded-full peer-checked:bg-easygas-green transition-colors"></div>
                        <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all peer-checked:translate-x-5"></span>
                    </label>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Sous-total</span>
                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ totalPrice.toLocaleString() }} FCFA</span>
                    </div>
                    <div v-if="discount > 0" class="flex justify-between text-sm text-green-600 dark:text-green-400">
                        <span>Réduction points</span>
                        <span class="font-medium">- {{ discount.toLocaleString() }} FCFA</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Livraison</span>
                        <span class="text-easygas-green font-bold">GRATUIT</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 pt-4 mt-4 flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-800 dark:text-white">TOTAL</span>
                    <span class="text-2xl font-black text-easygas-green">{{ finalPrice.toLocaleString() }} FCFA</span>
                </div>
            </div>

            <AppButton type="submit" :loading="form.processing" class="animate-slide-up w-full" style="animation-delay: 0.4s">
                Confirmer ma commande
            </AppButton>

            <div class="flex items-start gap-2 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-800">
                <Info class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" />
                <p class="text-xs text-blue-700 dark:text-blue-300">Le paiement s'effectue à la livraison par Cash ou Mobile Money auprès du livreur.</p>
            </div>
        </form>
    </MobileLayout>
</template>
